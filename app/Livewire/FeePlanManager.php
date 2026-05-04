<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Finance\FeePlan;

class FeePlanManager extends Component
{
    public $plan_name, $description, $editing_id;

    public function render()
    {
        return view('livewire.fee-plan-manager', [
            'plans' => FeePlan::latest()->get()
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->validate(['plan_name' => 'required']);
        FeePlan::create(['name' => $this->plan_name, 'description' => $this->description]);
        $this->reset(['plan_name', 'description']);
        session()->flash('message', 'Plan created!');
    }

    public function edit($id)
    {
        $p = FeePlan::findOrFail($id);
        $this->editing_id = $id;
        $this->plan_name = $p->name;
        $this->description = $p->description;
    }

    public function update()
    {
        $this->validate(['plan_name' => 'required']);
        FeePlan::findOrFail($this->editing_id)->update(['name' => $this->plan_name, 'description' => $this->description]);
        $this->reset(['editing_id', 'plan_name', 'description']);
        session()->flash('message', 'Plan updated!');
    }

    public function delete($id)
    {
        FeePlan::findOrFail($id)->delete();
    }
}
