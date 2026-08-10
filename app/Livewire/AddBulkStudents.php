<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Imports\StudentsImport;
use App\Models\Student\Student;
use App\Models\Campus\Campus;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Section;
use App\Models\Academic\Session;
use App\Models\Tenant as TenantModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AddBulkStudents extends Component
{
    use WithFileUploads;

    public $file;

    // Parsed + validated rows, ready for review before commit.
    // Each: ['row' => n, 'data' => [...resolved fields...], 'errors' => [...], 'valid' => bool]
    public $preview_rows = [];
    public $unrecognized_headers = [];

    // Result after commit
    public $imported_count = 0;
    public $failed_rows = [];
    public $showing_results = false;

    // Header label => internal field key. Matched case-insensitively, trimmed.
    protected $columnMap = [
        'first name'    => 'first_name',
        'last name'     => 'last_name',
        'father name'   => 'father_name',
        'father cnic'   => 'father_cnic',
        'father phone'  => 'father_phone',
        'cnic'          => 'cnic_no',
        'student cnic'  => 'cnic_no',
        'gender'        => 'gender',
        'date of birth' => 'date_of_birth',
        'religion'      => 'religion',
        'nationality'   => 'nationality',
        'campus'        => 'campus',
        'class'         => 'class',
        'section'       => 'section',
        'address'       => 'address',
        'city'          => 'city',
    ];

    public function downloadTemplate()
    {
        $headers = ['First Name', 'Last Name', 'Father Name', 'Father CNIC', 'Father Phone', 'CNIC', 'Gender', 'Date of Birth', 'Religion', 'Nationality', 'Campus', 'Class', 'Section', 'Address', 'City'];
        $example = ['Ali', 'Khan', 'Muhammad Khan', '3520112345671', '03001234567', '3520112345672', 'Male', '2015-04-12', 'Islam', 'Pakistani', 'Main Campus', 'Grade 5', 'A', 'House 12, Street 4', 'Lahore'];

        return response()->streamDownload(function () use ($headers, $example) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);
            fputcsv($out, $example);
            fclose($out);
        }, 'student_bulk_import_template.csv');
    }

    public function updatedFile()
    {
        $this->preview_rows = [];
        $this->unrecognized_headers = [];
        $this->showing_results = false;

        $this->validate(['file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120']);

        $rows = Excel::toArray(new StudentsImport, $this->file);
        $sheet = $rows[0] ?? [];

        if (empty($sheet)) {
            $this->addError('file', 'The uploaded file appears to be empty.');
            return;
        }

        $headerRow = array_shift($sheet);
        $headerIndex = [];
        foreach ($headerRow as $i => $label) {
            $key = $this->columnMap[strtolower(trim((string) $label))] ?? null;
            if ($key) {
                $headerIndex[$key] = $i;
            } elseif (trim((string) $label) !== '') {
                $this->unrecognized_headers[] = trim((string) $label);
            }
        }

        if (!isset($headerIndex['first_name']) || !isset($headerIndex['father_name']) || !isset($headerIndex['campus']) || !isset($headerIndex['class'])) {
            $this->addError('file', 'The file must include at minimum: First Name, Father Name, Campus, and Class columns.');
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $campusCache  = Campus::where('tenant_id', $tenantId)->get()->keyBy(fn ($c) => strtolower(trim($c->name)));
        $classCache   = SchoolClass::where('tenant_id', $tenantId)->get()->keyBy(fn ($c) => strtolower(trim($c->name)));
        $sectionCache = Section::where('tenant_id', $tenantId)->get();

        $rowNum = 1; // header was row 1
        foreach ($sheet as $raw) {
            $rowNum++;
            if (empty(array_filter($raw, fn ($v) => trim((string) $v) !== ''))) {
                continue; // skip fully blank rows
            }

            $get = fn ($key) => isset($headerIndex[$key]) ? trim((string) ($raw[$headerIndex[$key]] ?? '')) : '';

            $data = [
                'first_name'    => $get('first_name'),
                'last_name'     => $get('last_name'),
                'father_name'   => $get('father_name'),
                'father_cnic'   => $get('father_cnic') ?: null,
                'father_phone'  => $get('father_phone') ?: null,
                'cnic_no'       => $get('cnic_no') ?: null,
                'gender'        => in_array($get('gender'), ['Male', 'Female'], true) ? $get('gender') : ($get('gender') ?: 'Male'),
                'date_of_birth' => $get('date_of_birth'),
                'religion'      => $get('religion') ?: null,
                'nationality'   => $get('nationality') ?: null,
                'campus_name'   => $get('campus'),
                'class_name'    => $get('class'),
                'section_name'  => $get('section'),
                'address'       => $get('address') ?: null,
                'city'          => $get('city') ?: null,
            ];

            $errors = [];

            if ($data['first_name'] === '') $errors[] = 'First Name is required';
            if ($data['father_name'] === '') $errors[] = 'Father Name is required';

            $campus = $campusCache->get(strtolower($data['campus_name']));
            if (!$campus) { $errors[] = "Campus '{$data['campus_name']}' not found"; }

            $class = $classCache->get(strtolower($data['class_name']));
            if (!$class) { $errors[] = "Class '{$data['class_name']}' not found"; }

            $section = null;
            if ($data['section_name'] !== '' && $class) {
                $section = $sectionCache->first(fn ($s) => $s->school_class_id === $class->id && strtolower(trim($s->name)) === strtolower($data['section_name']));
                if (!$section) { $errors[] = "Section '{$data['section_name']}' not found in class '{$data['class_name']}'"; }
            }

            if ($data['date_of_birth'] !== '') {
                try {
                    $data['date_of_birth'] = \Carbon\Carbon::parse($data['date_of_birth'])->format('Y-m-d');
                } catch (\Exception) {
                    $errors[] = "Date of Birth '{$data['date_of_birth']}' could not be parsed";
                    $data['date_of_birth'] = null;
                }
            } else {
                $data['date_of_birth'] = null;
            }

            $data['campus_id']       = $campus?->id;
            $data['school_class_id'] = $class?->id;
            $data['section_id']      = $section?->id;

            $this->preview_rows[] = [
                'row'    => $rowNum,
                'data'   => $data,
                'errors' => $errors,
                'valid'  => empty($errors),
            ];
        }
    }

    public function removeRow($index)
    {
        unset($this->preview_rows[$index]);
        $this->preview_rows = array_values($this->preview_rows);
    }

    public function confirmImport()
    {
        $validRows = array_filter($this->preview_rows, fn ($r) => $r['valid']);
        if (empty($validRows)) {
            session()->flash('error', 'No valid rows to import.');
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $activeSession = Session::where('tenant_id', $tenantId)->where('is_active', true)->first();

        $imported = 0;
        $failed = [];

        DB::transaction(function () use ($validRows, $tenantId, $activeSession, &$imported, &$failed) {
            $tenant = TenantModel::find($tenantId);
            $systemNumber = $tenant->next_student_number ?? 1;
            $systemPrefix = $tenant->student_prefix ?? 'STD-';
            $admissionSeq = (int) (Student::max('id') ?? 0);

            foreach ($validRows as $row) {
                $data = $row['data'];

                $created = null;
                for ($attempt = 0; $attempt < 5; $attempt++) {
                    $admissionSeq++;
                    $admissionNo = 'ADM-' . date('Y') . '-' . str_pad($admissionSeq, 4, '0', STR_PAD_LEFT);

                    try {
                        $created = Student::create([
                            'tenant_id'       => $tenantId,
                            'session_id'      => $activeSession?->id,
                            'campus_id'       => $data['campus_id'],
                            'school_class_id' => $data['school_class_id'],
                            'section_id'      => $data['section_id'],
                            'system_id'       => $systemPrefix . $systemNumber,
                            'admission_no'    => $admissionNo,
                            'first_name'      => $data['first_name'],
                            'last_name'       => $data['last_name'],
                            'father_name'     => $data['father_name'],
                            'father_cnic'     => $data['father_cnic'],
                            'father_phone'    => $data['father_phone'],
                            'cnic_no'         => $data['cnic_no'],
                            'gender'          => $data['gender'],
                            'date_of_birth'   => $data['date_of_birth'],
                            'religion'        => $data['religion'],
                            'nationality'     => $data['nationality'],
                            'address'         => $data['address'],
                            'city'            => $data['city'],
                            'admission_date'  => date('Y-m-d'),
                            'is_active'       => true,
                        ]);
                        break;
                    } catch (\Illuminate\Database\QueryException $e) {
                        // admission_no (or system_id) collided with a row created
                        // concurrently elsewhere (e.g. the single New Admission
                        // form) -- retry with the next number instead of failing
                        // this row outright.
                        if ($attempt === 4) {
                            $failed[] = "Row {$row['row']}: " . $e->getMessage();
                        }
                    }
                }

                if ($created) {
                    $systemNumber++;
                    $imported++;
                }
            }

            $tenant->update(['next_student_number' => $systemNumber]);
        });

        $this->imported_count = $imported;
        $this->failed_rows = $failed;
        $this->showing_results = true;
        $this->preview_rows = [];
        $this->file = null;
    }

    public function startOver()
    {
        $this->reset(['file', 'preview_rows', 'unrecognized_headers', 'imported_count', 'failed_rows', 'showing_results']);
    }

    public function render()
    {
        return view('livewire.add-bulk-students')->layout('layouts.app');
    }
}
