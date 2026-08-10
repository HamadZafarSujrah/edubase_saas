<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Communication\SmsTemplate;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class SmsTemplateManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $name, $category = 'general', $body;
    public $template_id;
    public $isModalOpen = false;

    public function render()
    {
        return view('livewire.sms-template-manager', [
            'templates' => SmsTemplate::orderBy('name')->paginate(10),
        ])->layout('layouts.app');
    }

    public function openModal() { $this->isModalOpen = true; }
    public function closeModal() { $this->isModalOpen = false; $this->resetFields(); }

    private function resetFields()
    {
        $this->name = '';
        $this->category = 'general';
        $this->body = '';
        $this->template_id = '';
    }

    public function edit($id)
    {
        $template = SmsTemplate::findOrFail($id);
        $this->template_id = $template->id;
        $this->name = $template->name;
        $this->category = $template->category;
        $this->body = $template->body;
        $this->openModal();
    }

    public function save()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $this->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('sms_templates', 'name')->where('tenant_id', $tenantId)->ignore($this->template_id),
            ],
            'category' => 'required|string|max:50',
            'body' => 'required|string|max:640',
        ], [
            'name.unique' => 'A template named ":input" already exists.',
        ]);

        SmsTemplate::updateOrCreate(
            ['id' => $this->template_id],
            ['name' => $this->name, 'category' => $this->category, 'body' => $this->body]
        );

        session()->flash('message', 'SMS template saved successfully.');
        $this->closeModal();
    }

    public function delete($id)
    {
        SmsTemplate::findOrFail($id)->delete();
        session()->flash('message', 'SMS template deleted successfully.');
    }
}
