<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\General\Notice;
use App\Models\Campus\Campus;
use Illuminate\Support\Facades\Auth;

class NoticeManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $title, $body, $campus_id, $expiry_date, $is_active = true;
    public $notice_id;
    public $isModalOpen = false;

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.notice-manager', [
            'notices' => Notice::with('campus')->where('tenant_id', $tenantId)->latest('published_at')->paginate(10),
            'campuses' => Campus::where('tenant_id', $tenantId)->orderBy('name')->get(),
        ])->layout('layouts.app');
    }

    public function openModal() { $this->isModalOpen = true; }
    public function closeModal() { $this->isModalOpen = false; $this->resetFields(); }

    private function resetFields()
    {
        $this->title = '';
        $this->body = '';
        $this->campus_id = '';
        $this->expiry_date = '';
        $this->is_active = true;
        $this->notice_id = '';
    }

    public function edit($id)
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $notice = Notice::where('tenant_id', $tenantId)->findOrFail($id);
        $this->notice_id = $notice->id;
        $this->title = $notice->title;
        $this->body = $notice->body;
        $this->campus_id = $notice->campus_id;
        $this->expiry_date = $notice->expiry_date?->format('Y-m-d');
        $this->is_active = $notice->is_active;
        $this->openModal();
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'campus_id' => 'nullable|exists:campuses,id',
            'expiry_date' => 'nullable|date',
        ]);

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        Notice::updateOrCreate(
            ['id' => $this->notice_id, 'tenant_id' => $tenantId],
            [
                'title' => $this->title,
                'body' => $this->body,
                'campus_id' => $this->campus_id ?: null,
                'expiry_date' => $this->expiry_date ?: null,
                'is_active' => $this->is_active,
                'published_by' => Auth::id(),
                'published_at' => $this->notice_id ? Notice::find($this->notice_id)->published_at : now(),
            ]
        );

        session()->flash('message', 'Notice saved successfully.');
        $this->closeModal();
    }

    public function delete($id)
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        Notice::where('tenant_id', $tenantId)->findOrFail($id)->delete();
        session()->flash('message', 'Notice deleted successfully.');
    }
}
