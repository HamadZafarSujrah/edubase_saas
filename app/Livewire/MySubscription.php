<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student\Student;
use App\Models\Campus\Campus;
use Illuminate\Support\Facades\Auth;

class MySubscription extends Component
{
    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $tenant = \App\Models\Tenant::with('plan')->find($tenantId);

        $studentCount = Student::count();
        $campusCount = Campus::count();

        return view('livewire.my-subscription', [
            'tenant' => $tenant,
            'studentCount' => $studentCount,
            'campusCount' => $campusCount,
            'studentLimitHit' => $tenant->wouldExceedPlanLimit('students', $studentCount),
            'campusLimitHit' => $tenant->wouldExceedPlanLimit('campuses', $campusCount),
        ])->layout('layouts.app');
    }
}
