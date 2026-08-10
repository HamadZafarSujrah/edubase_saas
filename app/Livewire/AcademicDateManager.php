<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Academic\AcademicDate;
use Illuminate\Support\Facades\Auth;

class AcademicDateManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $title, $start_date, $end_date, $type = 'holiday', $description;
    public $date_id;
    public $isModalOpen = false;

    public function render()
    {
        return view('livewire.academic-date-manager', [
            'dates' => AcademicDate::orderByDesc('start_date')->paginate(10),
        ])->layout('layouts.app');
    }

    public function openModal() { $this->isModalOpen = true; }
    public function closeModal() { $this->isModalOpen = false; $this->resetFields(); }

    private function resetFields()
    {
        $this->title = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->type = 'holiday';
        $this->description = '';
        $this->date_id = '';
    }

    public function edit($id)
    {
        $date = AcademicDate::findOrFail($id);
        $this->date_id = $date->id;
        $this->title = $date->title;
        $this->start_date = $date->start_date->format('Y-m-d');
        $this->end_date = $date->end_date?->format('Y-m-d');
        $this->type = $date->type;
        $this->description = $date->description;
        $this->openModal();
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'type' => 'required|in:holiday,event',
            'description' => 'nullable|string|max:255',
        ]);

        AcademicDate::updateOrCreate(
            ['id' => $this->date_id],
            [
                'title' => $this->title,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date ?: null,
                'type' => $this->type,
                'description' => $this->description,
            ]
        );

        session()->flash('message', 'Academic date saved successfully.');
        $this->closeModal();
    }

    public function delete($id)
    {
        AcademicDate::findOrFail($id)->delete();
        session()->flash('message', 'Academic date deleted successfully.');
    }
}
