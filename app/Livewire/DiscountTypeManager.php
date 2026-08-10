<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Finance\DiscountType;
use App\Models\Finance\ChallanDiscount;
use Illuminate\Validation\Rule;

class DiscountTypeManager extends Component
{
    public $name, $type = 'fixed', $is_active = true, $editing_id;

    protected function rules(): array
    {
        $tenantId = session('tenant_id') ?? auth()->user()->tenant_id;

        return [
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('discount_types', 'name')
                    ->where('tenant_id', $tenantId)
                    ->ignore($this->editing_id),
            ],
            'type' => 'required|in:percent,fixed',
        ];
    }

    protected $messages = [
        'name.unique' => 'A discount type with this name already exists.',
    ];

    public function render()
    {
        return view('livewire.discount-type-manager', [
            'discountTypes' => DiscountType::latest()->get()
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->validate();
        DiscountType::create([
            'name' => $this->name,
            'type' => $this->type,
            'is_active' => $this->is_active,
        ]);
        $this->reset(['name', 'type', 'is_active']);
        $this->type = 'fixed';
        $this->is_active = true;
        session()->flash('message', 'Discount Type created successfully.');
    }

    public function edit($id)
    {
        $d = DiscountType::findOrFail($id);
        $this->editing_id = $id;
        $this->name = $d->name;
        $this->type = $d->type;
        $this->is_active = $d->is_active;
    }

    public function update()
    {
        $this->validate();
        DiscountType::findOrFail($this->editing_id)->update([
            'name' => $this->name,
            'type' => $this->type,
            'is_active' => $this->is_active,
        ]);
        $this->reset(['editing_id', 'name', 'type', 'is_active']);
        $this->type = 'fixed';
        $this->is_active = true;
        session()->flash('message', 'Discount Type updated successfully.');
    }

    public function delete($id)
    {
        // Deleting would null out discount_type_id on every historical
        // ChallanDiscount row referencing it (challan_discounts.discount_type_id
        // is nullOnDelete) -- permanently erasing why past discounts were
        // given. Deactivate instead of losing that audit trail.
        if (ChallanDiscount::where('discount_type_id', $id)->exists()) {
            session()->flash('error', 'This discount type has been used on past payments and cannot be deleted. Mark it Inactive instead.');
            return;
        }

        DiscountType::findOrFail($id)->delete();
        session()->flash('message', 'Deleted successfully.');
    }
}
