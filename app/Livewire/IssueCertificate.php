<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student\Student;
use App\Models\Student\Certificate;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

class IssueCertificate extends Component
{
    public $student_search = '';
    public $suggested_students = [];
    public $student_id = '';
    public $selected_student = null;

    public $certificate_type = 'character';
    public $custom_title = '';
    public $body_text = '';
    public $issued_date;

    public $certificate_types = [
        'character' => 'Character Certificate',
        'bonafide'  => 'Bonafide Certificate',
        'leaving'   => 'School Leaving Certificate',
        'custom'    => 'Custom Certificate',
    ];

    public function mount()
    {
        $this->issued_date = date('Y-m-d');
    }

    public function updatedStudentSearch()
    {
        if (strlen($this->student_search) < 2) {
            $this->suggested_students = [];
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $this->suggested_students = Student::where('tenant_id', $tenantId)
            ->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->student_search . '%')
                  ->orWhere('admission_no', 'like', '%' . $this->student_search . '%')
                  ->orWhere('father_name', 'like', '%' . $this->student_search . '%');
            })
            ->limit(8)
            ->get(['id', 'first_name', 'last_name', 'admission_no', 'father_name'])
            ->toArray();
    }

    public function selectStudent($id)
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $this->selected_student = Student::where('tenant_id', $tenantId)
            ->with(['schoolClass', 'session', 'campus'])
            ->find($id);
        $this->student_id = $id;
        $this->suggested_students = [];
        $this->student_search = trim(($this->selected_student->first_name ?? '') . ' ' . ($this->selected_student->last_name ?? ''));
        $this->generateTemplate();
    }

    public function updatedCertificateType()
    {
        $this->generateTemplate();
    }

    protected function generateTemplate()
    {
        if (!$this->selected_student || $this->certificate_type === 'custom') {
            if ($this->certificate_type === 'custom') $this->body_text = '';
            return;
        }

        $s = $this->selected_student;
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $schoolName = Tenant::find($tenantId)?->name ?? 'this institution';

        $name = trim($s->first_name . ' ' . $s->last_name);
        $father = $s->father_name;
        $admissionNo = $s->admission_no;
        $class = $s->schoolClass->name ?? '—';
        $session = $s->session->name ?? '—';
        $pronoun = $s->gender === 'Female' ? 'She' : 'He';
        $possessive = $s->gender === 'Female' ? 'her' : 'his';
        $relation = $s->gender === 'Female' ? 'D/o' : 'S/o';

        $templates = [
            'character' => "This is to certify that {$name}, {$relation} {$father}, Admission No. {$admissionNo}, was/is a student of {$class} at {$schoolName} during the session {$session}. During this period, {$pronoun}'s conduct and character remained satisfactory. This certificate is issued on {$possessive} request for the purpose it may serve.",
            'bonafide'  => "This is to certify that {$name}, {$relation} {$father}, Admission No. {$admissionNo}, is a bonafide student of {$schoolName}, currently studying in {$class} during the session {$session}. This certificate is issued for whatever purpose it may serve.",
            'leaving'   => "This is to certify that {$name}, {$relation} {$father}, Admission No. {$admissionNo}, was a student of {$schoolName} in {$class} and has left the institution. During {$possessive} stay, conduct remained satisfactory. No dues are outstanding against {$possessive} name unless otherwise noted.",
        ];

        $this->body_text = $templates[$this->certificate_type] ?? '';
    }

    public function issue()
    {
        $this->validate([
            'student_id'       => 'required|exists:students,id',
            'certificate_type' => 'required|in:character,bonafide,leaving,custom',
            'custom_title'     => $this->certificate_type === 'custom' ? 'required|string|max:150' : 'nullable|string|max:150',
            'body_text'        => 'required|string',
            'issued_date'      => 'required|date',
        ]);

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $title = $this->certificate_type === 'custom' ? $this->custom_title : $this->certificate_types[$this->certificate_type];

        $certificate = Certificate::create([
            'tenant_id'        => $tenantId,
            'student_id'       => $this->student_id,
            'certificate_type' => $this->certificate_type,
            'title'            => $title,
            'body_text'        => $this->body_text,
            'issued_date'      => $this->issued_date,
            'issued_by'        => Auth::id(),
        ]);

        return redirect()->route('print-certificate', ['id' => $certificate->id]);
    }

    public function render()
    {
        return view('livewire.issue-certificate')->layout('layouts.app');
    }
}
