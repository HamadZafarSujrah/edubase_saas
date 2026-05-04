<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance\GLAccount;
use App\Models\Finance\GLAccountGroup;
use Illuminate\Support\Facades\Auth;

class GLAccounts extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search_id = '';
    public $search_code = '';
    public $search_name = '';
    public $search_group = '';
    public $search_class = '';
    public $search_inactive = '';

    public $code;
    public $name;
    public $group_id;
    public $is_inactive = 0;
    public $editing_id = null;

    public function resetFields()
    {
        $this->code = '';
        $this->name = '';
        $this->group_id = '';
        $this->is_inactive = 0;
        $this->editing_id = null;
    }

    public function save()
    {
        $this->validate([
            'code' => 'required',
            'name' => 'required',
            'group_id' => 'required',
        ]);

        $group = GLAccountGroup::findOrFail($this->group_id);

        if ($this->editing_id) {
            $account = GLAccount::findOrFail($this->editing_id);
            $account->update([
                'code' => $this->code,
                'name' => $this->name,
                'group_id' => $this->group_id,
                'type' => strtolower($group->account_class),
                'is_inactive' => $this->is_inactive,
                'updated_by' => Auth::id(),
            ]);
        } else {
            GLAccount::create([
                'tenant_id' => session('tenant_id'),
                'group_id' => $this->group_id,
                'code' => $this->code,
                'name' => $this->name,
                'type' => strtolower($group->account_class),
                'is_inactive' => $this->is_inactive,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        $this->resetFields();
        session()->flash('message', 'GL Account saved successfully.');
        $this->dispatch('closeModal');
    }

    public function edit($id)
    {
        $account = GLAccount::findOrFail($id);
        $this->editing_id = $account->id;
        $this->code = $account->code;
        $this->name = $account->name;
        $this->group_id = $account->group_id;
        $this->is_inactive = $account->is_inactive;
    }

    public function delete($id)
    {
        GLAccount::destroy($id);
        session()->flash('message', 'Account deleted.');
    }

    public function render()
    {
        $query = GLAccount::with(['group', 'creator'])
            ->when($this->search_id, fn($q) => $q->where('id', 'LIKE', "%{$this->search_id}%"))
            ->when($this->search_code, fn($q) => $q->where('code', 'LIKE', "%{$this->search_code}%"))
            ->when($this->search_name, fn($q) => $q->where('name', 'LIKE', "%{$this->search_name}%"))
            ->when($this->search_group, fn($q) => $q->where('group_id', $this->search_group))
            ->when($this->search_class, fn($q) => $q->whereHas('group', fn($sq) => $sq->where('account_class', $this->search_class)))
            ->when($this->search_inactive !== '', fn($q) => $q->where('is_inactive', $this->search_inactive));

        return view('livewire.gl-accounts', [
            'accounts' => $query->paginate(15),
            'groups' => GLAccountGroup::where('is_inactive', 0)->orderBy('account_class')->get()->groupBy('account_class')
        ])->layout('layouts.app');
    }
}

