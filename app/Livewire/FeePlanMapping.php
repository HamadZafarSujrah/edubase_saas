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

        // findOrFail on tenant-scoped models throws if the id belongs to
        // another tenant, closing off a forged-payload cross-tenant reference
        // instead of silently accepting it.
        FeePlan::findOrFail($planId);
        FeeParticular::findOrFail($particularId);

        // firstOrCreate (backed by the unique index on tenant/campus/plan/
        // particular) makes this atomic -- a double-click/slow-network retry
        // can no longer insert two rows for the same mapping.
        $mapping = FeePlanParticular::firstOrCreate([
            'campus_id' => $this->campus_id,
            'fee_plan_id' => $planId,
            'fee_particular_id' => $particularId,
        ], [
            'is_mapped' => true,
            // amount/min_amount/months default to 0/false via migration
        ]);

        // Toggle mapped state rather than deleting the row -- deleting wipes
        // out amount/min_amount/month/is_first_time configuration the next
        // time this particular is re-checked, with no warning.
        if (!$mapping->wasRecentlyCreated) {
            $mapping->update(['is_mapped' => !$mapping->is_mapped]);
        }
    }

    /**
     * Map every particular in the current list onto one plan in one click,
     * for particulars not already mapped (mapped or unmapped-but-existing).
     */
    public function toggleAllForPlan($planId)
    {
        if (!$this->campus_id) return;

        $plan = FeePlan::findOrFail($planId);
        $particularIds = FeeParticular::pluck('id');

        foreach ($particularIds as $particularId) {
            $mapping = FeePlanParticular::firstOrCreate([
                'campus_id' => $this->campus_id,
                'fee_plan_id' => $plan->id,
                'fee_particular_id' => $particularId,
            ], [
                'is_mapped' => true,
            ]);

            if (!$mapping->wasRecentlyCreated && !$mapping->is_mapped) {
                $mapping->update(['is_mapped' => true]);
            }
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
                ->where('is_mapped', true)
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
