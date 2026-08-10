<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\HRM\AllowanceType;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class AllowanceTypeManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $name, $type = 'allowance', $description;
    public $allowance_type_id;
    public $isModalOpen = false;

    public function render()
    {
        return view('livewire.allowance-type-manager', [
            'allowanceTypes' => AllowanceType::orderBy('name')->paginate(10),
        ])->layout('layouts.app');
    }

    public function openModal() { $this->isModalOpen = true; }
    public function closeModal() { $this->isModalOpen = false; $this->resetFields(); }

    private function resetFields()
    {
        $this->name = '';
        $this->type = 'allowance';
        $this->description = '';
        $this->allowance_type_id = '';
    }

    public function edit($id)
    {
        $allowanceType = AllowanceType::findOrFail($id);
        $this->allowance_type_id = $allowanceType->id;
        $this->name = $allowanceType->name;
        $this->type = $allowanceType->type;
        $this->description = $allowanceType->description;
        $this->openModal();
    }

    public function save()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $this->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('allowance_types', 'name')->where('tenant_id', $tenantId)->ignore($this->allowance_type_id),
            ],
            'type' => 'required|in:allowance,deduction',
            'description' => 'nullable|string|max:255',
        ], [
            'name.unique' => 'An allowance/deduction type named ":input" already exists.',
        ]);

        AllowanceType::updateOrCreate(
            ['id' => $this->allowance_type_id],
            ['name' => $this->name, 'type' => $this->type, 'description' => $this->description]
        );

        session()->flash('message', 'Allowance/deduction type saved successfully.');
        $this->closeModal();
    }

    public function delete($id)
    {
        AllowanceType::findOrFail($id)->delete();
        session()->flash('message', 'Allowance/deduction type deleted successfully.');
    }
}
