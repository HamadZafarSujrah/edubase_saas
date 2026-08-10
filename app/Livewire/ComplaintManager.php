<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\General\Complaint;
use App\Models\Student\Student;
use Illuminate\Support\Facades\Auth;

class ComplaintManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // New complaint form
    public $subject = '';
    public $description = '';
    public $complainant_name = '';
    public $complainant_phone = '';
    public $student_search = '';
    public $suggested_students = [];
    public $student_id = '';
    public $selected_student = null;
    public $isFormOpen = false;

    // List filters
    public $status_filter = 'open';

    public function updatingStatusFilter() { $this->resetPage(); }

    public function openForm() { $this->isFormOpen = true; }
    public function closeForm() { $this->isFormOpen = false; $this->resetForm(); }

    private function resetForm()
    {
        $this->subject = '';
        $this->description = '';
        $this->complainant_name = '';
        $this->complainant_phone = '';
        $this->student_search = '';
        $this->suggested_students = [];
        $this->student_id = '';
        $this->selected_student = null;
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
                  ->orWhere('admission_no', 'like', '%' . $this->student_search . '%');
            })
            ->limit(8)
            ->get(['id', 'first_name', 'last_name', 'admission_no'])
            ->toArray();
    }

    public function selectStudent($id)
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $this->selected_student = Student::where('tenant_id', $tenantId)->find($id);
        $this->student_id = $id;
        $this->suggested_students = [];
        $this->student_search = trim(($this->selected_student->first_name ?? '') . ' ' . ($this->selected_student->last_name ?? ''));
    }

    public function submit()
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'complainant_name' => 'nullable|string|max:100',
            'complainant_phone' => 'nullable|string|max:20',
        ]);

        Complaint::create([
            'student_id' => $this->student_id ?: null,
            'subject' => $this->subject,
            'description' => $this->description,
            'complainant_name' => $this->complainant_name,
            'complainant_phone' => $this->complainant_phone,
            'status' => 'open',
            'created_by' => Auth::id(),
        ]);

        session()->flash('message', 'Complaint logged successfully.');
        $this->closeForm();
    }

    public function markInProgress($id)
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        Complaint::where('tenant_id', $tenantId)->findOrFail($id)->update([
            'status' => 'in_progress',
            'assigned_to' => Auth::id(),
        ]);
        session()->flash('message', 'Complaint marked in progress.');
    }

    public function resolve($id)
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        Complaint::where('tenant_id', $tenantId)->findOrFail($id)->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);
        session()->flash('message', 'Complaint marked resolved.');
    }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $query = Complaint::where('tenant_id', $tenantId)->with(['student', 'assignedTo', 'createdBy']);

        if ($this->status_filter !== 'all') {
            $query->where('status', $this->status_filter);
        }

        return view('livewire.complaint-manager', [
            'complaints' => $query->latest('id')->paginate(15),
        ])->layout('layouts.app');
    }
}
