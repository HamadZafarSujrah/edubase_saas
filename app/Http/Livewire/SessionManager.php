<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Academic\Session;
use Livewire\WithPagination;

class SessionManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $name, $start_date, $end_date, $is_active = false;
    public $session_id;
    public $isModalOpen = false;

    public function render()
    {
        return view('livewire.session-manager', [
            'sessions' => Session::orderBy('start_date', 'desc')->paginate(10)
        ])->layout('layouts.app');
    }

    public function openModal() { $this->isModalOpen = true; }
    public function closeModal() { $this->isModalOpen = false; $this->resetFields(); }

    private function resetFields()
    {
        $this->name = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->is_active = false;
        $this->session_id = '';
    }

    public function edit($id)
    {
        $session = Session::findOrFail($id);
        $this->session_id = $session->id;
        $this->name = $session->name;
        $this->start_date = $session->start_date->format('Y-m-d');
        $this->end_date = $session->end_date->format('Y-m-d');
        $this->is_active = $session->is_active;
        $this->openModal();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // If this one is becoming active, optionally deactivate others.
        // For schools, we often keep strict: 1 active session at a time.
        if ($this->is_active) {
            Session::where('is_active', true)->update(['is_active' => false]);
        }

        Session::updateOrCreate(
            ['id' => $this->session_id],
            [
                'name' => $this->name,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'is_active' => $this->is_active
            ]
        );

        session()->flash('message', 'Academic Session saved successfully.');
        $this->closeModal();
    }

    public function delete($id)
    {
        Session::findOrFail($id)->delete();
        session()->flash('message', 'Session deleted successfully.');
    }
}
