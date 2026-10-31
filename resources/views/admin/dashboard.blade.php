@extends('layouts.admin')

@section('title', 'Operations Dashboard')

@section('content')
<div class="row mb-4 align-items-center">
  <div class="col-md-6">
    <h4 class="fw-bold py-1 mb-1">
      <span class="text-muted fw-light">Operations /</span> Central Live Cockpit
      @if($currentBranch && $currentBranch->region)
        <span class="badge bg-label-primary fs-tiny ms-2">{{ $currentBranch->region->name }}</span>
      @endif
    </h4>
    <p class="text-muted mb-0 small">
      Real-time queue monitoring, counter throughput & regulatory SLA compliance tracking.
    </p>
  </div>
  <div class="col-md-6 d-flex justify-content-md-end gap-2 mt-3 mt-md-0">
    <!-- Floor PA Announcement Broadcast Button -->
    <button type="button" class="btn btn-sm btn-outline-warning d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#managerBroadcastModal">
      <i class="bx bx-broadcast me-1"></i> Floor PA Broadcast
    </button>

    <!-- Manager Emergency Token Injection Button -->
    <button type="button" class="btn btn-sm btn-outline-danger d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#dashboardInjectModal">
      <i class="bx bx-shield-plus me-1"></i> Manager Override
    </button>

    <!-- Branch Selector Dropdown -->
    <form method="GET" action="{{ route('admin.dashboard') }}" class="d-flex align-items-center">
      <div class="input-group input-group-sm">
        <label class="input-group-text bg-white fw-semibold" for="branchSelect">
          <i class="bx bx-buildings text-primary me-1"></i>
        </label>
        <select class="form-select fw-semibold" id="branchSelect" name="branch_id" onchange="this.form.submit()">
          @foreach($branches as $b)
            <option value="{{ $b->id }}" {{ $b->id == $selectedBranchId ? 'selected' : '' }}>
              {{ $b->code }} - {{ $b->name }}
            </option>
          @endforeach
        </select>
      </div>
    </form>

    <button type="button" class="btn btn-sm btn-primary" onclick="window.location.reload()">
      <i class="bx bx-refresh"></i>
    </button>
  </div>
</div>

<!-- Key Performance Indicators (KPIs) -->
<div class="row g-3 mb-4">
  <!-- Total Issued Today -->
  <div class="col-sm-6 col-lg-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="badge bg-label-primary p-2 rounded-3">
            <i class="bx bx-receipt fs-4"></i>
          </span>
          <span class="text-muted small fw-semibold">Daily Issued</span>
        </div>
        <h3 class="card-title mb-1 fw-bold">{{ $stats['total_today'] }}</h3>
        <small class="text-muted">Total tokens generated today</small>
      </div>
    </div>
  </div>

  <!-- Waiting Now -->
  <div class="col-sm-6 col-lg-3">
    <div class="card h-100 border-start border-warning border-4">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="badge bg-label-warning p-2 rounded-3">
            <i class="bx bx-time-five fs-4"></i>
          </span>
          <span class="text-warning small fw-semibold">In Waiting Hall</span>
        </div>
        <h3 class="card-title mb-1 fw-bold text-warning">{{ $stats['waiting'] }}</h3>
        <small class="text-muted">Customers currently in queue</small>
      </div>
    </div>
  </div>

  <!-- Serving Now -->
  <div class="col-sm-6 col-lg-3">
    <div class="card h-100 border-start border-info border-4">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="badge bg-label-info p-2 rounded-3">
            <i class="bx bx-user-voice fs-4"></i>
          </span>
          <span class="text-info small fw-semibold">At Tellers</span>
        </div>
        <h3 class="card-title mb-1 fw-bold text-info">{{ $stats['serving'] }}</h3>
        <small class="text-muted">Customers currently being served</small>
      </div>
    </div>
  </div>

  <!-- Completed Today -->
  <div class="col-sm-6 col-lg-3">
    <div class="card h-100 border-start border-success border-4">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="badge bg-label-success p-2 rounded-3">
            <i class="bx bx-check-circle fs-4"></i>
          </span>
          <span class="text-success small fw-semibold">Success Rate</span>
        </div>
        <h3 class="card-title mb-1 fw-bold text-success">{{ $stats['completed'] }}</h3>
        <small class="text-muted">Completed transactions</small>
      </div>
    </div>
  </div>
