@extends('layouts.admin')

@section('title', 'Regional Clusters')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold py-1 mb-1">
      <span class="text-muted fw-light">Administration /</span> Regional Operations Clusters
    </h4>
    <p class="text-muted mb-0 small">
      Structure bank branches into administrative regional operations territories (South, Central, North).
    </p>
  </div>
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRegionModal">
    <i class="bx bx-plus me-1"></i> Add Regional Cluster
  </button>
</div>

<div class="card shadow-sm">
  <div class="table-responsive text-nowrap">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Cluster Code</th>
          <th>Territory Name</th>
          <th>Headquarters</th>
          <th>Regional Operations Head</th>
          <th>Active Branches</th>
          <th>Status</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($regions as $reg)
          <tr>
            <td>
              <strong class="text-primary font-monospace">{{ $reg->code }}</strong>
            </td>
            <td>
              <div class="fw-bold text-dark">{{ $reg->name }}</div>
              <small class="text-muted"><i class="bx bx-envelope me-1"></i>{{ $reg->contact_email }}</small>
            </td>
            <td>
              <span class="badge bg-label-info">{{ $reg->city_headquarters }}</span>
            </td>
            <td>
              <div class="fw-semibold">{{ $reg->regional_manager_name ?? 'Vacant' }}</div>
              <small class="text-muted">{{ $reg->contact_phone }}</small>
            </td>
            <td>
              <span class="badge bg-label-primary fs-6">{{ $reg->branches_count }} Branches</span>
            </td>
            <td>
              @if($reg->is_active)
                <span class="badge bg-label-success">Active</span>
              @else
                <span class="badge bg-label-danger">Disabled</span>
              @endif
            </td>
            <td class="text-end">
              <button class="btn btn-sm btn-icon btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editRegionModal{{ $reg->id }}">
                <i class="bx bx-edit"></i>
              </button>
              <form action="{{ route('admin.regions.destroy', $reg->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete region?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-icon btn-outline-danger">
                  <i class="bx bx-trash"></i>
                </button>
              </form>
            </td>
          </tr>

          <!-- Edit Modal -->
          <div class="modal fade" id="editRegionModal{{ $reg->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <form action="{{ route('admin.regions.update', $reg->id) }}" method="POST">
                  @csrf
                  @method('PUT')
                  <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Edit Region: {{ $reg->code }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="row g-2 mb-3">
                      <div class="col-8">
                        <label class="form-label fw-bold">Cluster Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $reg->name }}" required>
                      </div>
                      <div class="col-4">
                        <label class="form-label fw-bold">Code</label>
                        <input type="text" name="code" class="form-control font-monospace" value="{{ $reg->code }}" required>
                      </div>
                    </div>
                    <div class="row g-2 mb-3">
                      <div class="col-6">
                        <label class="form-label fw-bold">City HQ</label>
                        <input type="text" name="city_headquarters" class="form-control" value="{{ $reg->city_headquarters }}" required>
                      </div>
                      <div class="col-6">
                        <label class="form-label fw-bold">Regional Head Name</label>
                        <input type="text" name="regional_manager_name" class="form-control" value="{{ $reg->regional_manager_name }}">
                      </div>
                    </div>
                    <div class="row g-2 mb-3">
                      <div class="col-6">
                        <label class="form-label fw-bold">Contact Email</label>
                        <input type="email" name="contact_email" class="form-control" value="{{ $reg->contact_email }}">
                      </div>
                      <div class="col-6">
                        <label class="form-label fw-bold">Phone</label>
                        <input type="text" name="contact_phone" class="form-control" value="{{ $reg->contact_phone }}">
                      </div>
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

<!-- Add Modal -->
<div class="modal fade" id="addRegionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('admin.regions.store') }}" method="POST">
        @csrf
        <div class="modal-header border-bottom">
          <h5 class="modal-title fw-bold"><i class="bx bx-map-pin me-2 text-primary"></i>Create Regional Cluster</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-2 mb-3">
            <div class="col-8">
              <label class="form-label fw-bold">Region Name</label>
              <input type="text" name="name" class="form-control" placeholder="e.g. West Region (Balochistan)" required>
            </div>
            <div class="col-4">
              <label class="form-label fw-bold">Code</label>
              <input type="text" name="code" class="form-control font-monospace" placeholder="REG-WEST" required>
            </div>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label fw-bold">City HQ</label>
              <input type="text" name="city_headquarters" class="form-control" placeholder="e.g. Quetta" required>
            </div>
            <div class="col-6">
              <label class="form-label fw-bold">Regional Head Name</label>
              <input type="text" name="regional_manager_name" class="form-control" placeholder="e.g. Tariq Tareen">
            </div>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label fw-bold">Contact Email</label>
              <input type="email" name="contact_email" class="form-control" placeholder="west@sarraf-qms.com">
            </div>
            <div class="col-6">
              <label class="form-label fw-bold">Phone</label>
              <input type="text" name="contact_phone" class="form-control" placeholder="+92-81-111-000">
            </div>
          </div>
        </div>
        <div class="modal-footer border-top">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary fw-bold">Create Cluster</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
