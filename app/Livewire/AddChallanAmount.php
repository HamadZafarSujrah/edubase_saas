<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Finance\Challan;
use App\Models\Finance\ChallanItem;

class AddChallanAmount extends Component
{
    public $challan_id;
    public $challan;

    public $particular_name = '';
    public $amount = '';

    public function mount()
    {
        $this->challan_id = request()->query('challan_id');

        if (!$this->challan_id) {
            abort(400, 'Challan ID is required.');
        }

        $this->challan = Challan::with(['student', 'items'])->findOrFail($this->challan_id);
    }

    public function addAmount()
    {
        $this->validate([
            'particular_name' => 'required|string|max:150',
            'amount' => 'required|numeric|min:0.01',
        ]);

        ChallanItem::create([
            'challan_id' => $this->challan->id,
            'fee_particular_id' => null,
            'particular_name' => $this->particular_name,
            'amount' => $this->amount,
        ]);

        $newTotal = round((float) $this->challan->total_amount + (float) $this->amount, 2);
        $alreadySettled = round((float) $this->challan->paid_amount + (float) $this->challan->discount_amount, 2);

        $this->challan->update([
            'total_amount' => $newTotal,
            'status' => $newTotal > $alreadySettled ? ($alreadySettled > 0 ? 'partial' : 'unpaid') : $this->challan->status,
        ]);

        $this->particular_name = '';
        $this->amount = '';
        $this->challan = Challan::with(['student', 'items'])->findOrFail($this->challan_id);

        session()->flash('message', 'Amount added to challan successfully.');
    }

    public function render()
    {
        return view('livewire.add-challan-amount')->layout('layouts.app');
    }
}
