<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Finance\FeeParticular;

class FeeParticularManager extends Component
{
    public $particular_name, $editing_id;

    protected $rules = [
        'particular_name' => 'required|string|max:100',
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
        FeeParticular::findOrFail($id)->delete();
        session()->flash('message', 'Deleted successfully.');
    }
}
