<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance\Challan;
use App\Models\Campus\Campus;

class ManageChallans extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $campus_id = '';
    public $month = '';
    public $year = '';
    public $status = '';

    public function mount()
    {
        $this->month = strtolower(date('M'));
        $this->year = date('Y');
    }

    public function updated() { $this->resetPage(); }

    public function markAsPaid($id)
    {
        $challan = Challan::findOrFail($id);
        $challan->update(['status' => 'paid']);
        session()->flash('message', "Challan #{$challan->challan_no} marked as PAID.");
    }

    public function deleteChallan($id)
    {
        Challan::findOrFail($id)->delete();
        session()->flash('message', "Challan deleted.");
    }

    public function render()
    {
        $query = Challan::with(['student.campus', 'items'])
            ->where('year', $this->year);

        if ($this->month) {
            $query->where('month', $this->month);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->campus_id) {
            $query->whereHas('student', function($q) {
                $q->where('campus_id', $this->campus_id);
            });
        }

        if ($this->search) {
            $query->whereHas('student', function($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('admission_no', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.manage-challans', [
            'challans' => $query->latest()->paginate(15),
            'campuses' => Campus::all(),
            'months' => ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec']
        ])->layout('layouts.app');
    }
}
