<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Exam\Exam;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Session;
use Illuminate\Support\Facades\Auth;

class ExamManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $name, $type = 'mid', $school_class_id, $session_id, $exam_date;
    public $exam_id;
    public $isModalOpen = false;

    public $filter_school_class_id = '';

    public function updatingFilterSchoolClassId() { $this->resetPage(); }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $query = Exam::where('tenant_id', $tenantId)->with(['schoolClass', 'session']);
        if ($this->filter_school_class_id) {
            $query->where('school_class_id', $this->filter_school_class_id);
        }

        return view('livewire.exam-manager', [
            'exams' => $query->orderByDesc('exam_date')->paginate(10),
            'classes' => SchoolClass::where('tenant_id', $tenantId)->orderBy('numeric_value')->get(),
            'sessions' => Session::where('tenant_id', $tenantId)->orderByDesc('start_date')->get(),
        ])->layout('layouts.app');
    }

    public function openModal() { $this->isModalOpen = true; }
    public function closeModal() { $this->isModalOpen = false; $this->resetFields(); }

    private function resetFields()
    {
        $this->name = '';
        $this->type = 'mid';
        $this->school_class_id = '';
        $this->session_id = '';
        $this->exam_date = '';
        $this->exam_id = '';
    }

    public function edit($id)
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $exam = Exam::where('tenant_id', $tenantId)->findOrFail($id);
        $this->exam_id = $exam->id;
        $this->name = $exam->name;
        $this->type = $exam->type;
        $this->school_class_id = $exam->school_class_id;
        $this->session_id = $exam->session_id;
        $this->exam_date = $exam->exam_date?->format('Y-m-d');
        $this->openModal();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:monthly,mid,final,quiz,assignment',
            'school_class_id' => 'required|exists:school_classes,id',
            'session_id' => 'nullable|exists:sessions,id',
            'exam_date' => 'nullable|date',
        ]);

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        Exam::updateOrCreate(
            ['id' => $this->exam_id, 'tenant_id' => $tenantId],
            [
                'name' => $this->name,
                'type' => $this->type,
                'school_class_id' => $this->school_class_id,
                'session_id' => $this->session_id ?: null,
                'exam_date' => $this->exam_date ?: null,
            ]
        );

        session()->flash('message', 'Exam saved successfully.');
        $this->closeModal();
    }

    public function delete($id)
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        Exam::where('tenant_id', $tenantId)->findOrFail($id)->delete();
        session()->flash('message', 'Exam deleted successfully.');
    }
}
