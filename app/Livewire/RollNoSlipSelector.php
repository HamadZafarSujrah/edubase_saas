<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Campus\Campus;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Section;
use Illuminate\Support\Facades\Auth;

class RollNoSlipSelector extends Component
{
    public $campus_id = '';
    public $school_class_id = '';
    public $section_id = '';

    public function updatedCampusId() { $this->school_class_id = ''; $this->section_id = ''; }
    public function updatedSchoolClassId() { $this->section_id = ''; }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.roll-no-slip-selector', [
            'campuses' => Campus::where('tenant_id', $tenantId)->orderBy('name')->get(),
            'classes' => SchoolClass::where('tenant_id', $tenantId)->orderBy('numeric_value')->get(),
            'sections' => $this->school_class_id ? Section::where('tenant_id', $tenantId)->where('school_class_id', $this->school_class_id)->get() : [],
        ])->layout('layouts.app');
    }
}