</div>

<!-- Secondary SLA Metric Row -->
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card bg-label-secondary border-0">
      <div class="card-body py-3 d-flex align-items-center">
        <div class="avatar avatar-md me-3">
          <span class="avatar-initial rounded-circle bg-white text-primary shadow-sm">
            <i class="bx bx-timer fs-3"></i>
          </span>
        </div>
        <div>
          <div class="text-muted small">Avg. Wait Time</div>
          <h5 class="mb-0 fw-bold">{{ $stats['avg_wait_minutes'] }} <span class="fs-tiny text-muted">Minutes</span></h5>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card bg-label-secondary border-0">
      <div class="card-body py-3 d-flex align-items-center">
        <div class="avatar avatar-md me-3">
          <span class="avatar-initial rounded-circle bg-white text-success shadow-sm">
            <i class="bx bx-hourglass fs-3"></i>
          </span>
        </div>
        <div>
          <div class="text-muted small">Avg. Service Time</div>
          <h5 class="mb-0 fw-bold">{{ $stats['avg_service_minutes'] }} <span class="fs-tiny text-muted">Minutes</span></h5>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card bg-label-danger border-0">
      <div class="card-body py-3 d-flex align-items-center">
        <div class="avatar avatar-md me-3">
          <span class="avatar-initial rounded-circle bg-white text-danger shadow-sm">
            <i class="bx bx-accessibility fs-3"></i>
          </span>
        </div>
        <div>
          <div class="text-danger small fw-semibold">SBP Priority Handled</div>
          <h5 class="mb-0 fw-bold text-danger">{{ $stats['priority_completed'] }} / {{ $stats['priority_total'] }}</h5>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card bg-label-warning border-0">
      <div class="card-body py-3 d-flex align-items-center">
        <div class="avatar avatar-md me-3">
          <span class="avatar-initial rounded-circle bg-white text-warning shadow-sm">
            <i class="bx bx-star fs-3"></i>
          </span>
        </div>
        <div>
          <div class="text-muted small">Customer Rating</div>
          <h5 class="mb-0 fw-bold text-warning">{{ number_format($avgFeedback, 1) }} / 5.0 <span class="fs-tiny text-muted">({{ $totalFeedbacks }} reviews)</span></h5>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Live Counters Wallboard -->
