<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\HRM\EmployeeStanding;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class EmployeeStandingManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $name, $description;
    public $standing_id;
    public $isModalOpen = false;

    public function render()
    {
        return view('livewire.employee-standing-manager', [
            'standings' => EmployeeStanding::orderBy('name')->paginate(10),
        ])->layout('layouts.app');
    }

    public function openModal() { $this->isModalOpen = true; }
    public function closeModal() { $this->isModalOpen = false; $this->resetFields(); }

    private function resetFields()
    {
        $this->name = '';
        $this->description = '';
        $this->standing_id = '';
    }

    public function edit($id)
    {
        $standing = EmployeeStanding::findOrFail($id);
        $this->standing_id = $standing->id;
        $this->name = $standing->name;
        $this->description = $standing->description;
        $this->openModal();
    }

    public function save()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $this->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('employee_standings', 'name')->where('tenant_id', $tenantId)->ignore($this->standing_id),
            ],
            'description' => 'nullable|string|max:255',
        ], [
            'name.unique' => 'A standing named ":input" already exists.',
        ]);

        EmployeeStanding::updateOrCreate(
            ['id' => $this->standing_id],
            ['name' => $this->name, 'description' => $this->description]
        );

        session()->flash('message', 'Standing saved successfully.');
        $this->closeModal();
    }

    public function delete($id)
    {
        EmployeeStanding::findOrFail($id)->delete();
        session()->flash('message', 'Standing deleted successfully.');
    }
}
