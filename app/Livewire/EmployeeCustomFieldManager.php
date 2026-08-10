<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\HRM\EmployeeCustomFieldDef;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class EmployeeCustomFieldManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $label;
    public $field_id;
    public $isModalOpen = false;

    public function render()
    {
        return view('livewire.employee-custom-field-manager', [
            'fields' => EmployeeCustomFieldDef::orderBy('label')->paginate(10),
        ])->layout('layouts.app');
    }

    public function openModal() { $this->isModalOpen = true; }
    public function closeModal() { $this->isModalOpen = false; $this->resetFields(); }

    private function resetFields()
    {
        $this->label = '';
        $this->field_id = '';
    }

    public function edit($id)
    {
        $field = EmployeeCustomFieldDef::findOrFail($id);
        $this->field_id = $field->id;
        $this->label = $field->label;
        $this->openModal();
    }

    public function save()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $this->validate([
            'label' => [
                'required', 'string', 'max:100',
                Rule::unique('employee_custom_field_defs', 'label')->where('tenant_id', $tenantId)->ignore($this->field_id),
            ],
        ], [
            'label.unique' => 'A custom field named ":input" already exists.',
        ]);

        EmployeeCustomFieldDef::updateOrCreate(
            ['id' => $this->field_id],
            ['label' => $this->label]
        );

        session()->flash('message', 'Custom field saved successfully.');
        $this->closeModal();
    }

    public function delete($id)
    {
        EmployeeCustomFieldDef::findOrFail($id)->delete();
        session()->flash('message', 'Custom field deleted successfully.');
    }
}
