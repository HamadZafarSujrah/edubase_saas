<div class="container-fluid">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="fas fa-calendar-alt me-2 text-primary"></i> Particular Amount and Repeating Months</h5>
            <div class="d-flex align-items-center gap-3">
                @if (session()->has('message'))
                    <span class="text-success small fw-bold animate__animated animate__fadeOut animate__delay-2s">{{ session('message') }}</span>
                @endif
                <button wire:click="saveAll" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm">
                    <i class="fas fa-save me-1"></i> Save All Changes
                </button>
                <select wire:model.live="campus_id" class="form-select form-select-sm" style="width: 180px;">
                    @foreach($campuses as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                </select>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0 align-middle text-center small">
                    <thead class="bg-light fw-bold text-muted">
                        <tr>
                            <th>#</th>
                            <th>Campus</th>
                            <th>Fee Plan List</th>
                            <th>Particular</th>
                            <th style="width: 100px;">Amount</th>
                            <th style="width: 80px;">Min Amount</th>
                            <th>First Time</th>
                            @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $m)
                                <th>{{ $m }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $r)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-start">{{ $r->campus->name }}</td>
                                <td class="text-start text-danger fw-bold">{{ $r->feePlan->name }}</td>
                                <td class="text-start fw-bold">{{ $r->particular->name }}</td>
                                <td>
                                    <input type="number" wire:model="editing_amounts.{{ $r->id }}.amount" class="form-control form-control-sm text-center border-0 bg-light">
                                </td>
                                <td>
                                    <input type="number" wire:model="editing_amounts.{{ $r->id }}.min_amount" class="form-control form-control-sm text-center border-0 bg-light">
                                </td>
                                <td>
                                    <input type="checkbox" class="form-check-input" wire:click="toggleFirstTime({{ $r->id }})" @if($r->is_first_time) checked @endif>
                                </td>
                                @foreach(['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'] as $month)
                                    <td>
                                        <input type="checkbox" class="form-check-input p-2" 
                                               wire:click="toggleMonth({{ $r->id }}, '{{ $month }}')"
                                               @if($r->$month) checked @endif>
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="19" class="py-5 text-muted">Please map particulars to plans in the mapping screen first!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<style>
    .form-check-input:checked { background-color: #0d6efd; border-color: #0d6efd; }
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
</style>
</div>