<div class="card mb-4">
  <div class="card-header d-flex justify-content-between align-items-center border-bottom pb-3">
    <div>
      <h5 class="mb-0 fw-bold"><i class="bx bx-grid-alt me-2 text-primary"></i>Live Counter Status Wallboard</h5>
      <small class="text-muted">Real-time status of all active counters at {{ $currentBranch->name }}</small>
    </div>
    <span class="badge bg-label-primary px-3 py-2 rounded-pill">
      {{ $counters->whereIn('status', ['online', 'serving'])->count() }} / {{ $counters->count() }} Counters Active
    </span>
  </div>
  <div class="card-body pt-3">
    <div class="row g-3">
      @forelse($counters as $c)
        <div class="col-md-4 col-sm-6">
          <div class="card border {{ $c->status === 'serving' ? 'border-primary shadow-sm' : ($c->status === 'online' ? 'border-success' : 'border-secondary') }} h-100">
            <div class="card-body p-3">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                  <span class="badge bg-primary text-white fw-bold me-1">#{{ $c->counter_number }}</span>
                  <span class="fw-bold text-dark">{{ $c->name }}</span>
                </div>
                @if($c->status === 'serving')
                  <span class="badge bg-label-primary pulse-dot">Serving</span>
                @elseif($c->status === 'online')
                  <span class="badge bg-label-success">Available</span>
                @elseif($c->status === 'break')
                  <span class="badge bg-label-warning">Break</span>
                @else
                  <span class="badge bg-label-secondary">Offline</span>
                @endif
              </div>

              <!-- Teller Info -->
              <div class="d-flex align-items-center text-muted small mb-2">
                <i class="bx bx-user me-1 text-primary"></i>
                <span>Teller: <strong>{{ $c->currentUser->name ?? 'Unassigned' }}</strong></span>
              </div>

              <!-- Type Badge -->
              @if($c->type === 'priority_dedicated')
                <div class="badge bg-label-danger w-100 py-1 mb-2 text-wrap" style="font-size: 10px;">
                  <i class="bx bx-shield-quarter me-1"></i> SBP Dedicated Priority Desk (PWD / Pensioners)
                </div>
              @endif

              <!-- Current Serving Token Banner -->
              <div class="p-2 rounded bg-light border text-center">
                @if($c->currentToken)
                  <small class="text-muted d-block" style="font-size: 11px;">Serving Token:</small>
                  <span class="fs-4 fw-bolder text-primary">{{ $c->currentToken->token_number }}</span>
                  <div class="small fw-semibold text-truncate">{{ $c->currentToken->customer_name }}</div>
                  <span class="badge bg-label-{{ $c->currentToken->priorityCategory->badge_color ?? 'primary' }} mt-1" style="font-size: 10px;">
                    {{ $c->currentToken->priorityCategory->name ?? 'Regular' }}
                  </span>
                @else
                  <small class="text-muted d-block py-2">No active customer being served</small>
                @endif
              </div>

              <!-- Services served -->
              <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center flex-wrap gap-1">
                <div class="d-flex flex-wrap gap-1">
                  @foreach($c->services as $srv)
                    <span class="badge bg-label-secondary" style="font-size: 9px;">{{ $srv->prefix }}</span>
                  @endforeach
                </div>
                <div class="d-flex align-items-center gap-1">
                  <span class="badge bg-label-success" style="font-size: 10px;" title="Current Cash Drawer">
                    <i class="bx bx-money me-1"></i>PKR {{ number_format($c->current_cash_drawer, 0) }}
                  </span>
                  <a href="{{ route('display.pole', $c->id) }}" target="_blank" class="btn btn-xs btn-outline-info px-2 py-0" title="Open Counter-Top Pole LED Display">
                    <i class="bx bx-tv"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12 text-center py-4 text-muted">
          No counters configured for this branch. <a href="{{ route('admin.counters.index', ['branch_id' => $selectedBranchId]) }}">Add Counters now</a>.
        </div>
      @endforelse
    </div>
  </div>
</div>

