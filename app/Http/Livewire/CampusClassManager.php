<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Campus\Campus;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\CampusClass;

class CampusClassManager extends Component
{
    public $campuses;
    public $selectedCampus = null;
    
    // An array of class IDs that are checked/selected for the current campus
    public $assignedClasses = [];

    public function mount()
    {
        $this->campuses = Campus::where('is_active', true)->get();
        // Auto-select the first campus if available
        if ($this->campuses->count() > 0) {
            $this->selectCampus($this->campuses->first()->id);
        }
    }

    public function selectCampus($campusId)
    {
        $this->selectedCampus = Campus::findOrFail($campusId);
        
        // Load the currently assigned classes for this specific campus
        $this->assignedClasses = CampusClass::where('campus_id', $this->selectedCampus->id)
                                    ->pluck('school_class_id')
                                    ->toArray();
    }

    public function toggleClassAssignment($classId)
    {
        if (in_array($classId, $this->assignedClasses)) {
            // Remove assignment
            CampusClass::where('campus_id', $this->selectedCampus->id)
                       ->where('school_class_id', $classId)
                       ->delete();
                       
            // Update array
            $this->assignedClasses = array_diff($this->assignedClasses, [$classId]);
        } else {
            // Add assignment
            CampusClass::create([
                'campus_id' => $this->selectedCampus->id,
                'school_class_id' => $classId
            ]);
            
            // Update array
            $this->assignedClasses[] = $classId;
        }

        session()->flash('message', 'Campus mappings updated securely.');
    }

    public function render()
    {
        return view('livewire.campus-class-manager', [
            // We pull all global classes
            'allClasses' => SchoolClass::where('is_active', true)->orderBy('numeric_value', 'asc')->get()
        ])->layout('layouts.app');
    }
}
