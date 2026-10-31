@extends('layouts.admin')

@section('title', 'Priority Tiers & Compliance')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold py-1 mb-1"><span class="text-muted fw-light">Compliance /</span> SBP & SBI Customer Priority Tiers</h4>
    <p class="text-muted mb-0 small">Weighted queue precedence algorithm configured per State Bank regulatory guidelines.</p>
  </div>
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPriorityModal">
    <i class="bx bx-plus me-1"></i> Add Priority Tier
  </button>
</div>

<!-- Regulatory Banner -->
<div class="alert alert-primary d-flex align-items-center mb-4 border-0 shadow-sm">
  <div class="avatar avatar-md me-3">
    <span class="avatar-initial rounded-circle bg-white text-primary">
      <i class="bx bx-shield-quarter fs-3"></i>
    </span>
  </div>
  <div>
    <h6 class="alert-heading fw-bold mb-1">State Bank Regulatory Mandates Active</h6>
    <div class="small">
      <strong>SBP BC-CPD Circular Letter No. 01/2018:</strong> Mandatory priority queues for Pensioners, Senior Citizens, and Special Persons. <br>
      <strong>SBP Policy 2021:</strong> Financial Inclusion of Persons with Disabilities (PWDs) - Immediate queue precedence and dedicated counter routing.
    </div>
  </div>
</div>

<div class="card shadow-sm">
  <div class="table-responsive text-nowrap">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Priority Tier</th>
          <th>اردو Title</th>
          <th>Code</th>
          <th>Algorithm Weight</th>
          <th>Regulatory Basis / Tender Mandate</th>
          <th>Total Served</th>
          <th>Status</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($priorities as $p)
          <tr>
            <td>
              <div class="d-flex align-items-center">
                <span class="badge bg-label-{{ $p->badge_color }} p-2 rounded-3 me-2">
                  <i class="bx {{ $p->icon }} fs-5"></i>
                </span>
                <div>
                  <strong class="text-dark">{{ $p->name }}</strong>
                </div>
              </div>
            </td>
            <td>
              <span class="urdu-font fs-6">{{ $p->name_ur }}</span>
            </td>
            <td>
              <span class="badge bg-light text-dark font-monospace border">{{ $p->code }}</span>
            </td>
            <td>
              <span class="badge bg-{{ $p->badge_color }} fs-6 px-3 py-1">
                {{ $p->weight_multiplier }}x Precedence
              </span>
            </td>
            <td>
              <small class="text-muted">{{ $p->regulatory_note ?? 'Standard Operational' }}</small>
            </td>
            <td>
              <span class="badge bg-label-primary fs-tiny">{{ $p->tokens_count }}</span>
            </td>
            <td>
              @if($p->is_active)
                <span class="badge bg-label-success">Active</span>
              @else
                <span class="badge bg-label-danger">Disabled</span>
              @endif
            </td>
            <td class="text-end">
              <button class="btn btn-sm btn-icon btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editPriorityModal{{ $p->id }}">
                <i class="bx bx-edit"></i>
              </button>
            </td>
          </tr>

          <!-- Edit Modal -->
          <div class="modal fade" id="editPriorityModal{{ $p->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <form action="{{ route('admin.priorities.update', $p->id) }}" method="POST">
                  @csrf
                  @method('PUT')
                  <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Edit Priority: {{ $p->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="form-label">Category Name</label>
                      <input type="text" name="name" class="form-control" value="{{ $p->name }}" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Urdu Title (اردو)</label>
                      <input type="text" name="name_ur" class="form-control text-end urdu-font" value="{{ $p->name_ur }}" required>
                    </div>
                    <div class="row g-2 mb-3">
                      <div class="col-6">
                        <label class="form-label">Queue Weight Multiplier (1-10)</label>
                        <input type="number" name="weight_multiplier" class="form-control" value="{{ $p->weight_multiplier }}" min="1" max="10" required>
                        <small class="text-muted">Higher weight gets called ahead</small>
                      </div>
                      <div class="col-6">
                        <label class="form-label">Badge Color</label>
                        <select name="badge_color" class="form-select" required>
                          <option value="danger" {{ $p->badge_color == 'danger' ? 'selected' : '' }}>Danger (Red)</option>
                          <option value="warning" {{ $p->badge_color == 'warning' ? 'selected' : '' }}>Warning (Yellow)</option>
                          <option value="info" {{ $p->badge_color == 'info' ? 'selected' : '' }}>Info (Cyan)</option>
                          <option value="primary" {{ $p->badge_color == 'primary' ? 'selected' : '' }}>Primary (Blue)</option>
                          <option value="secondary" {{ $p->badge_color == 'secondary' ? 'selected' : '' }}>Secondary (Gray)</option>
                        </select>
                      </div>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Regulatory Mandate Reference</label>
                      <input type="text" name="regulatory_note" class="form-control" value="{{ $p->regulatory_note }}">
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="is_active" id="active{{ $p->id }}" {{ $p->is_active ? 'checked' : '' }}>
                      <label class="form-check-label" for="active{{ $p->id }}">Active on Kiosk and Dispenser</label>
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
<div class="modal fade" id="addPriorityModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('admin.priorities.store') }}" method="POST">
        @csrf
        <div class="modal-header border-bottom">
          <h5 class="modal-title fw-bold"><i class="bx bx-shield-quarter me-2 text-primary"></i>Add Priority Classification</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-2 mb-3">
            <div class="col-8">
              <label class="form-label">Priority Tier Name</label>
              <input type="text" name="name" class="form-control" placeholder="e.g. Student Accounts" required>
            </div>
            <div class="col-4">
              <label class="form-label">Code</label>
              <input type="text" name="code" class="form-control font-monospace" placeholder="STUDENT" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Urdu Title (اردو)</label>
            <input type="text" name="name_ur" class="form-control text-end urdu-font" placeholder="طالب علم کیٹیگری" required>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label">Weight Multiplier (1-10)</label>
              <input type="number" name="weight_multiplier" class="form-control" value="2" min="1" max="10" required>
            </div>
            <div class="col-6">
              <label class="form-label">Badge Color</label>
              <select name="badge_color" class="form-select" required>
                <option value="primary">Primary</option>
                <option value="success">Success</option>
                <option value="info">Info</option>
                <option value="warning">Warning</option>
                <option value="danger">Danger</option>
              </select>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Icon (Boxicons)</label>
            <input type="text" name="icon" class="form-control" value="bx-user-check" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Regulatory Mandate Reference</label>
            <input type="text" name="regulatory_note" class="form-control" placeholder="e.g. Internal Bank Policy">
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" id="newPriActive" checked>
            <label class="form-check-label" for="newPriActive">Active on Kiosk and Dispenser</label>
          </div>
        </div>
        <div class="modal-footer border-top">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Tier</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
