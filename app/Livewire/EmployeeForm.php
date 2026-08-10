<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\HRM\Employee;
use App\Models\HRM\Department;
use App\Models\HRM\Designation;
use App\Models\HRM\EmploymentType;
use App\Models\HRM\Shift;
use App\Models\HRM\EmployeeCustomFieldDef;
use App\Models\Campus\Campus;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeeForm extends Component
{
    use WithFileUploads;

    public $employee_id;
    public $editing = false;

    public $emp_no, $first_name, $last_name, $cnic, $gender, $phone, $email;
    public $campus_id = '', $department_id = '', $designation_id = '', $shift_id = '', $employment_type_id = '';
    public $status = 'active';
    public $join_date;
    public $address;

    public $photo;
    public $existing_photo_url = null;

    public $attachment_rows = [];
    public $existing_documents = [];

    public $custom_fields = [];

    public function mount($id = null)
    {
        $this->join_date = date('Y-m-d');
        $this->attachment_rows = [['title' => '', 'file' => null]];

        if ($id) {
            $emp = Employee::findOrFail($id);
            $this->editing = true;
            $this->employee_id = $emp->id;
            $this->emp_no = $emp->emp_no;
            $this->first_name = $emp->first_name;
            $this->last_name = $emp->last_name;
            $this->cnic = $emp->cnic;
            $this->gender = $emp->gender;
            $this->phone = $emp->phone;
            $this->email = $emp->email;
            $this->campus_id = $emp->campus_id;
            $this->department_id = $emp->department_id;
            $this->designation_id = $emp->designation_id;
            $this->shift_id = $emp->shift_id;
            $this->employment_type_id = $emp->employment_type_id;
            $this->status = $emp->status;
            $this->join_date = $emp->join_date?->format('Y-m-d');
            $this->address = $emp->address;
            $this->existing_photo_url = $emp->photo_path ? Storage::disk('public')->url($emp->photo_path) : null;
            $this->existing_documents = $emp->documents()->get()->map(fn ($d) => [
                'title' => $d->title,
                'url' => Storage::disk('public')->url($d->file_path),
            ])->toArray();
            $this->custom_fields = $emp->custom_fields ?? [];
            return;
        }

        $this->generateEmpNo();
    }

    public function generateEmpNo()
    {
        // MAX(id)+1, not COUNT()+1 -- a hard-deleted employee would make COUNT()
        // regress and suggest a number already taken by a later employee. This
        // is only a starting suggestion; the tenant-scoped uniqueness rule in
        // save() is the real guard.
        $next = (int) (Employee::max('id') ?? 0) + 1;
        $this->emp_no = 'EMP-' . date('Y') . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function updatedDepartmentId()
    {
        $this->designation_id = '';
    }

    public function addAttachment() { $this->attachment_rows[] = ['title' => '', 'file' => null]; }

    public function removeAttachment($index)
    {
        unset($this->attachment_rows[$index]);
        $this->attachment_rows = array_values($this->attachment_rows);
    }

    public function save()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $this->validate([
            'emp_no' => [
                'required', 'string', 'max:50',
                Rule::unique('employees', 'emp_no')->where('tenant_id', $tenantId)->ignore($this->employee_id),
            ],
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'cnic' => 'nullable|string|max:20',
            'gender' => 'nullable|in:Male,Female,Other',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'campus_id' => 'nullable|exists:campuses,id',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'shift_id' => 'nullable|exists:shifts,id',
            'employment_type_id' => 'nullable|exists:employment_types,id',
            'status' => 'required|in:active,inactive',
            'join_date' => 'nullable|date',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ], [
            'emp_no.unique' => 'This employee number is already in use.',
        ]);

        $data = [
            'emp_no' => $this->emp_no,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'cnic' => $this->cnic,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'email' => $this->email,
            'campus_id' => $this->campus_id ?: null,
            'department_id' => $this->department_id ?: null,
            'designation_id' => $this->designation_id ?: null,
            'shift_id' => $this->shift_id ?: null,
            'employment_type_id' => $this->employment_type_id ?: null,
            'status' => $this->status,
            'join_date' => $this->join_date ?: null,
            'address' => $this->address,
            'custom_fields' => array_filter($this->custom_fields, fn ($v) => $v !== '' && $v !== null),
        ];

        if ($this->photo) {
            $data['photo_path'] = $this->photo->store('employee-photos', 'public');
        }

        $employee = Employee::updateOrCreate(['id' => $this->employee_id], $data);

        foreach ($this->attachment_rows as $row) {
            if ($row['file'] && $row['title']) {
                $path = $row['file']->store('employee-documents', 'public');
                $employee->documents()->create([
                    'title' => $row['title'],
                    'file_path' => $path,
                    'file_type' => $row['file']->getClientOriginalExtension(),
                ]);
            }
        }

        session()->flash('message', $this->editing ? 'Employee updated successfully.' : 'Employee added successfully.');

        return redirect()->route('hrm.employees.directory');
    }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.employee-form', [
            'campuses' => Campus::orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
            'designations' => $this->department_id ? Designation::where('department_id', $this->department_id)->get() : [],
            'shifts' => Shift::orderBy('name')->get(),
            'employmentTypes' => EmploymentType::orderBy('name')->get(),
            'customFieldDefs' => EmployeeCustomFieldDef::where('tenant_id', $tenantId)->orderBy('label')->get(),
        ])->layout('layouts.app');
    }
}
