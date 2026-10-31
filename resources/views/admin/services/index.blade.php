@extends('layouts.admin')

@section('title', 'Service Categories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold py-1 mb-1"><span class="text-muted fw-light">Configuration /</span> Banking Service Categories</h4>
    <p class="text-muted mb-0 small">Manage token prefixes, multilingual titles and target SLA service times.</p>
  </div>
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">
    <i class="bx bx-plus me-1"></i> Add Service Category
  </button>
</div>

<div class="card shadow-sm">
  <div class="table-responsive text-nowrap">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Prefix</th>
          <th>Service Name (English)</th>
          <th>Service Name (اردو)</th>
          <th>Avg. SLA Time</th>
          <th>Theme Color</th>
          <th>Total Tokens Issued</th>
          <th>Status</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($services as $s)
          <tr>
            <td>
              <span class="badge bg-{{ $s->color }} fs-5 px-3 py-2 font-monospace">{{ $s->prefix }}</span>
            </td>
            <td>
              <div class="d-flex align-items-center">
                <i class="bx {{ $s->icon }} fs-4 me-2 text-{{ $s->color }}"></i>
                <div>
                  <div class="fw-bold">{{ $s->name }}</div>
                  <small class="text-muted">{{ $s->description }}</small>
                </div>
              </div>
            </td>
            <td>
              <span class="urdu-font fs-6 text-dark">{{ $s->name_ur }}</span>
            </td>
            <td>
              <span class="badge bg-label-info">
                <i class="bx bx-time me-1"></i> {{ $s->avg_service_time_mins }} Mins
              </span>
            </td>
            <td>
              <span class="badge bg-label-{{ $s->color }} text-capitalize">{{ $s->color }}</span>
            </td>
            <td>
              <span class="badge bg-label-primary fs-tiny">{{ $s->tokens_count }}</span>
            </td>
            <td>
              @if($s->is_active)
                <span class="badge bg-label-success">Active</span>
              @else
                <span class="badge bg-label-danger">Disabled</span>
              @endif
            </td>
            <td class="text-end">
              <button class="btn btn-sm btn-icon btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editSrvModal{{ $s->id }}">
                <i class="bx bx-edit"></i>
              </button>
            </td>
          </tr>

          <!-- Edit Modal -->
          <div class="modal fade" id="editSrvModal{{ $s->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <form action="{{ route('admin.services.update', $s->id) }}" method="POST">
                  @csrf
                  @method('PUT')
                  <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Edit Service: {{ $s->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="row g-2 mb-3">
                      <div class="col-8">
                        <label class="form-label">Service Name (English)</label>
                        <input type="text" name="name" class="form-control" value="{{ $s->name }}" required>
                      </div>
                      <div class="col-4">
                        <label class="form-label">Token Prefix</label>
                        <input type="text" name="prefix" class="form-control font-monospace" value="{{ $s->prefix }}" maxlength="3" required>
                      </div>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Service Name (اردو - SBP Spec)</label>
                      <input type="text" name="name_ur" class="form-control text-end urdu-font" value="{{ $s->name_ur }}" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Description</label>
                      <input type="text" name="description" class="form-control" value="{{ $s->description }}">
                    </div>
                    <div class="row g-2 mb-3">
                      <div class="col-4">
                        <label class="form-label">Avg SLA (Mins)</label>
                        <input type="number" name="avg_service_time_mins" class="form-control" value="{{ $s->avg_service_time_mins }}" required>
                      </div>
                      <div class="col-4">
                        <label class="form-label">Boxicon Class</label>
                        <input type="text" name="icon" class="form-control" value="{{ $s->icon }}" required>
                      </div>
                      <div class="col-4">
                        <label class="form-label">Badge Color</label>
                        <select name="color" class="form-select" required>
                          <option value="primary" {{ $s->color == 'primary' ? 'selected' : '' }}>Primary</option>
                          <option value="success" {{ $s->color == 'success' ? 'selected' : '' }}>Success</option>
                          <option value="info" {{ $s->color == 'info' ? 'selected' : '' }}>Info</option>
                          <option value="warning" {{ $s->color == 'warning' ? 'selected' : '' }}>Warning</option>
                          <option value="danger" {{ $s->color == 'danger' ? 'selected' : '' }}>Danger</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="is_active" id="active{{ $s->id }}" {{ $s->is_active ? 'checked' : '' }}>
                      <label class="form-check-label" for="active{{ $s->id }}">Active on Kiosk and Portal</label>
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
<div class="modal fade" id="addServiceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('admin.services.store') }}" method="POST">
        @csrf
        <div class="modal-header border-bottom">
          <h5 class="modal-title fw-bold"><i class="bx bx-list-check me-2 text-primary"></i>Add Banking Service Category</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-2 mb-3">
            <div class="col-8">
              <label class="form-label">Service Name (English)</label>
              <input type="text" name="name" class="form-control" placeholder="e.g. Utility Bills & Taxation" required>
            </div>
            <div class="col-4">
              <label class="form-label">Prefix</label>
              <input type="text" name="prefix" class="form-control font-monospace" placeholder="e.g. U" maxlength="3" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Service Name (اردو - SBP Spec)</label>
            <input type="text" name="name_ur" class="form-control text-end urdu-font" placeholder="یوٹیلٹی بلز اور ٹیکس ادائیگی" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <input type="text" name="description" class="form-control" placeholder="Short description for kiosk">
          </div>
          <div class="row g-2 mb-3">
            <div class="col-4">
              <label class="form-label">Avg SLA (Mins)</label>
              <input type="number" name="avg_service_time_mins" class="form-control" value="8" required>
            </div>
            <div class="col-4">
              <label class="form-label">Boxicon Class</label>
              <input type="text" name="icon" class="form-control" value="bx-credit-card" required>
            </div>
            <div class="col-4">
              <label class="form-label">Badge Color</label>
              <select name="color" class="form-select" required>
                <option value="primary">Primary</option>
                <option value="success">Success</option>
                <option value="info">Info</option>
                <option value="warning">Warning</option>
                <option value="danger">Danger</option>
              </select>
            </div>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" id="newSrvActive" checked>
            <label class="form-check-label" for="newSrvActive">Active on Kiosk and Portal</label>
          </div>
        </div>
        <div class="modal-footer border-top">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Service</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
