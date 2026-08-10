<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\General\House;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class HouseManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $name, $color, $motto;
    public $house_id;
    public $isModalOpen = false;

    public function render()
    {
        return view('livewire.house-manager', [
            'houses' => House::withCount('students')->orderBy('name')->paginate(10),
        ])->layout('layouts.app');
    }

    public function openModal() { $this->isModalOpen = true; }
    public function closeModal() { $this->isModalOpen = false; $this->resetFields(); }

    private function resetFields()
    {
        $this->name = '';
        $this->color = '';
        $this->motto = '';
        $this->house_id = '';
    }

    public function edit($id)
    {
        $house = House::findOrFail($id);
        $this->house_id = $house->id;
        $this->name = $house->name;
        $this->color = $house->color;
        $this->motto = $house->motto;
        $this->openModal();
    }

    public function save()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $this->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('houses', 'name')->where('tenant_id', $tenantId)->ignore($this->house_id),
            ],
            'color' => 'nullable|string|max:30',
            'motto' => 'nullable|string|max:255',
        ], [
            'name.unique' => 'A house named ":input" already exists.',
        ]);

        House::updateOrCreate(
            ['id' => $this->house_id],
            ['name' => $this->name, 'color' => $this->color, 'motto' => $this->motto]
        );

        session()->flash('message', 'House saved successfully.');
        $this->closeModal();
    }

    public function delete($id)
    {
        House::findOrFail($id)->delete();
        session()->flash('message', 'House deleted successfully.');
    }
}
