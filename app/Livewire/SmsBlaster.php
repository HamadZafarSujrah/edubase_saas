<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student\Student;
use App\Models\Campus\Campus;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Section;
use App\Models\Communication\SmsTemplate;
use App\Services\SmsService;
use Illuminate\Support\Facades\Auth;

class SmsBlaster extends Component
{
    public $campus_id = '';
    public $school_class_id = '';
    public $section_id = '';
    public $message = '';
    public $template_id = '';

    public function updatedTemplateId()
    {
        if ($this->template_id) {
            $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
            $template = SmsTemplate::where('tenant_id', $tenantId)->find($this->template_id);
            $this->message = $template->body ?? $this->message;
        }
    }

    public $show_result = false;
    public $sent_count = 0;
    public $skipped_count = 0;

    public function updatedCampusId() { $this->school_class_id = ''; $this->section_id = ''; }
    public function updatedSchoolClassId() { $this->section_id = ''; }

    protected function recipientsQuery()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $query = Student::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->where('send_branded_sms', true);

        if ($this->campus_id) {
            $query->where('campus_id', $this->campus_id);
        }
        if ($this->school_class_id) {
            $query->where('school_class_id', $this->school_class_id);
        }
        if ($this->section_id) {
            $query->where('section_id', $this->section_id);
        }

        return $query;
    }

    public function getRecipientCountProperty()
    {
        return $this->recipientsQuery()
            ->get(['id', 'father_phone', 'guardian_phone', 'mother_phone'])
            ->filter(fn ($s) => !empty($s->primary_phone))
            ->count();
    }

    public function send()
    {
        $this->validate([
            'message' => 'required|string|max:640',
        ]);

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $students = $this->recipientsQuery()->with(['schoolClass', 'section'])->get();

        $sms = new SmsService();
        $sent = 0;
        $skipped = 0;

        foreach ($students as $student) {
            if (empty($student->primary_phone)) {
                $skipped++;
                continue;
            }

            $personalized = str_replace(
                ['{student_name}', '{class}'],
                [$student->full_name, trim(($student->schoolClass->name ?? '') . ' ' . ($student->section->name ?? ''))],
                $this->message
            );

            $sms->sendAndLog([
                'tenant_id'      => $tenantId,
                'student_id'     => $student->id,
                'phone'          => $student->primary_phone,
                'recipient_name' => $student->father_name ?: $student->full_name,
                'message'        => $personalized,
                'type'           => 'blast',
                'sent_by'        => Auth::id(),
            ]);
            $sent++;
        }

        $this->sent_count = $sent;
        $this->skipped_count = $skipped;
        $this->show_result = true;
        $this->message = '';
    }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.sms-blaster', [
            'campuses' => Campus::where('tenant_id', $tenantId)->get(),
            'classes'  => SchoolClass::where('tenant_id', $tenantId)->orderBy('numeric_value')->get(),
            'sections' => $this->school_class_id ? Section::where('tenant_id', $tenantId)->where('school_class_id', $this->school_class_id)->get() : [],
            'templates' => SmsTemplate::where('tenant_id', $tenantId)->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
