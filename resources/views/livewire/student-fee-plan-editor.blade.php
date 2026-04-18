<div class="container-fluid">
    <!-- Header Box (Matches your Screenshot 2) -->
    <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
        <div class="bg-warning bg-opacity-10 py-3 text-center border-bottom border-warning border-opacity-25">
            <h4 class="mb-0 fw-bold">Update Fee Plan</h4>
        </div>
        <div class="card-body bg-light p-4">
            <div class="row g-3 text-center small">
                <div class="col-md-3">
                    <span class="d-block text-muted text-uppercase mb-1">Student Name</span>
                    <span class="fw-bold fs-6">{{ $student->first_name }} {{ $student->last_name }}</span>
                </div>
                <div class="col-md-3 border-start">
                    <span class="d-block text-muted text-uppercase mb-1">Father Name</span>
                    <span class="fw-bold fs-6">{{ $student->father_name }}</span>
                </div>
                <div class="col-md-3 border-start">
                    <span class="d-block text-muted text-uppercase mb-1">Admission ID (SID)</span>
                    <span class="fw-bold fs-6 text-primary">{{ $student->id }}</span>
                </div>
                <div class="col-md-3 border-start">
                    <span class="d-block text-muted text-uppercase mb-1 fw-bold tiny">Assigned Plan</span>
                    @if($student->fee_plan_id)
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="fw-bold fs-6 text-success me-2">{{ $student->feePlan->name }}</span>
                            <select wire:model.live="new_fee_plan_id" class="form-select form-select-sm border-warning py-0" style="width: auto; font-size: 0.7rem;">
                                <option value="">Re-assign...</option>
                                @foreach($fee_plans as $fp) <option value="{{ $fp->id }}">{{ $fp->name }}</option> @endforeach
                            </select>
                        </div>
                    @else
                        <div class="px-2">
                            <select wire:model.live="new_fee_plan_id" class="form-select form-select-sm border-danger">
                                <option value="">Select Plan to Assign</option>
                                @foreach($fee_plans as $fp) <option value="{{ $fp->id }}">{{ $fp->name }}</option> @endforeach
                            </select>
                            <span class="tiny text-danger fw-bold d-block mt-1">No Plan Assigned</span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="row g-3 text-center small mt-2 border-top pt-3">
                <div class="col-md-3">
                    <label class="d-block text-muted text-uppercase mb-1 fw-bold tiny">With Effect From</label>
                    <select wire:model.live="effect_from" class="form-select form-select-sm border-primary">
                        <option value="">Select Starting Month</option>
                        @foreach($available_months as $m)
                            <option value="{{ $m['val'] }}">{{ $m['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 border-start">
                    <span class="d-block text-muted text-uppercase mb-1">Campus</span>
                    <span class="fw-bold">{{ $student->campus->name ?? 'N/A' }}</span>
                </div>
                <div class="col-md-3">
                    <span class="d-block text-muted text-uppercase mb-1">Class</span>
                    <span class="fw-bold">{{ $student->schoolClass->name ?? 'N/A' }}</span>
                </div>
                <div class="col-md-3">
                    <span class="d-block text-muted text-uppercase mb-1">Section</span>
                    <span class="fw-bold">{{ $student->section->name ?? 'N/A' }}</span>
                </div>
                <div class="col-md-3">
                    <span class="d-block text-muted text-uppercase mb-1">Admission Date</span>
                    <span class="fw-bold">{{ $student->admission_date ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('message') }}</div>
    @endif

    <!-- ONE-TIME CHARGES SECTION (Admission, Security, etc.) -->
    @php 
        $oneTimeItems = array_filter($items, fn($i) => $i['is_first_time']);
        $recurringItems = array_filter($items, fn($i) => !$i['is_first_time']);
    @endphp

    @if(count($oneTimeItems) > 0)
        <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
            <div class="bg-primary bg-opacity-10 py-2 px-4 border-bottom border-primary border-opacity-25">
                <h6 class="mb-0 fw-bold small text-uppercase">One-Time / Admission Charges</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered mb-0 align-middle text-center">
                    <thead class="bg-light small">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th class="text-start">Fee Particular</th>
                            <th style="width: 200px;">Actual Fee</th>
                            <th style="width: 200px;">Discount</th>
                            <th style="width: 200px; background-color: #f0fff0;">Fee After Discount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($oneTimeItems as $id => $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-start fw-bold">{{ $item['particular_name'] }}</td>
                                <td><input type="number" wire:model.live="items.{{ $id }}.actual_fee" class="form-control text-center border-0 bg-light"></td>
                                <td><input type="number" wire:model.live="items.{{ $id }}.discount" class="form-control text-center border-0 bg-light text-danger fw-bold"></td>
                                <td class="fw-bold text-success" style="background-color: #f0fff0;">{{ number_format($item['actual_fee'] - $item['discount'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- RECURRING MONTHLY FEES -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="bg-success bg-opacity-10 py-2 px-4 border-bottom border-success border-opacity-25 d-flex justify-content-between">
            <h6 class="mb-0 fw-bold small text-uppercase">Monthly Repeating Fees (Recurring)</h6>
            <span class="tiny text-muted fw-bold text-uppercase">Monthly Matrix Layout</span>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered mb-0 align-middle text-center">
                <thead class="bg-dark text-white text-uppercase small">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th class="text-start">Fee Particular</th>
                        <th style="width: 120px;">Actual Fee</th>
                        <th style="width: 120px;">Discount</th>
                        <th style="width: 120px;" class="bg-success bg-opacity-10 text-success">Net Fee</th>
                        @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $m)
                            <th class="small py-3">{{ $m }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php $totalActual = 0; $totalDiscount = 0; $totalFinal = 0; @endphp
                    
                    {{-- Calculate Total correctly from ALL items including one-time --}}
                    @foreach($items as $id => $item)
                        @php 
                            $afterDiscount = $item['actual_fee'] - $item['discount'];
                            $totalActual += $item['actual_fee'];
                            $totalDiscount += $item['discount'];
                            $totalFinal += $afterDiscount;
                        @endphp
                    @endforeach

                    @forelse($recurringItems as $id => $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="text-start fw-bold">{{ $item['particular_name'] }}</td>
                            <td><input type="number" wire:model.live="items.{{ $id }}.actual_fee" class="form-control text-center border-0 bg-light px-1"></td>
                            <td><input type="number" wire:model.live="items.{{ $id }}.discount" class="form-control text-center border-0 bg-light text-danger fw-bold px-1"></td>
                            <td class="bg-success bg-opacity-10 fw-bold text-success small">
                                {{ number_format($item['actual_fee'] - $item['discount'], 2) }}
                            </td>
                            @foreach(['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'] as $m)
                                <td><input type="checkbox" wire:model="items.{{ $id }}.{{ $m }}" class="form-check-input"></td>
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="17" class="text-center py-4 text-muted small">No recurring items found for this plan.</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-danger bg-opacity-10 fw-bold">
                    <tr>
                        <td colspan="2" class="text-end py-3">GRAND TOTAL (One-Time + Recurring):</td>
                        <td>{{ number_format($totalActual, 2) }}</td>
                        <td class="text-danger">{{ number_format($totalDiscount, 2) }}</td>
                        <td class="text-success fs-5">{{ number_format($totalFinal, 2) }}</td>
                        <td colspan="12"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-4 text-center">
            <button wire:click="save" class="btn btn-success px-5 py-2 fw-bold rounded-pill shadow-lg">
                <i class="fas fa-check-circle me-1"></i> Update Student Fee Plan
            </button>
            <a href="/view-edit-fee-plans" class="btn btn-light px-4 border rounded-pill ms-2">
                <i class="fas fa-list me-1"></i> Back to Fee Hub
            </a>
        </div>
    </div>
</div>
