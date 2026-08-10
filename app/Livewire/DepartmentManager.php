<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\HRM\Department;
use App\Models\HRM\DepartmentGroup;
use App\Models\HRM\Designation;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class DepartmentManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Form fields for Department
    public $name, $department_group_id, $department_id;
    public $isDepartmentModalOpen = false;

    // Form fields for Designation
    public $managingDesignationsFor = null;
    public $designation_name;
    public $isDesignationModalOpen = false;

    // Form fields for Department Group
    public $group_name;
    public $isGroupModalOpen = false;

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.department-manager', [
            'departments' => Department::withCount('designations')->with('group')->orderBy('name')->paginate(10),
            'groups' => DepartmentGroup::where('tenant_id', $tenantId)->orderBy('name')->get(),
            'designations' => $this->managingDesignationsFor
                ? Designation::where('department_id', $this->managingDesignationsFor->id)->get()
                : [],
        ])->layout('layouts.app');
    }

    // --- DEPARTMENT MANAGEMENT ---

    public function openDepartmentModal() { $this->isDepartmentModalOpen = true; }
    public function closeDepartmentModal() { $this->isDepartmentModalOpen = false; $this->resetDepartmentFields(); }

    private function resetDepartmentFields()
    {
        $this->name = '';
        $this->department_group_id = '';
        $this->department_id = '';
    }

    public function editDepartment($id)
    {
        $dept = Department::findOrFail($id);
        $this->department_id = $dept->id;
        $this->name = $dept->name;
        $this->department_group_id = $dept->department_group_id;
        $this->openDepartmentModal();
    }

    public function saveDepartment()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $this->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('departments', 'name')
                    ->where('tenant_id', $tenantId)
                    ->ignore($this->department_id),
            ],
            'department_group_id' => 'nullable|exists:department_groups,id',
        ], [
            'name.unique' => 'A department named ":input" already exists.',
        ]);

        Department::updateOrCreate(
            ['id' => $this->department_id],
            [
                'name' => $this->name,
                'department_group_id' => $this->department_group_id ?: null,
            ]
        );

        session()->flash('message', 'Department saved successfully.');
        $this->closeDepartmentModal();
    }

    public function deleteDepartment($id)
    {
        Department::findOrFail($id)->delete();
        session()->flash('message', 'Department deleted successfully.');
    }

    // --- DESIGNATION MANAGEMENT ---

    public function manageDesignations($departmentId)
    {
        $this->managingDesignationsFor = Department::findOrFail($departmentId);
        $this->designation_name = '';
        $this->isDesignationModalOpen = true;
    }

    public function closeDesignationModal()
    {
        $this->isDesignationModalOpen = false;
        $this->managingDesignationsFor = null;
    }

    public function saveDesignation()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $this->validate([
            'designation_name' => [
                'required', 'string', 'max:100',
                Rule::unique('designations', 'name')
                    ->where('tenant_id', $tenantId)
                    ->where('department_id', $this->managingDesignationsFor->id),
            ],
        ], [
            'designation_name.unique' => 'This department already has a designation named ":input".',
        ]);

        Designation::create([
            'department_id' => $this->managingDesignationsFor->id,
            'name' => $this->designation_name,
        ]);

        $this->designation_name = '';
        session()->flash('designation_message', 'Designation added successfully.');
    }

    public function deleteDesignation($id)
    {
        Designation::findOrFail($id)->delete();
        session()->flash('designation_message', 'Designation deleted.');
    }

    // --- DEPARTMENT GROUP MANAGEMENT ---

    public function openGroupModal() { $this->isGroupModalOpen = true; }
    public function closeGroupModal() { $this->isGroupModalOpen = false; $this->group_name = ''; }

    public function saveGroup()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $this->validate([
            'group_name' => [
                'required', 'string', 'max:255',
                Rule::unique('department_groups', 'name')->where('tenant_id', $tenantId),
            ],
        ], [
            'group_name.unique' => 'A department group named ":input" already exists.',
        ]);

        DepartmentGroup::create(['name' => $this->group_name]);

        $this->group_name = '';
        session()->flash('group_message', 'Department group added successfully.');
    }

    public function deleteGroup($id)
    {
        DepartmentGroup::findOrFail($id)->delete();
        session()->flash('group_message', 'Department group deleted.');
    }
}
