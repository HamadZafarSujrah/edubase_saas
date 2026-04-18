<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Finance\FeePlanParticular;
use App\Models\Campus\Campus;

class FeeBillingSetup extends Component
{
    public $campus_id;
    public $editing_amounts = []; // Stores amounts temporarily

    public function mount()
    {
        $c = Campus::first();
        if ($c) $this->campus_id = $c->id;
    }

    public function updated($propertyName)
    {
        // Auto-save logic if something changes in the matrix
        if (strpos($propertyName, 'editing_amounts') !== false) {
           // We can implement a bulk save button instead for better performance
        }
    }

    public function saveAll()
    {
        foreach ($this->editing_amounts as $id => $data) {
            $record = FeePlanParticular::find($id);
            if ($record) {
                $record->update($data);
            }
        }
        session()->flash('message', 'Billing Matrix updated successfully!');
    }

    public function toggleMonth($recordId, $month)
    {
        $record = FeePlanParticular::find($recordId);
        if ($record) {
            $record->$month = !$record->$month;
            $record->save();
        }
    }

    public function toggleFirstTime($recordId)
    {
        $record = FeePlanParticular::find($recordId);
        if ($record) {
            $record->is_first_time = !$record->is_first_time;
            $record->save();
        }
    }

    public function render()
    {
        $query = FeePlanParticular::with(['campus', 'feePlan', 'particular']);
        if ($this->campus_id) {
            $query->where('campus_id', $this->campus_id);
        }
        
        $records = $query->get();

        // Initialize editing_amounts with current values
        foreach ($records as $r) {
            if (!isset($this->editing_amounts[$r->id])) {
                $this->editing_amounts[$r->id] = [
                    'amount' => $r->amount,
                    'min_amount' => $r->min_amount
                ];
            }
        }

        return view('livewire.fee-billing-setup', [
            'campuses' => Campus::all(),
            'records' => $records
        ])->layout('layouts.app');
    }
}
