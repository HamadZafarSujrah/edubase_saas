<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\General\DiaryEntry;
use App\Models\Campus\Campus;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Section;
use Illuminate\Support\Facades\Auth;

class DiaryManager extends Component
{
    public $campus_id = '';
    public $school_class_id = '';
    public $section_id = '';
    public $date;

    public $subject = '';
    public $content = '';

    public function mount()
    {
        $this->date = date('Y-m-d');
    }

    public function updatedCampusId() { $this->school_class_id = ''; $this->section_id = ''; }
    public function updatedSchoolClassId() { $this->section_id = ''; }

    public function getEntriesProperty()
    {
        if (!$this->section_id) {
            return collect();
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return DiaryEntry::where('tenant_id', $tenantId)
            ->where('section_id', $this->section_id)
            ->where('date', $this->date)
            ->with('createdBy')
            ->latest('id')
            ->get();
    }

    public function addEntry()
    {
        $this->validate([
            'section_id' => 'required|exists:sections,id',
            'date' => 'required|date',
            'content' => 'required|string',
            'subject' => 'nullable|string|max:100',
        ]);

        DiaryEntry::create([
            'section_id' => $this->section_id,
            'date' => $this->date,
            'subject' => $this->subject,
            'content' => $this->content,
            'created_by' => Auth::id(),
        ]);

        $this->subject = '';
        $this->content = '';
        session()->flash('message', 'Diary entry added.');
    }

    public function deleteEntry($id)
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        DiaryEntry::where('tenant_id', $tenantId)->findOrFail($id)->delete();
        session()->flash('message', 'Diary entry deleted.');
    }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.diary-manager', [
            'campuses' => Campus::where('tenant_id', $tenantId)->orderBy('name')->get(),
            'classes' => SchoolClass::where('tenant_id', $tenantId)->orderBy('numeric_value')->get(),
            'sections' => $this->school_class_id ? Section::where('tenant_id', $tenantId)->where('school_class_id', $this->school_class_id)->get() : [],
        ])->layout('layouts.app');
    }
}
