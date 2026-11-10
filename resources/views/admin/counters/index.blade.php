@extends('layouts.admin')

@section('title', 'Counter Setup')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold py-1 mb-1"><span class="text-muted fw-light">Configuration /</span> Counter Stations</h4>
    <p class="text-muted mb-0 small">Configure teller stations, service routing, and SBP dedicated priority counters.</p>
  </div>
  <div class="d-flex gap-2">
    <!-- Branch Filter -->
    @if($isBranchManager ?? false)
      <span class="badge bg-label-primary px-3 py-2 d-flex align-items-center">
        <i class="bx bx-buildings me-1"></i> {{ $branches->first()->name ?? 'Your Branch' }}
      </span>
    @else
      <form method="GET" action="{{ route('admin.counters.index') }}" class="d-flex align-items-center">
        <select class="form-select form-select-sm" name="branch_id" onchange="this.form.submit()">
          @foreach($branches as $b)
            <option value="{{ $b->id }}" {{ $b->id == $branchId ? 'selected' : '' }}>
              {{ $b->code }} - {{ $b->name }}
            </option>
          @endforeach
        </select>
      </form>
    @endif
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCounterModal">
      <i class="bx bx-plus me-1"></i> Add Counter
    </button>
  </div>
</div>

<div class="card shadow-sm">
  <div class="table-responsive text-nowrap">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Counter #</th>
          <th>Counter Name</th>
          <th>Type / Mode</th>
          <th>Assigned Teller</th>
          <th>Assigned Services</th>
          <th>Status</th>
          <th>Active Token</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($counters as $c)
          <tr>
            <td>
              <span class="badge bg-primary fs-6 px-3">#{{ $c->counter_number }}</span>
            </td>
            <td>
              <div class="fw-semibold">{{ $c->name }}</div>
              <small class="text-muted">{{ $c->branch->name ?? '' }}</small>
            </td>
            <td>
              @if($c->type === 'priority_dedicated')
                <span class="badge bg-label-danger">
                  <i class="bx bx-shield-quarter me-1"></i> SBP Priority Desk
                </span>
              @elseif($c->type === 'cash')
                <span class="badge bg-label-success">
                  <i class="bx bx-money me-1"></i> Cash Counter
                </span>
              @elseif($c->type === 'inquiries')
                <span class="badge bg-label-info">
                  <i class="bx bx-headphone me-1"></i> Inquiry Desk
                </span>
              @else
                <span class="badge bg-label-secondary">General Service</span>
              @endif
            </td>
            <td>
              @if($c->currentUser)
                <div class="d-flex align-items-center">
                  <div class="avatar avatar-xs me-2">
                    <span class="avatar-initial rounded-circle bg-label-primary font-monospace">
                      {{ substr($c->currentUser->name, 0, 1) }}
                    </span>
                  </div>
                  <div>
                    <div class="fw-semibold text-dark small">{{ $c->currentUser->name }}</div>
                  </div>
                </div>
              @else
                <span class="text-muted small">No Teller Logged In</span>
              @endif
            </td>
            <td>
              <div class="d-flex flex-wrap gap-1" style="max-width: 280px;">
                @forelse($c->services as $srv)
                  <span class="badge bg-label-{{ $srv->color }}" style="font-size: 10px;">{{ $srv->prefix }} - {{ $srv->name }}</span>
                @empty
                  <span class="text-muted small">All Services Allowed</span>
                @endforelse
              </div>
            </td>
            <td>
              @if($c->status === 'serving')
                <span class="badge bg-label-primary pulse-dot">Serving</span>
              @elseif($c->status === 'online')
                <span class="badge bg-label-success">Online</span>
              @elseif($c->status === 'break')
                <span class="badge bg-label-warning">Break</span>
              @else
                <span class="badge bg-label-secondary">Offline</span>
              @endif
            </td>
            <td>
              @if($c->currentToken)
                <strong class="text-primary">{{ $c->currentToken->token_number }}</strong>
                <small class="d-block text-muted text-truncate" style="max-width: 100px;">{{ $c->currentToken->customer_name }}</small>
              @else
                <span class="text-muted small">-</span>
              @endif
            </td>
            <td class="text-end">
              <form action="{{ route('admin.counters.toggle', $c->id) }}" method="POST" class="d-inline">
                @csrf
                @if($c->status === 'online' || $c->status === 'serving')
                  <button type="submit" class="btn btn-sm btn-outline-warning" title="Pause / Close Counter">
                    <i class="bx bx-pause me-1"></i> Close
                  </button>
                @else
                  <button type="submit" class="btn btn-sm btn-outline-success" title="Start / Open Counter">
                    <i class="bx bx-play me-1"></i> Start
                  </button>
                @endif
              </form>
              <button class="btn btn-sm btn-icon btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editCounterModal{{ $c->id }}">
                <i class="bx bx-edit"></i>
              </button>
              <form action="{{ route('admin.counters.destroy', $c->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete counter?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-icon btn-outline-danger">
                  <i class="bx bx-trash"></i>
                </button>
              </form>
            </td>
          </tr>

          <!-- Edit Modal -->
          <div class="modal fade" id="editCounterModal{{ $c->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <form action="{{ route('admin.counters.update', $c->id) }}" method="POST">
                  @csrf
                  @method('PUT')
                  <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Edit Counter #{{ $c->counter_number }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="row g-2 mb-3">
                      <div class="col-4">
                        <label class="form-label">Number</label>
                        <input type="text" name="counter_number" class="form-control" value="{{ $c->counter_number }}" required>
                      </div>
                      <div class="col-8">
                        <label class="form-label">Counter Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $c->name }}" required>
                      </div>
                    </div>
                    <div class="row g-2 mb-3">
                      <div class="col-6">
                        <label class="form-label">Counter Type</label>
                        <select name="type" class="form-select" required>
                          <option value="general" {{ $c->type == 'general' ? 'selected' : '' }}>General</option>
                          <option value="priority_dedicated" {{ $c->type == 'priority_dedicated' ? 'selected' : '' }}>SBP Dedicated Priority (PWD/Pensioners)</option>
                          <option value="cash" {{ $c->type == 'cash' ? 'selected' : '' }}>Cash Counter</option>
                          <option value="inquiries" {{ $c->type == 'inquiries' ? 'selected' : '' }}>Inquiry Desk</option>
                        </select>
                      </div>
                      <div class="col-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                          <option value="online" {{ $c->status == 'online' ? 'selected' : '' }}>Online</option>
                          <option value="serving" {{ $c->status == 'serving' ? 'selected' : '' }}>Serving</option>
                          <option value="break" {{ $c->status == 'break' ? 'selected' : '' }}>Break</option>
                          <option value="offline" {{ $c->status == 'offline' ? 'selected' : '' }}>Offline</option>
                        </select>
                      </div>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Assign Teller</label>
                      <select name="current_user_id" class="form-select">
                        <option value="">-- No Teller Assigned --</option>
                        @foreach($tellers as $t)
                          <option value="{{ $t->id }}" {{ $c->current_user_id == $t->id ? 'selected' : '' }}>
                            {{ $t->name }} ({{ $t->email }})
                          </option>
                        @endforeach
                      </select>
                    </div>
                    <div class="mb-3">
                      <label class="form-label fw-semibold">Assigned Service Categories</label>
                      <div class="row g-2 border p-2 rounded">
                        @foreach($services as $srv)
                          <div class="col-6">
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" name="service_ids[]" value="{{ $srv->id }}" id="srv_{{ $c->id }}_{{ $srv->id }}"
                                {{ $c->services->contains($srv->id) ? 'checked' : '' }}>
                              <label class="form-check-label small" for="srv_{{ $c->id }}_{{ $srv->id }}">
                                {{ $srv->prefix }} - {{ $srv->name }}
                              </label>
                            </div>
                          </div>
                        @endforeach
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addCounterModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('admin.counters.store') }}" method="POST">
        @csrf
        <input type="hidden" name="branch_id" value="{{ $branchId }}">
        <div class="modal-header border-bottom">
          <h5 class="modal-title fw-bold"><i class="bx bx-grid-alt me-2 text-primary"></i>Create New Counter</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-2 mb-3">
            <div class="col-4">
              <label class="form-label">Counter #</label>
              <input type="text" name="counter_number" class="form-control" placeholder="e.g. 06" required>
            </div>
            <div class="col-8">
              <label class="form-label">Counter Name</label>
              <input type="text" name="name" class="form-control" placeholder="e.g. Counter 06 - Foreign Remittance" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Counter Type</label>
            <select name="type" class="form-select" required>
              <option value="general">General</option>
              <option value="priority_dedicated">SBP Dedicated Priority Desk (PWD / Pensioners)</option>
              <option value="cash">Cash Counter</option>
              <option value="inquiries">Inquiry Desk</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Assign Teller</label>
            <select name="current_user_id" class="form-select">
              <option value="">-- No Teller Assigned --</option>
              @foreach($tellers as $t)
                <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->email }})</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Services Served By This Counter</label>
            <div class="row g-2 border p-2 rounded">
              @foreach($services as $srv)
                <div class="col-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="service_ids[]" value="{{ $srv->id }}" id="new_srv_{{ $srv->id }}" checked>
                    <label class="form-check-label small" for="new_srv_{{ $srv->id }}">
                      {{ $srv->prefix }} - {{ $srv->name }}
                    </label>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
        <div class="modal-footer border-top">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Counter</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
