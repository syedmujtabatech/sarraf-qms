@extends('layouts.admin')

@section('title', 'Bank Staff & Tellers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold py-1 mb-1">
      <span class="text-muted fw-light">Administration /</span> Staff Directory & Counter Binding
    </h4>
    <p class="text-muted mb-0 small">
      Manage bank personnel, designate tellers to active stations, and allocate regional responsibilities.
    </p>
  </div>
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
    <i class="bx bx-user-plus me-1"></i> Add Banking Staff Member
  </button>
</div>

<!-- Filters Bar -->
<div class="card mb-4 shadow-sm">
  <div class="card-body py-3">
    <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3 align-items-center">
      <div class="col-md-4">
        <label class="form-label small fw-semibold">Filter by Banking Role</label>
        <select name="role_id" class="form-select form-select-sm" onchange="this.form.submit()">
          <option value="">-- All Roles --</option>
          @foreach($roles as $r)
            <option value="{{ $r->id }}" {{ $r->id == $roleId ? 'selected' : '' }}>{{ $r->display_name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label small fw-semibold">Filter by Branch</label>
        <select name="branch_id" class="form-select form-select-sm" onchange="this.form.submit()">
          <option value="">-- All Branches --</option>
          @foreach($branches as $b)
            <option value="{{ $b->id }}" {{ $b->id == $branchId ? 'selected' : '' }}>{{ $b->code }} - {{ $b->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4 d-flex align-items-end">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm w-100">Reset Filter</a>
      </div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="table-responsive text-nowrap">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Staff Officer</th>
          <th>Designated Role</th>
          <th>Assigned Counter</th>
          <th>Branch & Region</th>
          <th>Status</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $u)
          <tr>
            <td>
              <div class="d-flex align-items-center">
                <div class="avatar avatar-md me-3">
                  <span class="avatar-initial rounded-circle bg-label-{{ $u->roleModel->badge_color ?? 'primary' }} fw-bold">
                    {{ strtoupper(substr($u->name, 0, 2)) }}
                  </span>
                </div>
                <div>
                  <div class="fw-bold text-dark">{{ $u->name }}</div>
                  <small class="text-muted"><i class="bx bx-envelope me-1"></i>{{ $u->email }}</small>
                </div>
              </div>
            </td>
            <td>
              <span class="badge bg-label-{{ $u->roleModel->badge_color ?? 'primary' }} fs-tiny py-1 px-2">
                {{ $u->role_title }}
              </span>
            </td>
            <td>
              @if($u->assignedCounter)
                <span class="badge bg-primary fs-tiny">
                  <i class="bx bx-desktop me-1"></i> #{{ $u->assignedCounter->counter_number }} - {{ $u->assignedCounter->name }}
                </span>
              @else
                <span class="text-muted small">No Counter Bound</span>
              @endif
            </td>
            <td>
              <div class="fw-semibold small">{{ $u->branch->name ?? 'Headquarters' }}</div>
              <small class="text-muted">{{ $u->region->name ?? $u->branch->region->name ?? 'National Pool' }}</small>
            </td>
            <td>
              @if($u->is_active)
                <span class="badge bg-label-success">Active</span>
              @else
                <span class="badge bg-label-danger">Disabled</span>
              @endif
            </td>
            <td class="text-end">
              <button class="btn btn-sm btn-icon btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}">
                <i class="bx bx-edit"></i>
              </button>
              @if($u->id !== 1)
                <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete staff account?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-icon btn-outline-danger">
                    <i class="bx bx-trash"></i>
                  </button>
                </form>
              @endif
            </td>
          </tr>

          <!-- Edit User Modal -->
          <div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <form action="{{ route('admin.users.update', $u->id) }}" method="POST">
                  @csrf
                  @method('PUT')
                  <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Edit Staff Profile: {{ $u->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="form-label fw-bold">Full Name</label>
                      <input type="text" name="name" class="form-control" value="{{ $u->name }}" required>
                    </div>
                    <div class="row g-2 mb-3">
                      <div class="col-6">
                        <label class="form-label fw-bold">Official Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $u->email }}" required>
                      </div>
                      <div class="col-6">
                        <label class="form-label fw-bold">New Password (Optional)</label>
                        <input type="password" name="password" class="form-control" placeholder="Leave blank to retain">
                      </div>
                    </div>
                    <div class="mb-3">
                      <label class="form-label fw-bold">Banking Role</label>
                      <select name="role_id" class="form-select" required>
                        @foreach($roles as $r)
                          <option value="{{ $r->id }}" {{ $u->role_id == $r->id ? 'selected' : '' }}>
                            {{ $r->display_name }} ({{ $r->name }})
                          </option>
                        @endforeach
                      </select>
                    </div>
                    <div class="row g-2 mb-3">
                      <div class="col-6">
                        <label class="form-label fw-bold">Region</label>
                        <select name="region_id" class="form-select">
                          <option value="">-- No Specific Region --</option>
                          @foreach($regions as $reg)
                            <option value="{{ $reg->id }}" {{ $u->region_id == $reg->id ? 'selected' : '' }}>{{ $reg->name }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-6">
                        <label class="form-label fw-bold">Branch</label>
                        <select name="branch_id" class="form-select">
                          <option value="">-- Headquarters Pool --</option>
                          @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ $u->branch_id == $b->id ? 'selected' : '' }}>{{ $b->code }} - {{ $b->name }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="mb-3">
                      <label class="form-label fw-bold">Designated Counter Station</label>
                      <select name="assigned_counter_id" class="form-select">
                        <option value="">-- No Fixed Counter --</option>
                        @foreach($counters as $cnt)
                          <option value="{{ $cnt->id }}" {{ $u->assigned_counter_id == $cnt->id ? 'selected' : '' }}>
                            #{{ $cnt->counter_number }} - {{ $cnt->name }} ({{ $cnt->branch->code ?? '' }})
                          </option>
                        @endforeach
                      </select>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="is_active" id="active{{ $u->id }}" {{ $u->is_active ? 'checked' : '' }}>
                      <label class="form-check-label" for="active{{ $u->id }}">Officer Account Active</label>
                    </div>
                  </div>
                  <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Save Changes</button>
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

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <div class="modal-header border-bottom">
          <h5 class="modal-title fw-bold"><i class="bx bx-user-plus me-2 text-primary"></i>Onboard Banking Staff Member</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-bold">Full Name</label>
            <input type="text" name="name" class="form-control" placeholder="e.g. Asad Mehmood" required>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label fw-bold">Official Email</label>
              <input type="email" name="email" class="form-control" placeholder="name@sarraf.com" required>
            </div>
            <div class="col-6">
              <label class="form-label fw-bold">Password</label>
              <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Banking Role</label>
            <select name="role_id" class="form-select" required>
              @foreach($roles as $r)
                <option value="{{ $r->id }}">{{ $r->display_name }} ({{ $r->name }})</option>
              @endforeach
            </select>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label fw-bold">Region</label>
              <select name="region_id" class="form-select">
                <option value="">-- Select Region --</option>
                @foreach($regions as $reg)
                  <option value="{{ $reg->id }}">{{ $reg->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-6">
              <label class="form-label fw-bold">Branch</label>
              <select name="branch_id" class="form-select">
                <option value="">-- Select Branch --</option>
                @foreach($branches as $b)
                  <option value="{{ $b->id }}">{{ $b->code }} - {{ $b->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Assign Counter Station (For Tellers / Cashiers)</label>
            <select name="assigned_counter_id" class="form-select">
              <option value="">-- No Fixed Counter --</option>
              @foreach($counters as $cnt)
                <option value="{{ $cnt->id }}">#{{ $cnt->counter_number }} - {{ $cnt->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer border-top">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary fw-bold">Create Staff Account</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