<!-- Activity & Distribution Breakdown -->
<div class="row g-4">
  <!-- Recent Queue Activity Table -->
  <div class="col-lg-8">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center border-bottom pb-3">
        <div>
          <h5 class="mb-0 fw-bold"><i class="bx bx-list-ol me-2 text-primary"></i>Live Queue Activity</h5>
          <small class="text-muted">Today's latest tokens and customer progression</small>
        </div>
        <a href="{{ route('admin.reports.index', ['branch_id' => $selectedBranchId]) }}" class="btn btn-sm btn-outline-primary">
          View Complete Log
        </a>
      </div>
      <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Token</th>
              <th>Customer</th>
              <th>Service</th>
              <th>Priority Tier</th>
              <th>Status</th>
              <th>Counter</th>
              <th>Time</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentTokens as $t)
              <tr>
                <td>
                  <strong class="text-primary fs-6">{{ $t->token_number }}</strong>
                </td>
                <td>
                  <div class="fw-semibold">{{ $t->customer_name }}</div>
                  <small class="text-muted">{{ $t->customer_phone ?? 'Walk-in' }}</small>
                </td>
                <td>
                  <span class="badge bg-label-{{ $t->serviceCategory->color ?? 'primary' }}">
                    {{ $t->serviceCategory->name ?? 'Service' }}
                  </span>
                </td>
                <td>
                  <span class="badge bg-label-{{ $t->priorityCategory->badge_color ?? 'secondary' }}">
                    <i class="bx {{ $t->priorityCategory->icon ?? 'bx-user' }} me-1"></i>
                    {{ $t->priorityCategory->name ?? 'Regular' }}
                  </span>
                </td>
                <td>
                  {!! $t->status_badge !!}
                </td>
                <td>
                  <span class="badge bg-light text-dark border">
                    {{ $t->counter ? '#' . $t->counter->counter_number : '-' }}
                  </span>
                </td>
                <td>
                  <small class="text-muted">{{ $t->created_at->format('h:i A') }}</small>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-4 text-muted">No tokens generated today yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Service Distribution & Regulatory Compliance -->
  <div class="col-lg-4">
    <!-- Services Distribution -->
    <div class="card mb-4">
      <div class="card-header border-bottom pb-3">
        <h5 class="mb-0 fw-bold"><i class="bx bx-pie-chart-alt me-2 text-primary"></i>Service Workload</h5>
        <small class="text-muted">Today's tokens per banking category</small>
      </div>
      <div class="card-body pt-3">
        <ul class="list-unstyled mb-0">
          @foreach($serviceStats as $srv)
            <li class="d-flex justify-content-between align-items-center mb-3">
              <div class="d-flex align-items-center">
                <span class="badge bg-label-{{ $srv->color }} p-2 rounded-3 me-2">
                  <i class="bx {{ $srv->icon }}"></i>
                </span>
                <div>
                  <div class="fw-semibold" style="font-size: 13px;">{{ $srv->name }}</div>
                  <div class="urdu-font text-muted" style="font-size: 11px; line-height: 1.2;">{{ $srv->name_ur }}</div>
                </div>
              </div>
              <span class="badge bg-label-primary fs-6">{{ $srv->tokens_count }}</span>
            </li>
          @endforeach
        </ul>
      </div>
    </div>

    <!-- SBP Priority Policy Compliance -->
    <div class="card border-0 bg-primary text-white">
      <div class="card-body">
        <div class="d-flex align-items-center mb-3">
          <i class="bx bx-shield-quarter fs-2 me-2"></i>
          <div>
            <h6 class="mb-0 text-white fw-bold">SBP Accessibility Guidelines</h6>
            <small class="text-white-50">State Bank Regulatory Mandate</small>
          </div>
        </div>
        <p class="small text-white-50 mb-3">
          Dedicated priority scheduling is active for Pensioners, Senior Citizens, and Special Persons (PWDs) as per SBP Circulars CPD 02/2014 & BC-CPD 01/2018.
        </p>
        <div class="d-flex justify-content-between align-items-center pt-2 border-top border-white-50">
          <span class="small fw-semibold">SLA Fulfillment:</span>
          <span class="badge bg-white text-primary fw-bold">100% Compliant</span>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Dashboard Manager Emergency Priority Injection Modal -->
