<div class="container-fluid py-4 min-vh-100 bg-white">
    <!-- Toolbar: Print only -->
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <div>
            <h5 class="mb-0 fw-bold text-dark">Chart of Accounts</h5>
            <small class="text-muted">Read-only report — manage accounts from <a href="/gl-accounts" class="text-primary">GL Accounts</a></small>
        </div>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fas fa-print me-1"></i> Print
        </button>
    </div>

    <!-- The Report Container -->
    <div class="mx-auto shadow-sm border p-5 bg-white" style="max-width: 900px; border-radius: 8px;">
        <div class="text-center mb-5 border-bottom pb-4">
            <h2 class="fw-bold text-dark mb-1">Chart of Accounts</h2>
            <div class="text-muted small">Alhikmah Higher Secondary School</div>
        </div>

        @if (session()->has('message'))
            <div class="alert alert-success border-0 shadow-sm mb-4 d-print-none">
                {{ session('message') }}
            </div>
        @endif

        <div class="coa-report">
            @foreach($grouped_data as $class => $groups)
                @if(count($groups) > 0)
                    <!-- Main Category (Asset, Income, etc.) -->
                    <div class="coa-category py-1 px-3 mb-1 bg-light fw-bold text-uppercase small text-dark border-top border-bottom" style="letter-spacing: 1px;">
                        {{ ucfirst($class) }}
                    </div>

                    @foreach($groups as $group)
                        <!-- Group Header (1 Current Assets) -->
                        <div class="row g-0 py-2 border-bottom align-items-center">
                            <div class="col-1 text-center fw-bold text-dark">{{ $group->id }}</div>
                            <div class="col-11 ps-3 fw-bold text-dark text-uppercase small">{{ $group->name }}</div>
                        </div>

                        <!-- Individual Accounts -->
                        @foreach($group->accounts as $account)
                        <div class="row g-0 py-1 border-bottom-subtle hover-row group-hover-parent">
                            <div class="col-2 text-center text-muted small pe-4 border-end">{{ $account->code }}</div>
                            <div class="col-8 ps-5 small text-dark fw-medium">{{ $account->name }}</div>
                            <div class="col-2 text-end pe-3 d-print-none">
                                <button wire:click="editAccount({{ $account->id }})" data-bs-toggle="modal" data-bs-target="#editAccountModal" class="btn btn-link btn-sm p-0 tiny text-decoration-none text-primary opacity-0 group-hover-visible">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <span class="mx-1 text-muted opacity-25 group-hover-visible">|</span>
                                <button wire:click="deleteAccount({{ $account->id }})" class="btn btn-link btn-sm p-0 tiny text-decoration-none text-danger opacity-0 group-hover-visible" onclick="confirm('Delete account?') || event.stopImmediatePropagation()">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                        
                        <div class="py-2"></div>
                    @endforeach
                    <div class="py-3"></div>
                @endif
            @endforeach
        </div>
    </div>

<style>
    .coa-report { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
    .border-bottom-subtle { border-bottom: 1px solid #f8f9fa; }
    .hover-row:hover { background-color: #fbfbfc; }
    .tiny { font-size: 0.7rem; }
    .extra-small { font-size: 0.75rem; }
    .group-hover-visible { transition: opacity 0.2s ease; opacity: 0; }
    .hover-row:hover .group-hover-visible { opacity: 1; }
    
    @media print {
        .d-print-none { display: none !important; }
        .shadow-sm { shadow: none !important; }
        .border { border: none !important; }
        .p-5 { padding: 0 !important; }
    }
</style>
</div>

