<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Student\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class StudentsByUser extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $user_id = '';

    public function updatedUserId() { $this->resetPage(); }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $query = Student::with(['schoolClass', 'section', 'creator'])
            ->where('tenant_id', $tenantId);

        if ($this->user_id) {
            $query->where('created_by', $this->user_id);
        }

        return view('livewire.students-by-user', [
            'students' => $query->latest('id')->paginate(15),
            'users' => User::where('tenant_id', $tenantId)->orderBy('name')->get(),
            'counts' => Student::where('tenant_id', $tenantId)
                ->whereNotNull('created_by')
                ->selectRaw('created_by, count(*) as total')
                ->groupBy('created_by')
                ->with('creator')
                ->get(),
        ])->layout('layouts.app');
    }
}
