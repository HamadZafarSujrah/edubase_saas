<div class="container-fluid">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="fas fa-th me-2 text-primary"></i> Fee Particulars in Fee Plan List</h5>
            <div class="d-flex align-items-center">
                <label class="small fw-bold me-2 mb-0">Select Campus:</label>
                <select wire:model.live="campus_id" class="form-select form-select-sm" style="width: 200px;">
                    @foreach($campuses as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                </select>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0 align-middle">
                    <thead class="bg-light text-center small fw-bold">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th class="text-start">Fee Plan List</th>
                            <th class="bg-warning bg-opacity-10" style="width: 120px;">All Particulars</th>
                            @foreach($particulars as $p)
                                <th>{{ $p->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach($plans as $plan)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-start fw-bold text-danger">{{ $plan->name }}</td>
                                <td class="bg-light">
                                    <input type="checkbox" class="form-check-input"
                                        wire:click="toggleAllForPlan({{ $plan->id }})"
                                        onclick="return confirm('Map every particular in this list to {{ $plan->name }}?');">
                                </td>
                                @foreach($particulars as $part)
                                    @php 
                                        $isSelected = isset($mappings[$plan->id]) && in_array($part->id, $mappings[$plan->id]);
                                    @endphp
                                    <td>
                                        <div class="form-check d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox" 
                                                wire:click="toggleMapping({{ $plan->id }}, {{ $part->id }})"
                                                @if($isSelected) checked @endif>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
