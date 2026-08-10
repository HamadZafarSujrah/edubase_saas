<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\HRM\EmploymentType;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class EmploymentTypeManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $name;
    public $employment_type_id;
    public $isModalOpen = false;

    public function render()
    {
        return view('livewire.employment-type-manager', [
            'employmentTypes' => EmploymentType::orderBy('name')->paginate(10),
        ])->layout('layouts.app');
    }

    public function openModal() { $this->isModalOpen = true; }
    public function closeModal() { $this->isModalOpen = false; $this->resetFields(); }

    private function resetFields()
    {
        $this->name = '';
        $this->employment_type_id = '';
    }

    public function edit($id)
    {
        $type = EmploymentType::findOrFail($id);
        $this->employment_type_id = $type->id;
        $this->name = $type->name;
        $this->openModal();
    }

    public function save()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $this->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('employment_types', 'name')
                    ->where('tenant_id', $tenantId)
                    ->ignore($this->employment_type_id),
            ],
        ], [
            'name.unique' => 'An employment type named ":input" already exists.',
        ]);

        EmploymentType::updateOrCreate(
            ['id' => $this->employment_type_id],
            ['name' => $this->name]
        );

        session()->flash('message', 'Employment type saved successfully.');
        $this->closeModal();
    }

    public function delete($id)
    {
        EmploymentType::findOrFail($id)->delete();
        session()->flash('message', 'Employment type deleted successfully.');
    }
}
