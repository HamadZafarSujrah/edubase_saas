<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Section;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ClassManager extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';

    // Form fields for Class
    public $name, $numeric_value, $is_active = true;
    public $class_id;
    
    // Form fields for Section
    public $managingSectionsFor = null;
    public $section_name, $room_number, $capacity = 30;
    
    public $isClassModalOpen = false;
    public $isSectionModalOpen = false;

    public function render()
    {
        return view('livewire.class-manager', [
            'classes' => SchoolClass::withCount('sections')->orderBy('numeric_value', 'asc')->paginate(10),
            'activeSections' => $this->managingSectionsFor 
                ? Section::where('school_class_id', $this->managingSectionsFor->id)->get() 
                : []
        ])->layout('layouts.app');
    }

    // --- CLASS MANAGEMENT ---
    
    public function openClassModal() { $this->isClassModalOpen = true; }
    public function closeClassModal() { $this->isClassModalOpen = false; $this->resetClassFields(); }

    private function resetClassFields()
    {
        $this->name = '';
        $this->numeric_value = '';
        $this->is_active = true;
        $this->class_id = '';
    }

    public function editClass($id)
    {
        $cls = SchoolClass::findOrFail($id);
        $this->class_id = $cls->id;
        $this->name = $cls->name;
        $this->numeric_value = $cls->numeric_value;
        $this->is_active = $cls->is_active;
        $this->openClassModal();
    }

    public function saveClass()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'numeric_value' => 'nullable|integer',
        ]);

        SchoolClass::updateOrCreate(
            ['id' => $this->class_id],
            [
                'name' => $this->name,
                'numeric_value' => $this->numeric_value === '' ? null : $this->numeric_value,
                'is_active' => $this->is_active
            ]
        );

        session()->flash('message', 'Class saved successfully.');
        $this->closeClassModal();
    }

    public function deleteClass($id)
    {
        SchoolClass::findOrFail($id)->delete();
        session()->flash('message', 'Class deleted successfully.');
    }

    // --- SECTION MANAGEMENT ---

    public function manageSections($classId)
    {
        $this->managingSectionsFor = SchoolClass::findOrFail($classId);
        $this->resetSectionFields();
        $this->isSectionModalOpen = true;
    }

    public function closeSectionModal() 
    { 
        $this->isSectionModalOpen = false; 
        $this->managingSectionsFor = null; 
    }

    private function resetSectionFields()
    {
        $this->section_name = '';
        $this->room_number = '';
        $this->capacity = 30;
    }

    public function saveSection()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $this->validate([
            'section_name' => [
                'required', 'string', 'max:50',
                Rule::unique('sections', 'name')
                    ->where('tenant_id', $tenantId)
                    ->where('school_class_id', $this->managingSectionsFor->id),
            ],
            'room_number' => 'nullable|string|max:50',
            'capacity' => 'required|integer|min:1',
        ], [
            'section_name.unique' => 'This class already has a section named ":input".',
        ]);

        Section::create([
            'school_class_id' => $this->managingSectionsFor->id,
            'name' => $this->section_name,
            'room_number' => $this->room_number,
            'capacity' => $this->capacity,
            'is_active' => true,
        ]);

        $this->resetSectionFields();
        session()->flash('section_message', 'Section added successfully.');
    }

    public function deleteSection($id)
    {
        Section::findOrFail($id)->delete();
        session()->flash('section_message', 'Section deleted.');
    }
}
