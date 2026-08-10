<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Academic\Standing;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StandingManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $name, $description;
    public $standing_id;
    public $isModalOpen = false;

    public function render()
    {
        return view('livewire.standing-manager', [
            'standings' => Standing::orderBy('name')->paginate(10),
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
        $standing = Standing::findOrFail($id);
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
                Rule::unique('standings', 'name')->where('tenant_id', $tenantId)->ignore($this->standing_id),
            ],
            'description' => 'nullable|string|max:255',
        ], [
            'name.unique' => 'A standing named ":input" already exists.',
        ]);

        Standing::updateOrCreate(
            ['id' => $this->standing_id],
            ['name' => $this->name, 'description' => $this->description]
        );

        session()->flash('message', 'Standing saved successfully.');
        $this->closeModal();
    }

    public function delete($id)
    {
        Standing::findOrFail($id)->delete();
        session()->flash('message', 'Standing deleted successfully.');
    }
}
