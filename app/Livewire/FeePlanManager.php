<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Finance\FeePlan;
use Illuminate\Validation\Rule;

class FeePlanManager extends Component
{
    public $plan_name, $description, $editing_id;

    protected function rules(): array
    {
        $tenantId = session('tenant_id') ?? auth()->user()->tenant_id;

        return [
            'plan_name' => [
                'required', 'string', 'max:100',
                Rule::unique('fee_plans', 'name')
                    ->where('tenant_id', $tenantId)
                    ->ignore($this->editing_id),
            ],
        ];
    }

    protected $messages = [
        'plan_name.unique' => 'A fee plan with this name already exists.',
    ];

    public function render()
    {
        return view('livewire.fee-plan-manager', [
            'plans' => FeePlan::latest()->get()
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->validate();
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
        $this->validate();
        FeePlan::findOrFail($this->editing_id)->update(['name' => $this->plan_name, 'description' => $this->description]);
        $this->reset(['editing_id', 'plan_name', 'description']);
        session()->flash('message', 'Plan updated!');
    }

    public function delete($id)
    {
        try {
            FeePlan::findOrFail($id)->delete();
            session()->flash('message', 'Plan deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'This fee plan is still mapped to particulars and cannot be deleted.');
        }
    }
}
