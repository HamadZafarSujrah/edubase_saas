<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\General\PtmSchedule;
use App\Models\Campus\Campus;
use App\Models\Academic\SchoolClass;
use Illuminate\Support\Facades\Auth;

class PtmScheduler extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $title, $campus_id, $school_class_id, $meeting_date, $start_time, $end_time, $description;
    public $ptm_id;
    public $isModalOpen = false;

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.ptm-scheduler', [
            'meetings' => PtmSchedule::with(['campus', 'schoolClass'])->where('tenant_id', $tenantId)->orderBy('meeting_date', 'desc')->paginate(10),
            'campuses' => Campus::where('tenant_id', $tenantId)->orderBy('name')->get(),
            'classes' => SchoolClass::where('tenant_id', $tenantId)->orderBy('numeric_value')->get(),
        ])->layout('layouts.app');
    }

    public function openModal() { $this->isModalOpen = true; }
    public function closeModal() { $this->isModalOpen = false; $this->resetFields(); }

    private function resetFields()
    {
        $this->title = '';
        $this->campus_id = '';
        $this->school_class_id = '';
        $this->meeting_date = '';
        $this->start_time = '';
        $this->end_time = '';
        $this->description = '';
        $this->ptm_id = '';
    }

    public function edit($id)
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $ptm = PtmSchedule::where('tenant_id', $tenantId)->findOrFail($id);
        $this->ptm_id = $ptm->id;
        $this->title = $ptm->title;
        $this->campus_id = $ptm->campus_id;
        $this->school_class_id = $ptm->school_class_id;
        $this->meeting_date = $ptm->meeting_date->format('Y-m-d');
        // DB TIME columns come back as "H:i:s" -- trim to "H:i" to match the
        // date_format:H:i validation rule and the <input type="time"> field.
        $this->start_time = $ptm->start_time ? substr($ptm->start_time, 0, 5) : '';
        $this->end_time = $ptm->end_time ? substr($ptm->end_time, 0, 5) : '';
        $this->description = $ptm->description;
        $this->openModal();
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'campus_id' => 'nullable|exists:campuses,id',
            'school_class_id' => 'nullable|exists:school_classes,id',
            'meeting_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'description' => 'nullable|string',
        ]);

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $data = [
            'title' => $this->title,
            'campus_id' => $this->campus_id ?: null,
            'school_class_id' => $this->school_class_id ?: null,
            'meeting_date' => $this->meeting_date,
            'start_time' => $this->start_time ?: null,
            'end_time' => $this->end_time ?: null,
            'description' => $this->description,
        ];

        if (!$this->ptm_id) {
            $data['created_by'] = Auth::id();
        }

        PtmSchedule::updateOrCreate(['id' => $this->ptm_id, 'tenant_id' => $tenantId], $data);

        session()->flash('message', 'Parent-Teacher Meeting scheduled successfully.');
        $this->closeModal();
    }

    public function delete($id)
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        PtmSchedule::where('tenant_id', $tenantId)->findOrFail($id)->delete();
        session()->flash('message', 'Meeting deleted.');
    }
}
