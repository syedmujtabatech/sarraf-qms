@extends('layouts.admin')

@section('title', 'Branch Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold py-1 mb-1"><span class="text-muted fw-light">Configuration /</span> Branch Network</h4>
    <p class="text-muted mb-0 small">Manage bank branches, operational hours and contact coordinates.</p>
  </div>
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBranchModal">
    <i class="bx bx-plus me-1"></i> Add New Branch
  </button>
</div>

<div class="card shadow-sm">
  <div class="table-responsive text-nowrap">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Branch Code</th>
          <th>Branch Name</th>
          <th>Region / Cluster</th>
          <th>City</th>
          <th>Operating Hours</th>
          <th>Counters</th>
          <th>Today's Tokens</th>
          <th>Status</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($branches as $b)
          <tr>
            <td><strong class="text-primary font-monospace">{{ $b->code }}</strong></td>
            <td>
              <div class="fw-semibold">{{ $b->name }}</div>
              <small class="text-muted">{{ $b->address }}</small>
            </td>
            <td>
              <span class="badge bg-label-primary">{{ $b->region->name ?? 'General Cluster' }}</span>
            </td>
            <td><span class="badge bg-label-info">{{ $b->city }}</span></td>
            <td>
              <small class="text-muted">
                <i class="bx bx-time me-1"></i>{{ \Carbon\Carbon::parse($b->open_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($b->close_time)->format('h:i A') }}
              </small>
            </td>
            <td>
              <span class="badge bg-label-secondary fs-tiny">{{ $b->counters_count }} Counters</span>
            </td>
            <td>
              <span class="badge bg-label-primary fs-tiny">{{ $b->tokens_count }} Tokens</span>
            </td>
            <td>
              @if($b->is_active)
                <span class="badge bg-label-success">Active</span>
              @else
                <span class="badge bg-label-danger">Inactive</span>
              @endif
            </td>
            <td class="text-end">
              <button class="btn btn-sm btn-icon btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editBranchModal{{ $b->id }}">
                <i class="bx bx-edit"></i>
              </button>
              <form action="{{ route('admin.branches.destroy', $b->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this branch?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-icon btn-outline-danger">
                  <i class="bx bx-trash"></i>
                </button>
              </form>
            </td>
          </tr>

          <!-- Edit Branch Modal -->
          <div class="modal fade" id="editBranchModal{{ $b->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <form action="{{ route('admin.branches.update', $b->id) }}" method="POST">
                  @csrf
                  @method('PUT')
                  <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Edit Branch #{{ $b->code }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="form-label">Branch Name</label>
                      <input type="text" name="name" class="form-control" value="{{ $b->name }}" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Regional Cluster</label>
                      <select name="region_id" class="form-select">
                        <option value="">-- No Region Assigned --</option>
                        @foreach($regions as $reg)
                          <option value="{{ $reg->id }}" {{ $b->region_id == $reg->id ? 'selected' : '' }}>{{ $reg->name }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="row g-2 mb-3">
                      <div class="col-6">
                        <label class="form-label">Branch Code</label>
                        <input type="text" name="code" class="form-control" value="{{ $b->code }}" required>
                      </div>
                      <div class="col-6">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="{{ $b->city }}" required>
                      </div>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Address</label>
                      <textarea name="address" class="form-control" rows="2">{{ $b->address }}</textarea>
                    </div>
                    <div class="row g-2 mb-3">
                      <div class="col-6">
                        <label class="form-label">Open Time</label>
                        <input type="time" name="open_time" class="form-control" value="{{ $b->open_time }}" required>
                      </div>
                      <div class="col-6">
                        <label class="form-label">Close Time</label>
                        <input type="time" name="close_time" class="form-control" value="{{ $b->close_time }}" required>
                      </div>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="is_active" id="active{{ $b->id }}" {{ $b->is_active ? 'checked' : '' }}>
                      <label class="form-check-label" for="active{{ $b->id }}">Branch Active</label>
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

<!-- Add Branch Modal -->
<div class="modal fade" id="addBranchModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('admin.branches.store') }}" method="POST">
        @csrf
        <div class="modal-header border-bottom">
          <h5 class="modal-title fw-bold"><i class="bx bx-buildings me-2 text-primary"></i>Add New Bank Branch</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Branch Name</label>
            <input type="text" name="name" class="form-control" placeholder="e.g. Clifton Commercial Branch" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Regional Operations Cluster</label>
            <select name="region_id" class="form-select">
              <option value="">-- Select Regional Cluster --</option>
              @foreach($regions as $reg)
                <option value="{{ $reg->id }}">{{ $reg->name }} ({{ $reg->code }})</option>
              @endforeach
            </select>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label">Branch Code</label>
              <input type="text" name="code" class="form-control" placeholder="e.g. SB-004" required>
            </div>
            <div class="col-6">
              <label class="form-label">City</label>
              <input type="text" name="city" class="form-control" placeholder="e.g. Karachi" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="2" placeholder="Full branch physical address"></textarea>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label">Open Time</label>
              <input type="time" name="open_time" class="form-control" value="09:00" required>
            </div>
            <div class="col-6">
              <label class="form-label">Close Time</label>
              <input type="time" name="close_time" class="form-control" value="17:00" required>
            </div>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" id="newActive" checked>
            <label class="form-check-label" for="newActive">Branch Active Immediately</label>
          </div>
        </div>
        <div class="modal-footer border-top">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Branch</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
