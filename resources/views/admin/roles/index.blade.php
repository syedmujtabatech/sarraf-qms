@extends('layouts.admin')

@section('title', 'Roles & Permissions (RBAC)')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold py-1 mb-1">
      <span class="text-muted fw-light">Administration /</span> Roles & Permission Management (RBAC)
    </h4>
    <p class="text-muted mb-0 small">
      Delegate operational authorities, branch approvals, and regulatory compliance duties across bank hierarchy.
    </p>
  </div>
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
    <i class="bx bx-plus me-1"></i> Add Custom Banking Role
  </button>
</div>

<!-- Roles Overview Cards Grid -->
<div class="row g-4 mb-4">
  @foreach($roles as $r)
    <div class="col-xl-4 col-lg-6 col-md-6">
      <div class="card h-100 shadow-sm border-top border-4 border-{{ $r->badge_color }}">
        <div class="card-body p-4 d-flex flex-column justify-content-between">
          <div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="badge bg-label-{{ $r->badge_color }} text-uppercase fw-bold" style="font-size: 11px;">
                {{ $r->name }}
              </span>
              <span class="text-muted small">
                <i class="bx bx-user me-1"></i> {{ $r->users_count }} Staff Assigned
              </span>
            </div>

            <h5 class="fw-bold text-dark mb-1">{{ $r->display_name }}</h5>
            <p class="text-muted small mb-3" style="min-height: 40px;">{{ $r->description }}</p>

            <!-- Permissions Preview -->
            <div class="mb-3">
              <small class="text-uppercase text-muted fw-bold d-block mb-2" style="font-size: 10px;">Granted Capabilities ({{ $r->permissions->count() }}):</small>
              <div class="d-flex flex-wrap gap-1" style="max-height: 110px; overflow-y: auto;">
                @if($r->name === 'super_admin')
                  <span class="badge bg-danger text-white py-1 px-2" style="font-size: 10px;">
                    <i class="bx bx-check-shield me-1"></i> Full Unrestricted National Authority
                  </span>
                @else
                  @forelse($r->permissions as $p)
                    <span class="badge bg-label-secondary" style="font-size: 10px;">{{ $p->display_name }}</span>
                  @empty
                    <span class="text-muted small fst-italic">No permissions assigned</span>
                  @endforelse
                @endif
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-2">
            <div>
              @if($r->is_system)
                <span class="badge bg-light text-secondary border small">Core System Role</span>
              @else
                <span class="badge bg-label-info small">Custom Role</span>
              @endif
            </div>
            <div class="d-flex gap-1">
              <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editRoleModal{{ $r->id }}">
                <i class="bx bx-edit me-1"></i> Edit Permissions
              </button>
              @if(!$r->is_system)
                <form action="{{ route('admin.roles.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Delete role?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-icon btn-outline-danger">
                    <i class="bx bx-trash"></i>
                  </button>
                </form>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Role & Permissions Modal -->
    <div class="modal fade" id="editRoleModal{{ $r->id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <form action="{{ route('admin.roles.update', $r->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-header border-bottom">
              <div>
                <h5 class="modal-title fw-bold">Edit Role Permissions: {{ $r->display_name }}</h5>
                <small class="text-muted">Configure fine-grained module privileges for this banking title</small>
              </div>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
              <div class="row g-3 mb-4">
                <div class="col-md-8">
                  <label class="form-label fw-bold">Role Display Title</label>
                  <input type="text" name="display_name" class="form-control" value="{{ $r->display_name }}" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">Badge Color</label>
                  <select name="badge_color" class="form-select" required>
                    <option value="primary" {{ $r->badge_color == 'primary' ? 'selected' : '' }}>Primary (Blue)</option>
                    <option value="success" {{ $r->badge_color == 'success' ? 'selected' : '' }}>Success (Green)</option>
                    <option value="danger" {{ $r->badge_color == 'danger' ? 'selected' : '' }}>Danger (Red)</option>
                    <option value="warning" {{ $r->badge_color == 'warning' ? 'selected' : '' }}>Warning (Yellow)</option>
                    <option value="info" {{ $r->badge_color == 'info' ? 'selected' : '' }}>Info (Cyan)</option>
                    <option value="secondary" {{ $r->badge_color == 'secondary' ? 'selected' : '' }}>Secondary (Gray)</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label fw-bold">Role Responsibilities Description</label>
                  <input type="text" name="description" class="form-control" value="{{ $r->description }}">
                </div>
              </div>

              <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bx bx-check-shield text-primary me-1"></i> Module Permissions Matrix
              </h6>

              @foreach($permissionsByModule as $moduleName => $perms)
                <div class="card bg-light border mb-3">
                  <div class="card-header bg-white py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                    <span class="fw-bold small text-primary text-uppercase">{{ $moduleName }}</span>
                    <span class="badge bg-label-secondary font-monospace" style="font-size: 10px;">{{ count($perms) }} Privileges</span>
                  </div>
                  <div class="card-body p-3">
                    <div class="row g-2">
                      @foreach($perms as $p)
                        <div class="col-md-6">
                          <div class="form-check">
                            <input
                              class="form-check-input"
                              type="checkbox"
                              name="permission_ids[]"
                              value="{{ $p->id }}"
                              id="perm_{{ $r->id }}_{{ $p->id }}"
                              {{ $r->permissions->contains('id', $p->id) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="perm_{{ $r->id }}_{{ $p->id }}">
                              <strong>{{ $p->display_name }}</strong>
                              <small class="d-block text-muted" style="font-size: 11px;">{{ $p->description }}</small>
                            </label>
                          </div>
                        </div>
                      @endforeach
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
            <div class="modal-footer border-top">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary fw-bold">Save Role Changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endforeach
</div>

<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf
        <div class="modal-header border-bottom">
          <h5 class="modal-title fw-bold"><i class="bx bx-key me-2 text-primary"></i>Create New Banking Role</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label fw-bold">Role Identifier (Slug)</label>
              <input type="text" name="name" class="form-control" placeholder="e.g. assistant_manager" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Role Title (Display Name)</label>
              <input type="text" name="display_name" class="form-control" placeholder="e.g. Assistant Operations Manager" required>
            </div>
            <div class="col-md-8">
              <label class="form-label fw-bold">Role Description</label>
              <input type="text" name="description" class="form-control" placeholder="Key responsibilities in branch / region">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-bold">Badge Color</label>
              <select name="badge_color" class="form-select" required>
                <option value="primary">Primary (Blue)</option>
                <option value="success">Success (Green)</option>
                <option value="info">Info (Cyan)</option>
                <option value="warning">Warning (Yellow)</option>
                <option value="danger">Danger (Red)</option>
                <option value="secondary">Secondary (Gray)</option>
              </select>
            </div>
          </div>

          <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
            <i class="bx bx-check-shield text-primary me-1"></i> Assign Initial Permissions
          </h6>

          @foreach($permissionsByModule as $moduleName => $perms)
            <div class="card bg-light border mb-3">
              <div class="card-header bg-white py-2 px-3 border-bottom">
                <span class="fw-bold small text-primary text-uppercase">{{ $moduleName }}</span>
              </div>
              <div class="card-body p-3">
                <div class="row g-2">
                  @foreach($perms as $p)
                    <div class="col-md-6">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permission_ids[]" value="{{ $p->id }}" id="new_perm_{{ $p->id }}">
                        <label class="form-check-label small" for="new_perm_{{ $p->id }}">
                          <strong>{{ $p->display_name }}</strong>
                        </label>
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          @endforeach
        </div>
        <div class="modal-footer border-top">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary fw-bold">Create Banking Role</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
