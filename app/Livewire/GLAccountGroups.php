<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance\GLAccountGroup;
use Illuminate\Support\Facades\Auth;

class GLAccountGroups extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search_id = '';
    public $search_name = '';
    public $search_class = '';
    public $search_inactive = '';

    public $name;
    public $account_class = 'Assets';
    public $is_inactive = 0;
    public $editing_id = null;

    public function resetForm()
    {
        $this->name = '';
        $this->account_class = 'asset';
        $this->is_inactive = 0;
        $this->editing_id = null;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:3',
            'account_class' => 'required',
        ]);

        if ($this->editing_id) {
            $group = GLAccountGroup::findOrFail($this->editing_id);
            $group->update([
                'name' => $this->name,
                'account_class' => $this->account_class,
                'is_inactive' => $this->is_inactive,
                'updated_by' => Auth::id(),
            ]);
        } else {
            GLAccountGroup::create([
                'tenant_id' => session('tenant_id'),
                'name' => $this->name,
                'account_class' => $this->account_class,
                'is_inactive' => $this->is_inactive,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        $this->resetForm();
        session()->flash('message', 'GL Account Group saved successfully.');
        $this->dispatch('closeModal');
    }

    public function edit($id)
    {
        $group = GLAccountGroup::findOrFail($id);
        $this->editing_id = $group->id;
        $this->name = $group->name;
        $this->account_class = $group->account_class;
        $this->is_inactive = $group->is_inactive;
    }

    public function delete($id)
    {
        GLAccountGroup::destroy($id);
        session()->flash('message', 'Group deleted.');
    }

    public function render()
    {
        $query = GLAccountGroup::with(['creator', 'updater'])
            ->when($this->search_id, fn($q) => $q->where('id', 'LIKE', "%{$this->search_id}%"))
            ->when($this->search_name, fn($q) => $q->where('name', 'LIKE', "%{$this->search_name}%"))
            ->when($this->search_class, fn($q) => $q->where('account_class', $this->search_class))
            ->when($this->search_inactive !== '', fn($q) => $q->where('is_inactive', $this->search_inactive))
            ->orderBy('id', 'asc');

        return view('livewire.gl-account-groups', [
            'groups' => $query->paginate(15)
        ])->layout('layouts.app');
    }
}

