<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Academic\GradingPolicy;
use App\Models\Academic\SchoolClass;
use Illuminate\Support\Facades\Auth;

class GradingPolicyManager extends Component
{
    public $school_class_id = '';

    public $grade_label, $min_percent, $max_percent;
    public $policy_id;
    public $isModalOpen = false;

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.grading-policy-manager', [
            'policies' => GradingPolicy::where('tenant_id', $tenantId)
                ->where('school_class_id', $this->school_class_id ?: null)
                ->orderByDesc('min_percent')
                ->get(),
            'classes' => SchoolClass::where('tenant_id', $tenantId)->orderBy('numeric_value')->get(),
        ])->layout('layouts.app');
    }

    public function openModal() { $this->isModalOpen = true; }
    public function closeModal() { $this->isModalOpen = false; $this->resetFields(); }

    private function resetFields()
    {
        $this->grade_label = '';
        $this->min_percent = '';
        $this->max_percent = '';
        $this->policy_id = '';
    }

    public function edit($id)
    {
        $policy = GradingPolicy::findOrFail($id);
        $this->policy_id = $policy->id;
        $this->grade_label = $policy->grade_label;
        $this->min_percent = $policy->min_percent;
        $this->max_percent = $policy->max_percent;
        $this->openModal();
    }

    public function save()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $this->validate([
            'grade_label' => 'required|string|max:20',
            'min_percent' => 'required|numeric|min:0|max:100',
            'max_percent' => 'required|numeric|min:0|max:100|gte:min_percent',
        ]);

        $overlapping = GradingPolicy::where('tenant_id', $tenantId)
            ->where('school_class_id', $this->school_class_id ?: null)
            ->where('id', '!=', $this->policy_id ?: 0)
            ->where('min_percent', '<=', $this->max_percent)
            ->where('max_percent', '>=', $this->min_percent)
            ->exists();

        if ($overlapping) {
            $this->addError('min_percent', 'This percentage range overlaps with an existing grade band for this scope.');
            return;
        }

        GradingPolicy::updateOrCreate(
            ['id' => $this->policy_id],
            [
                'school_class_id' => $this->school_class_id ?: null,
                'grade_label' => $this->grade_label,
                'min_percent' => $this->min_percent,
                'max_percent' => $this->max_percent,
            ]
        );

        session()->flash('message', 'Grading policy saved successfully.');
        $this->closeModal();
    }

    public function delete($id)
    {
        GradingPolicy::findOrFail($id)->delete();
        session()->flash('message', 'Grading policy deleted successfully.');
    }
}
