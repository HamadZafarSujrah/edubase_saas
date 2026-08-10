<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Academic\ClassShift;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ClassShiftManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $name, $start_time, $end_time;
    public $shift_id;
    public $isModalOpen = false;

    public function render()
    {
        return view('livewire.class-shift-manager', [
            'shifts' => ClassShift::withCount('schoolClasses')->orderBy('name')->paginate(10),
        ])->layout('layouts.app');
    }

    public function openModal() { $this->isModalOpen = true; }
    public function closeModal() { $this->isModalOpen = false; $this->resetFields(); }

    private function resetFields()
    {
        $this->name = '';
        $this->start_time = '';
        $this->end_time = '';
        $this->shift_id = '';
    }

    public function edit($id)
    {
        $shift = ClassShift::findOrFail($id);
        $this->shift_id = $shift->id;
        $this->name = $shift->name;
        // DB TIME columns come back as "H:i:s" -- trim to "H:i" to match the
        // date_format:H:i validation rule and the <input type="time"> field.
        $this->start_time = $shift->start_time ? substr($shift->start_time, 0, 5) : '';
        $this->end_time = $shift->end_time ? substr($shift->end_time, 0, 5) : '';
        $this->openModal();
    }

    public function save()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $this->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('class_shifts', 'name')->where('tenant_id', $tenantId)->ignore($this->shift_id),
            ],
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
        ], [
            'name.unique' => 'A class shift named ":input" already exists.',
        ]);

        ClassShift::updateOrCreate(
            ['id' => $this->shift_id],
            [
                'name' => $this->name,
                'start_time' => $this->start_time ?: null,
                'end_time' => $this->end_time ?: null,
            ]
        );

        session()->flash('message', 'Class shift saved successfully.');
        $this->closeModal();
    }

    public function delete($id)
    {
        ClassShift::findOrFail($id)->delete();
        session()->flash('message', 'Class shift deleted successfully.');
    }
}