<div class="modal fade" id="dashboardInjectModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('admin.manager.injectEmergencyToken') }}" method="POST">
        @csrf
        <input type="hidden" name="branch_id" value="{{ $selectedBranchId }}">
        <div class="modal-header border-bottom bg-danger text-white">
          <h5 class="modal-title fw-bold text-white"><i class="bx bx-shield-plus me-2"></i>Manager Emergency Priority Override</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger py-2 small mb-3">
            <i class="bx bx-error-circle me-1"></i> Authorized for Branch Managers, Regional Heads & Chief Tellers. Immediately bypasses regular FIFO queue.
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Customer Full Name</label>
            <input type="text" name="customer_name" class="form-control" placeholder="e.g. Corporate Director / Emergency Case" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Select Service Required</label>
            <select name="service_category_id" class="form-select" required>
              @foreach(\App\Models\ServiceCategory::where('is_active', true)->get() as $sc)
                <option value="{{ $sc->id }}">{{ $sc->prefix }} - {{ $sc->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Reason for Priority Override</label>
            <input type="text" name="override_reason" class="form-control" placeholder="e.g. SBP urgent inspection / Medical emergency" required>
          </div>
          <div class="row g-2">
            <div class="col-6">
              <label class="form-label small">Phone (Optional)</label>
              <input type="text" name="customer_phone" class="form-control" placeholder="0300-0000000">
            </div>
            <div class="col-6">
              <label class="form-label small">CNIC (Optional)</label>
              <input type="text" name="customer_cnic" class="form-control" placeholder="42101-0000000-0">
            </div>
          </div>
        </div>
        <div class="modal-footer border-top">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger fw-bold">Authorize & Place at Head of Queue</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Branch Manager Floor Audio Broadcast Modal -->
<div class="modal fade" id="managerBroadcastModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form action="{{ route('admin.manager.broadcastAnnouncement') }}" method="POST">
        @csrf
        <input type="hidden" name="branch_id" value="{{ $selectedBranchId }}">
        <div class="modal-header border-bottom bg-warning text-dark">
          <h5 class="modal-title fw-bold"><i class="bx bx-broadcast me-2"></i>Branch Manager Floor Audio PA Broadcast</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning py-2 small mb-3">
            <i class="bx bx-info-circle me-1"></i> This broadcast will immediately play through all Waiting Hall Lobby TV loudspeakers and teller consoles in Urdu and English.
          </div>

          <!-- Quick Template Selector -->
          <div class="mb-3">
            <label class="form-label fw-bold">Select Standard Banking Announcement Template (Optional):</label>
            <div class="d-flex flex-wrap gap-2">
              <button type="button" class="btn btn-xs btn-outline-secondary" onclick="applyBroadcastTemplate('Utility bill counters are open', 'Attention all valued customers, counters 1, 2, and 3 are now accepting utility bill deposits.', 'توجہ فرمائیں: تمام معزز صارفین بجلی اور گیس کے بل کاؤنٹر نمبر 1، 2 اور 3 پر جمع کرا سکتے ہیں۔')">
                Utility Bills Open
              </button>
              <button type="button" class="btn btn-xs btn-outline-secondary" onclick="applyBroadcastTemplate('SBP Priority Reminder', 'Special Persons, Senior Citizens, and Pensioners are requested to proceed to Priority Counter 1 for fast-track service.', 'معذور افراد، بزرگ شہری اور پنشنرز ترجیحی کاؤنٹر نمبر 1 پر تشریف لائیں۔')">
                SBP Priority Desk
              </button>
              <button type="button" class="btn btn-xs btn-outline-secondary" onclick="applyBroadcastTemplate('Branch Closing Notice', 'Notice: Banking counters will close in 15 minutes. Please proceed to your designated teller.', 'اعلان: بینکنگ کے اوقات 15 منٹ میں اختتام پذیر ہوں گے۔ برائے مہربانی اپنے کاؤنٹر پر تشریف لے جائیں۔')">
                15-Min Closing Notice
              </button>
              <button type="button" class="btn btn-xs btn-outline-secondary" onclick="applyBroadcastTemplate('Biometric Re-verification', 'All customers opening new accounts are requested to have their biometric verification completed at Desk 4.', 'نئے اکاؤنٹ کھولنے والے معزز صارفین سے گزارش ہے کہ ڈیسک نمبر 4 سے بائیومیٹرک تصدیق مکمل کرائیں۔')">
                Biometric KYC
              </button>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold">English Announcement Text (Web Speech Voice)</label>
            <textarea name="voice_text_en" id="broadcastEn" class="form-control" rows="2" placeholder="e.g. Attention all valued customers, Counter 1 is now open for cash deposits." required></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold">Urdu Announcement Text (اردو آواز)</label>
            <textarea name="voice_text_ur" id="broadcastUr" class="form-control text-end urdu-font" rows="2" placeholder="مثال: توجہ فرمائیں! کاؤنٹر نمبر 1 کیش جمع کرانے کے لیے کھلا ہے۔" required style="font-family: 'Noto Nastaliq Urdu', Tahoma;"></textarea>
          </div>
        </div>
        <div class="modal-footer border-top">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning fw-bold px-4">
            <i class="bx bx-broadcast me-1"></i> Broadcast Across Lobby Speakers
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  function applyBroadcastTemplate(title, en, ur) {
    document.getElementById('broadcastEn').value = en;
    document.getElementById('broadcastUr').value = ur;
  }
</script>
@endsection
