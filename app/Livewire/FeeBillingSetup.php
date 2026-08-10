<?php

namespace App\Livewire;

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

    public function updatedCampusId()
    {
        // Switching campus must discard any not-yet-saved edits for the
        // previous campus -- otherwise they silently ride along and get
        // committed the next time Save All Changes is clicked, applying to
        // rows the admin can no longer even see on screen.
        $this->editing_amounts = [];
    }

    public function saveAll()
    {
        $errors = [];

        foreach ($this->editing_amounts as $id => $data) {
            $amount = (float) ($data['amount'] ?? 0);
            $minAmount = (float) ($data['min_amount'] ?? 0);

            if ($amount < 0 || $minAmount < 0) {
                $errors[] = "Record #{$id}: amount and minimum amount cannot be negative.";
                continue;
            }
            if ($minAmount > $amount) {
                $errors[] = "Record #{$id}: minimum amount cannot exceed the amount.";
                continue;
            }

            $record = FeePlanParticular::find($id);
            if ($record) {
                $record->update(['amount' => $amount, 'min_amount' => $minAmount]);
            }
        }

        if (!empty($errors)) {
            session()->flash('error', 'Some rows were not saved: ' . implode(' ', $errors));
            return;
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
        $query = FeePlanParticular::with(['campus', 'feePlan', 'particular'])
            ->where('is_mapped', true);
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
