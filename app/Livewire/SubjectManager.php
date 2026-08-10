<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Academic\Subject;
use App\Models\Academic\SubjectGroup;
use App\Models\Academic\ClassSubject;
use App\Models\Academic\SchoolClass;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class SubjectManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Subject form
    public $name, $code, $subject_group_id, $subject_id;
    public $isSubjectModalOpen = false;

    // Group management
    public $group_name;
    public $isGroupModalOpen = false;

    // Class assignment
    public $managingClassesFor = null;
    public $assign_school_class_id, $assign_marks_total = 100;
    public $isAssignModalOpen = false;

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.subject-manager', [
            'subjects' => Subject::withCount('classSubjects')->with('group')->orderBy('name')->paginate(10),
            'groups' => SubjectGroup::where('tenant_id', $tenantId)->orderBy('name')->get(),
            'classes' => SchoolClass::where('tenant_id', $tenantId)->orderBy('numeric_value')->get(),
            'classAssignments' => $this->managingClassesFor
                ? ClassSubject::where('subject_id', $this->managingClassesFor->id)->with('schoolClass')->get()
                : [],
        ])->layout('layouts.app');
    }

    // --- SUBJECT MANAGEMENT ---

    public function openSubjectModal() { $this->isSubjectModalOpen = true; }
    public function closeSubjectModal() { $this->isSubjectModalOpen = false; $this->resetSubjectFields(); }

    private function resetSubjectFields()
    {
        $this->name = '';
        $this->code = '';
        $this->subject_group_id = '';
        $this->subject_id = '';
    }

    public function editSubject($id)
    {
        $subject = Subject::findOrFail($id);
        $this->subject_id = $subject->id;
        $this->name = $subject->name;
        $this->code = $subject->code;
        $this->subject_group_id = $subject->subject_group_id;
        $this->openSubjectModal();
    }

    public function saveSubject()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $this->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('subjects', 'name')->where('tenant_id', $tenantId)->ignore($this->subject_id),
            ],
            'code' => 'nullable|string|max:50',
            'subject_group_id' => 'nullable|exists:subject_groups,id',
        ], [
            'name.unique' => 'A subject named ":input" already exists.',
        ]);

        Subject::updateOrCreate(
            ['id' => $this->subject_id],
            [
                'name' => $this->name,
                'code' => $this->code,
                'subject_group_id' => $this->subject_group_id ?: null,
            ]
        );

        session()->flash('message', 'Subject saved successfully.');
        $this->closeSubjectModal();
    }

    public function deleteSubject($id)
    {
        Subject::findOrFail($id)->delete();
        session()->flash('message', 'Subject deleted successfully.');
    }

    // --- CLASS ASSIGNMENT ---

    public function manageClasses($subjectId)
    {
        $this->managingClassesFor = Subject::findOrFail($subjectId);
        $this->assign_school_class_id = '';
        $this->assign_marks_total = 100;
        $this->isAssignModalOpen = true;
    }

    public function closeAssignModal()
    {
        $this->isAssignModalOpen = false;
        $this->managingClassesFor = null;
    }

    public function assignToClass()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $this->validate([
            'assign_school_class_id' => [
                'required', 'exists:school_classes,id',
                Rule::unique('class_subjects', 'school_class_id')
                    ->where('tenant_id', $tenantId)
                    ->where('subject_id', $this->managingClassesFor->id),
            ],
            'assign_marks_total' => 'required|integer|min:1',
        ], [
            'assign_school_class_id.unique' => 'This subject is already assigned to that class.',
        ]);

        ClassSubject::create([
            'school_class_id' => $this->assign_school_class_id,
            'subject_id' => $this->managingClassesFor->id,
            'marks_total' => $this->assign_marks_total,
        ]);

        $this->assign_school_class_id = '';
        $this->assign_marks_total = 100;
        session()->flash('assign_message', 'Subject assigned to class.');
    }

    public function removeAssignment($id)
    {
        ClassSubject::findOrFail($id)->delete();
        session()->flash('assign_message', 'Assignment removed.');
    }

    // --- GROUP MANAGEMENT ---

    public function openGroupModal() { $this->isGroupModalOpen = true; }
    public function closeGroupModal() { $this->isGroupModalOpen = false; $this->group_name = ''; }

    public function saveGroup()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $this->validate([
            'group_name' => [
                'required', 'string', 'max:255',
                Rule::unique('subject_groups', 'name')->where('tenant_id', $tenantId),
            ],
        ], [
            'group_name.unique' => 'A subject group named ":input" already exists.',
        ]);

        SubjectGroup::create(['name' => $this->group_name]);

        $this->group_name = '';
        session()->flash('group_message', 'Subject group added successfully.');
    }

    public function deleteGroup($id)
    {
        SubjectGroup::findOrFail($id)->delete();
        session()->flash('group_message', 'Subject group deleted.');
    }
}
