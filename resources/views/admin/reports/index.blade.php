@extends('layouts.admin')

@section('title', 'Compliance & SLA Reports')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold py-1 mb-1"><span class="text-muted fw-light">Reporting /</span> Management & SBP SLA Analytics</h4>
    <p class="text-muted mb-0 small">Audit queue metrics, teller efficiency, and SBP circular compliance.</p>
  </div>
  <a href="{{ route('admin.reports.exportCsv', ['branch_id' => $branchId, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-outline-success">
    <i class="bx bx-download me-1"></i> Export Full CSV Report
  </a>
</div>

<!-- Filters Bar -->
<div class="card mb-4 shadow-sm">
  <div class="card-body py-3">
    <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-3 align-items-end">
      <div class="col-md-3">
        <label class="form-label small fw-semibold">Bank Branch</label>
        <select name="branch_id" class="form-select form-select-sm">
          <option value="">-- All Branches --</option>
          @foreach($branches as $b)
            <option value="{{ $b->id }}" {{ $b->id == $branchId ? 'selected' : '' }}>{{ $b->code }} - {{ $b->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small fw-semibold">Start Date</label>
        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
      </div>
      <div class="col-md-3">
        <label class="form-label small fw-semibold">End Date</label>
        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm w-100">
          <i class="bx bx-filter-alt me-1"></i> Apply Filter
        </button>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
      </div>
    </form>
  </div>
</div>

<!-- Key Performance Summary KPI Cards -->
<div class="row g-3 mb-4">
  <div class="col-md-2 col-sm-4 col-6">
    <div class="card text-center p-3">
      <div class="text-muted small">Total Generated</div>
      <h4 class="mb-0 fw-bold text-primary">{{ $totalIssued }}</h4>
    </div>
  </div>
  <div class="col-md-2 col-sm-4 col-6">
    <div class="card text-center p-3">
      <div class="text-muted small">Completed</div>
      <h4 class="mb-0 fw-bold text-success">{{ $totalServed }}</h4>
    </div>
  </div>
  <div class="col-md-2 col-sm-4 col-6">
    <div class="card text-center p-3">
      <div class="text-muted small">No-Shows</div>
      <h4 class="mb-0 fw-bold text-danger">{{ $totalNoShow }}</h4>
    </div>
  </div>
  <div class="col-md-2 col-sm-4 col-6">
    <div class="card text-center p-3">
      <div class="text-muted small">Avg. Wait Time</div>
      <h4 class="mb-0 fw-bold text-dark">{{ round($avgWaitSecs / 60, 1) }} <span class="fs-tiny">min</span></h4>
    </div>
  </div>
  <div class="col-md-2 col-sm-4 col-6">
    <div class="card text-center p-3">
      <div class="text-muted small">Avg. Service Time</div>
      <h4 class="mb-0 fw-bold text-dark">{{ round($avgServeSecs / 60, 1) }} <span class="fs-tiny">min</span></h4>
    </div>
  </div>
  <div class="col-md-2 col-sm-4 col-6">
    <div class="card text-center p-3 border-primary border">
      <div class="text-primary small fw-semibold">SBP SLA Score</div>
      <h4 class="mb-0 fw-bold text-primary">{{ $sbpSlaPercentage }}%</h4>
    </div>
  </div>
</div>

<!-- Teller Productivity Section -->
<div class="card mb-4">
  <div class="card-header border-bottom pb-3">
    <h5 class="mb-0 fw-bold"><i class="bx bx-user-check me-2 text-primary"></i>Teller Productivity & Transaction Throughput</h5>
  </div>
  <div class="card-body pt-3">
    <div class="row g-3">
      @foreach($tellerStats as $t)
        <div class="col-md-4">
          <div class="p-3 border rounded d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
              <div class="avatar avatar-md me-3">
                <span class="avatar-initial rounded-circle bg-label-primary font-monospace fw-bold">
                  {{ substr($t->name, 0, 1) }}
                </span>
              </div>
              <div>
                <div class="fw-bold">{{ $t->name }}</div>
                <small class="text-muted">{{ $t->assignedCounter->name ?? 'Teller Desk' }}</small>
              </div>
            </div>
            <div class="text-end">
              <span class="badge bg-label-success fs-6">{{ $t->served_tokens_count }}</span>
              <small class="d-block text-muted" style="font-size: 10px;">Served</small>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>

<!-- Detailed Audit Log Table -->
<div class="card shadow-sm">
  <div class="card-header border-bottom pb-3">
    <h5 class="mb-0 fw-bold"><i class="bx bx-table me-2 text-primary"></i>Detailed Token Transaction Audit Log</h5>
  </div>
  <div class="table-responsive text-nowrap">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Token #</th>
          <th>Branch</th>
          <th>Service</th>
          <th>Priority Tier</th>
          <th>Customer</th>
          <th>Counter & Teller</th>
          <th>Wait / Serve Time</th>
          <th>Txn Amount</th>
          <th>Status</th>
          <th>Date & Time</th>
        </tr>
      </thead>
      <tbody>
        @forelse($tokens as $t)
          <tr>
            <td>
              <strong class="text-primary fs-6">{{ $t->token_number }}</strong>
            </td>
            <td>
              <span class="badge bg-light text-dark border">{{ $t->branch->code ?? '' }}</span>
            </td>
            <td>
              <span class="badge bg-label-{{ $t->serviceCategory->color ?? 'primary' }}">
                {{ $t->serviceCategory->name ?? '' }}
              </span>
            </td>
            <td>
              <span class="badge bg-label-{{ $t->priorityCategory->badge_color ?? 'secondary' }}">
                {{ $t->priorityCategory->name ?? 'Regular' }}
              </span>
            </td>
            <td>
              <div class="fw-semibold">{{ $t->customer_name }}</div>
              <small class="text-muted font-monospace">{{ $t->customer_cnic ?? $t->customer_phone ?? 'Walk-in' }}</small>
            </td>
            <td>
              <div>{{ $t->counter->name ?? 'Unassigned' }}</div>
              <small class="text-muted">{{ $t->servedBy->name ?? '-' }}</small>
            </td>
            <td>
              <small class="d-block text-muted">Wait: <strong>{{ round($t->waiting_time_seconds / 60, 1) }}m</strong></small>
              <small class="d-block text-muted">Serve: <strong>{{ round($t->serving_time_seconds / 60, 1) }}m</strong></small>
            </td>
            <td>
              @if($t->transaction_amount)
                <span class="fw-bold text-success">PKR {{ number_format($t->transaction_amount, 2) }}</span>
              @else
                <span class="text-muted">-</span>
              @endif
            </td>
            <td>
              {!! $t->status_badge !!}
            </td>
            <td>
              <small class="text-muted">{{ $t->created_at->format('M d, Y h:i A') }}</small>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="10" class="text-center py-4 text-muted">No records match the selected filter.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($tokens->hasPages())
    <div class="card-footer py-2">
      {{ $tokens->withQueryString()->links() }}
    </div>
  @endif
</div>
@endsection
