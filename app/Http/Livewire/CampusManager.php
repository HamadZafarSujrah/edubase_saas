<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Campus\Campus;
use Livewire\WithPagination;

class CampusManager extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';

    public $name, $address, $contact_email, $contact_phone, $is_active = true;
    public $campus_id;
    public $isModalOpen = false;

    // We rely on the HasTenant scope so we don't query by tenant_id explicitly!
    
    protected $rules = [
        'name' => 'required|string|max:255',
        'address' => 'required|string',
        'contact_email' => 'nullable|email',
        'contact_phone' => 'nullable|string',
        'is_active' => 'boolean',
    ];

    public function render()
    {
        return view('livewire.campus-manager', [
            'campuses' => Campus::paginate(10),
        ])->layout('layouts.app');
    }

    public function create()
    {
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
        $this->validate();

        Campus::updateOrCreate(
            ['id' => $this->campus_id],
            [
                'name' => $this->name,
                'address' => $this->address,
                'contact_email' => $this->contact_email,
                'contact_phone' => $this->contact_phone,
                'is_active' => $this->is_active
            ]
        );

        session()->flash('message', 
            $this->campus_id ? 'Campus Updated Successfully.' : 'Campus Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $campus = Campus::findOrFail($id);
        
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
        Campus::find($id)->delete();
        session()->flash('message', 'Campus Deleted Successfully.');
    }
}
