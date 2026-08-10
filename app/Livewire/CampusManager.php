<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Campus\Campus;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CampusManager extends Component
{
    use WithPagination, AuthorizesRequests;
    
    protected $paginationTheme = 'bootstrap';

    public $name, $address, $contact_email, $contact_phone, $is_active = true;
    public $campus_id;
    public $isModalOpen = false;
    public $search = '';

    protected $queryString = ['search'];

    // We rely on the HasTenant scope so we don't query by tenant_id explicitly!
    
    protected $rules = [
        'name'          => 'required|string|min:3|max:255',
        'address'       => 'required|string|min:10',
        'contact_email' => 'nullable|email|max:255',
        'contact_phone' => 'nullable|string|max:20',
        'is_active'     => 'boolean',
    ];

    public function render()
    {
        $this->authorize('viewAny', Campus::class);

        return view('livewire.campus-manager', [
            'campuses' => Campus::query()
                ->when($this->search, function($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('address', 'like', '%' . $this->search . '%');
                })
                ->latest()
                ->paginate(10),
        ])->layout('layouts.app');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->authorize('create', Campus::class);
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetValidation();
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->address = '';
        $this->contact_email = '';
        $this->contact_phone = '';
        $this->is_active = true;
        $this->campus_id = '';
    }

    public function store()
    {
        if ($this->campus_id) {
            $campus = Campus::findOrFail($this->campus_id);
            $this->authorize('update', $campus);
        } else {
            $this->authorize('create', Campus::class);
        }

        $this->validate();

        if (!$this->campus_id) {
            $tenant = \App\Models\Tenant::find(session('tenant_id') ?? auth()->user()->tenant_id);
            if ($tenant && $tenant->wouldExceedPlanLimit('campuses', Campus::count())) {
                session()->flash('error', "Your institution's plan campus limit has been reached. Please upgrade your plan to add more campuses.");
                $this->closeModal();
                return;
            }
        }

        try {
            Campus::updateOrCreate(
                ['id' => $this->campus_id],
                [
                    'name'          => $this->name,
                    'address'       => $this->address,
                    'contact_email' => $this->contact_email,
                    'contact_phone' => $this->contact_phone,
                    'is_active'     => $this->is_active
                ]
            );

            session()->flash('success', 
                $this->campus_id ? 'Campus Updated Successfully.' : 'Campus Created Successfully.');

            $this->closeModal();
            $this->resetInputFields();
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $campus = Campus::findOrFail($id);
        $this->authorize('update', $campus);
        
        $this->campus_id = $id;
        $this->name = $campus->name;
        $this->address = $campus->address;
        $this->contact_email = $campus->contact_email;
        $this->contact_phone = $campus->contact_phone;
        $this->is_active = $campus->is_active;

        $this->openModal();
    }

    public function delete($id)
    {
        try {
            $campus = Campus::findOrFail($id);
            $this->authorize('delete', $campus);

            // Safety check: Cannot delete if has students
            if ($campus->students()->exists()) {
                session()->flash('error', 'Cannot delete campus. Institutional rule: Close all enrollments first.');
                return;
            }

            $campus->delete();
            session()->flash('success', 'Campus Deleted Successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Operation failed: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        $campus = Campus::findOrFail($id);
        $this->authorize('update', $campus);
        
        $campus->update(['is_active' => !$campus->is_active]);
        session()->flash('success', 'Status toggled successfully.');
    }
}
