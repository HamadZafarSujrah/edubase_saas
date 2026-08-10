<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Student\Certificate;
use Illuminate\Support\Facades\Auth;

class CertificatesLog extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $certificate_type = '';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingCertificateType() { $this->resetPage(); }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $query = Certificate::where('tenant_id', $tenantId)
            ->with(['student', 'issuedBy']);

        if ($this->certificate_type) {
            $query->where('certificate_type', $this->certificate_type);
        }

        if ($this->search) {
            $query->whereHas('student', function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('admission_no', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.certificates-log', [
            'certificates' => $query->latest('issued_date')->paginate(15),
        ])->layout('layouts.app');
    }
}
