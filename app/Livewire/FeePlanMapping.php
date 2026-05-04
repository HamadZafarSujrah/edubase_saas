<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Finance\FeePlan;
use App\Models\Finance\FeeParticular;
use App\Models\Finance\FeePlanParticular;
use App\Models\Campus\Campus;

class FeePlanMapping extends Component
{
    public $campus_id;

    public function mount()
    {
        $c = Campus::first();
        if ($c) $this->campus_id = $c->id;
    }

    public function toggleMapping($planId, $particularId)
    {
        if (!$this->campus_id) return;

        $existing = FeePlanParticular::where([
            'campus_id' => $this->campus_id,
            'fee_plan_id' => $planId,
            'fee_particular_id' => $particularId
        ])->first();

        if ($existing) {
            $existing->delete();
        } else {
            FeePlanParticular::create([
                'campus_id' => $this->campus_id,
                'fee_plan_id' => $planId,
                'fee_particular_id' => $particularId,
                // defaults to 0 amount and all months false
            ]);
        }
    }

    public function render()
    {
        $campuses = Campus::all();
        $plans = FeePlan::all();
        $particulars = FeeParticular::all();

        // Get current mappings for the selected campus
        $mappings = [];
        if ($this->campus_id) {
            $mappings = FeePlanParticular::where('campus_id', $this->campus_id)
                ->get()
                ->groupBy('fee_plan_id')
                ->map(function($items) {
                    return $items->pluck('fee_particular_id')->toArray();
                })->toArray();
        }

        return view('livewire.fee-plan-mapping', [
            'campuses' => $campuses,
            'plans' => $plans,
            'particulars' => $particulars,
            'mappings' => $mappings
        ])->layout('layouts.app');
    }
}
