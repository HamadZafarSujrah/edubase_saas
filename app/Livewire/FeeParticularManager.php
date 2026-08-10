<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Finance\FeeParticular;
use Illuminate\Validation\Rule;

class FeeParticularManager extends Component
{
    public $particular_name, $editing_id;

    protected function rules(): array
    {
        $tenantId = session('tenant_id') ?? auth()->user()->tenant_id;

        return [
            'particular_name' => [
                'required', 'string', 'max:100',
                Rule::unique('fee_particulars', 'name')
                    ->where('tenant_id', $tenantId)
                    ->ignore($this->editing_id),
            ],
        ];
    }

    protected $messages = [
        'particular_name.unique' => 'A fee particular with this name already exists.',
    ];

    public function render()
    {
        return view('livewire.fee-particular-manager', [
            'particulars' => FeeParticular::latest()->get()
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->validate();
        FeeParticular::create(['name' => $this->particular_name]);
        $this->particular_name = '';
        session()->flash('message', 'Fee Particular Created successfully.');
    }

    public function edit($id)
    {
        $p = FeeParticular::findOrFail($id);
        $this->editing_id = $id;
        $this->particular_name = $p->name;
    }

    public function update()
    {
        $this->validate();
        FeeParticular::findOrFail($this->editing_id)->update(['name' => $this->particular_name]);
        $this->reset(['editing_id', 'particular_name']);
        session()->flash('message', 'Fee Particular Updated successfully.');
    }

    public function delete($id)
    {
        try {
            FeeParticular::findOrFail($id)->delete();
            session()->flash('message', 'Deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'This fee particular is still mapped to one or more fee plans and cannot be deleted.');
        }
    }
}
