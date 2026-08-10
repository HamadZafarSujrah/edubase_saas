<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Student\Student;
use App\Models\Campus\Campus;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Section;
use Illuminate\Support\Facades\Auth;
use Endroid\QrCode\Builder\Builder;

class StudentQRCode extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $campus_id = '';
    public $school_class_id = '';
    public $section_id = '';
    public $status_filter = 'active';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingCampusId() { $this->resetPage(); $this->school_class_id = ''; $this->section_id = ''; }
    public function updatingSchoolClassId() { $this->resetPage(); $this->section_id = ''; }
    public function updatingStatusFilter() { $this->resetPage(); }

    /**
     * QR payload is just the admission_no -- a compact, already-unique,
     * human-verifiable identifier that any future QR Attendance scanner can
     * look up directly (Student::where('admission_no', $scanned)->first()),
     * without needing a separate encoding/decoding scheme.
     */
    protected function generateQr(string $data, int $size = 180): string
    {
        return Builder::create()
            ->data($data)
            ->size($size)
            ->margin(4)
            ->build()
            ->getDataUri();
    }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $query = Student::with(['campus', 'schoolClass', 'section', 'media'])
            ->where('tenant_id', $tenantId);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('admission_no', 'like', '%' . $this->search . '%');
            });
        }
        if ($this->campus_id) $query->where('campus_id', $this->campus_id);
        if ($this->school_class_id) $query->where('school_class_id', $this->school_class_id);
        if ($this->section_id) $query->where('section_id', $this->section_id);
        if ($this->status_filter !== 'all') $query->where('is_active', $this->status_filter === 'active');

        $students = $query->orderBy('first_name')->paginate(12);

        // QR codes are only generated for the current page of students, not
        // the whole matching set -- rendering is real work (PNG encoding),
        // and a filtered directory can easily match hundreds of students.
        $qrCodes = [];
        foreach ($students as $s) {
            $qrCodes[$s->id] = $this->generateQr($s->admission_no);
        }

        return view('livewire.student-qrcode', [
            'students' => $students,
            'qrCodes'  => $qrCodes,
            'campuses' => Campus::where('tenant_id', $tenantId)->get(),
            'classes'  => SchoolClass::where('tenant_id', $tenantId)->orderBy('numeric_value')->get(),
            'sections' => $this->school_class_id ? Section::where('tenant_id', $tenantId)->where('school_class_id', $this->school_class_id)->get() : [],
        ])->layout('layouts.app');
    }
}
