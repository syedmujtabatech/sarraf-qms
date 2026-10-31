@extends('layouts.admin')

@section('title', 'Customer Service History')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold py-1 mb-1"><span class="text-muted fw-light">Customer Relations /</span> Service History & Analytics</h4>
    <p class="text-muted mb-0 small">Maintain and audit customer visits, repeat interactions and service records (SBI RFP Requirement).</p>
  </div>
</div>

<!-- Search Box -->
<div class="card mb-4 shadow-sm border-0">
  <div class="card-body p-4">
    <form method="GET" action="{{ route('admin.customers.history') }}" class="row g-2 align-items-center">
      <div class="col-md-9">
        <div class="input-group input-group-merge">
          <span class="input-group-text"><i class="bx bx-search fs-4"></i></span>
          <input
            type="text"
            name="search"
            class="form-control form-control-lg"
            placeholder="Search by Customer CNIC (e.g. 42101-...), Phone Number, or Full Name..."
            value="{{ $search }}"
            required>
        </div>
      </div>
      <div class="col-md-3">
        <button type="submit" class="btn btn-primary btn-lg w-100">
          <i class="bx bx-user-pin me-1"></i> Retrieve History
        </button>
      </div>
    </form>
    <div class="mt-2 text-muted small">
      <strong>Quick Demo Searches:</strong>
      <a href="{{ route('admin.customers.history', ['search' => '42101-1234567-1']) }}" class="badge bg-label-primary text-decoration-none me-1">42101-1234567-1 (Muhammad Rashid)</a>
      <a href="{{ route('admin.customers.history', ['search' => '37405-9876543-1']) }}" class="badge bg-label-info text-decoration-none me-1">37405-9876543-1 (Col Abdul Ghaffar)</a>
      <a href="{{ route('admin.customers.history', ['search' => '42201-5551234-9']) }}" class="badge bg-label-danger text-decoration-none">42201-5551234-9 (Mirza Asad - PWD)</a>
    </div>
  </div>
</div>

@if($customerSummary)
  <!-- Customer Profile Card -->
  <div class="card mb-4 border-0 shadow-sm bg-label-primary">
    <div class="card-body p-4">
      <div class="row align-items-center">
        <div class="col-md-5 d-flex align-items-center mb-3 mb-md-0">
          <div class="avatar avatar-xl me-3">
            <span class="avatar-initial rounded-circle bg-primary text-white fs-3 fw-bold">
              {{ strtoupper(substr($customerSummary['name'], 0, 2)) }}
            </span>
          </div>
          <div>
            <h4 class="fw-bold mb-1 text-primary">{{ $customerSummary['name'] }}</h4>
            <div class="text-muted small">
              <i class="bx bx-id-card me-1"></i> CNIC: <strong>{{ $customerSummary['cnic'] ?? 'N/A' }}</strong> &bull; 
              <i class="bx bx-phone me-1"></i> {{ $customerSummary['phone'] ?? 'N/A' }}
            </div>
          </div>
        </div>

        <div class="col-md-7">
          <div class="row g-2 text-center">
            <div class="col-4">
              <div class="p-2 bg-white rounded shadow-sm">
                <small class="text-muted d-block" style="font-size: 11px;">Total Visits</small>
                <strong class="fs-5 text-dark">{{ $customerSummary['total_visits'] }}</strong>
              </div>
            </div>
            <div class="col-4">
              <div class="p-2 bg-white rounded shadow-sm">
                <small class="text-muted d-block" style="font-size: 11px;">Avg Wait Time</small>
                <strong class="fs-5 text-dark">{{ $customerSummary['avg_wait_mins'] }}m</strong>
              </div>
            </div>
            <div class="col-4">
              <div class="p-2 bg-white rounded shadow-sm">
                <small class="text-muted d-block" style="font-size: 11px;">Total Txn Value</small>
                <strong class="fs-5 text-success">PKR {{ number_format($customerSummary['total_transactions_val'], 0) }}</strong>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Historical Service Timeline -->
  <div class="card shadow-sm">
    <div class="card-header border-bottom pb-3">
      <h5 class="mb-0 fw-bold"><i class="bx bx-history me-2 text-primary"></i>Service History Records</h5>
    </div>
    <div class="table-responsive text-nowrap">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Token</th>
            <th>Date & Time</th>
            <th>Branch</th>
            <th>Service</th>
            <th>Priority Category</th>
            <th>Teller</th>
            <th>Wait / Service Time</th>
            <th>Txn Details</th>
            <th>Customer Feedback</th>
          </tr>
        </thead>
        <tbody>
          @foreach($customerHistory as $item)
            <tr>
              <td><strong class="text-primary">{{ $item->token_number }}</strong></td>
              <td>{{ $item->created_at->format('M d, Y h:i A') }}</td>
              <td><span class="badge bg-light text-dark border">{{ $item->branch->name ?? '' }}</span></td>
              <td>
                <span class="badge bg-label-{{ $item->serviceCategory->color ?? 'primary' }}">
                  {{ $item->serviceCategory->name ?? '' }}
                </span>
              </td>
              <td>
                <span class="badge bg-label-{{ $item->priorityCategory->badge_color ?? 'secondary' }}">
                  {{ $item->priorityCategory->name ?? 'Regular' }}
                </span>
              </td>
              <td>{{ $item->servedBy->name ?? 'Teller Desk' }}</td>
              <td>
                <small class="d-block text-muted">Wait: {{ round($item->waiting_time_seconds / 60, 1) }}m</small>
                <small class="d-block text-muted">Service: {{ round($item->serving_time_seconds / 60, 1) }}m</small>
              </td>
              <td>
                @if($item->transaction_amount)
                  <div class="fw-bold text-success">PKR {{ number_format($item->transaction_amount, 2) }}</div>
                  <small class="text-muted font-monospace">{{ $item->transaction_reference }}</small>
                @endif
                @if($item->notes)
                  <small class="d-block text-muted fst-italic">{{ $item->notes }}</small>
                @endif
              </td>
              <td>
                @if($item->feedback)
                  <span class="badge bg-label-warning">
                    <i class="bx bxs-star text-warning"></i> {{ $item->feedback->rating }}/5
                  </span>
                  @if($item->feedback->comments)
                    <small class="d-block text-muted text-truncate" style="max-width: 150px;">"{{ $item->feedback->comments }}"</small>
                  @endif
                @else
                  <span class="text-muted small">No rating</span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@elseif($search)
  <div class="card text-center p-5">
    <div class="text-muted mb-3">
      <i class="bx bx-user-x fs-1 text-danger"></i>
    </div>
    <h5>No Customer Records Found</h5>
    <p class="text-muted">No historical transactions or visits match the identifier "<strong>{{ $search }}</strong>".</p>
  </div>
@else
  <div class="card text-center p-5 shadow-sm border-0">
    <div class="text-primary mb-3">
      <i class="bx bx-search-alt fs-1"></i>
    </div>
    <h5>Customer Service History Lookup</h5>
    <p class="text-muted mb-0">Enter a customer's CNIC, registered mobile number, or full name above to load their lifetime branch visits and service log.</p>
  </div>
@endif
@endsection
