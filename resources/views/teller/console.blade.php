@extends('layouts.admin')

@section('title', 'Teller Operator Console | Sarraf-QMS')

@section('content')
<div class="row mb-3 align-items-center">
  <div class="col-md-5">
    <h4 class="fw-bold py-1 mb-1">
      <span class="text-muted fw-light">Operations /</span> Teller Operator Workbench
    </h4>
    <p class="text-muted mb-0 small">Counter transaction processing, customer dispatch & audio queue management.</p>
  </div>
  <div class="col-md-7 d-flex justify-content-md-end flex-wrap gap-2 align-items-center">
    @if($activeCounter)
      <!-- Counter-Top Pole LED Display Link -->
      <a href="{{ route('display.pole', $counterId ?? $activeCounter->id) }}" target="_blank" class="btn btn-sm btn-outline-info shadow-xs" title="Open digital LED pole display for this counter">
        <i class="bx bx-tv me-1"></i> Counter Pole Display
      </a>

      <!-- Direct / Jump Call Token Modal Button -->
      <button type="button" class="btn btn-sm btn-outline-primary shadow-xs" data-bs-toggle="modal" data-bs-target="#callSpecificModal">
        <i class="bx bx-dialpad me-1"></i> Direct / Jump Call
      </button>

      <!-- Emergency VIP Injection Button -->
      <button type="button" class="btn btn-sm btn-outline-danger shadow-xs" data-bs-toggle="modal" data-bs-target="#emergencyInjectModal">
        <i class="bx bx-shield-plus me-1"></i> VIP Injection
      </button>
    @endif

    <!-- Counter Switcher -->
    <form method="GET" action="{{ route('teller.index') }}" class="d-flex align-items-center">
      <input type="hidden" name="branch_id" value="{{ $selectedBranchId }}">
      <div class="input-group input-group-sm">
        <label class="input-group-text bg-white fw-semibold">
          <i class="bx bx-desktop text-success me-1"></i> My Counter:
        </label>
        <select class="form-select fw-bold text-primary" name="counter_id" onchange="this.form.submit()">
          @foreach($counters as $cnt)
            <option value="{{ $cnt->id }}" {{ $activeCounter && $cnt->id == $activeCounter->id ? 'selected' : '' }}>
              #{{ $cnt->counter_number }} - {{ $cnt->name }}
            </option>
          @endforeach
        </select>
      </div>
    </form>
  </div>
</div>

@if($activeCounter)
  <!-- Cash Drawer & Operational Break Control Bar -->
  <div class="card mb-3 border-0 shadow-sm bg-white">
    <div class="card-body py-2 px-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
      <!-- Cash Drawer Balance & Denomination Counter -->
      <div class="d-flex align-items-center">
        <div class="avatar avatar-sm rounded-circle bg-label-success me-2">
          <i class="bx bx-money fs-4"></i>
        </div>
        <div>
          <small class="text-muted d-block" style="font-size: 10px;">Cash Drawer Balance</small>
          <strong class="text-success fs-6">PKR {{ number_format($activeCounter->current_cash_drawer, 2) }}</strong>
          <button type="button" class="btn btn-xs btn-outline-success ms-2 px-2 py-0" data-bs-toggle="modal" data-bs-target="#denominationModal">
            <i class="bx bx-calculator me-1"></i> Denomination Breakdown
          </button>
        </div>
      </div>

      <!-- Break Management Status -->
      <div class="d-flex align-items-center gap-2">
        @if($activeCounter->status === 'break')
          <span class="badge bg-danger py-2 px-3">
            <i class="bx bx-coffee me-1"></i> On Break: {{ $activeCounter->break_reason }}
          </span>
          <form action="{{ route('teller.resumeWork', $activeCounter->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-success fw-bold">
              <i class="bx bx-play me-1"></i> Resume Counter Work
            </button>
          </form>
        @else
          <span class="badge bg-label-success py-2 px-3">
            <i class="bx bx-check-circle me-1"></i> Station Active & Online
          </span>
          <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#takeBreakModal">
            <i class="bx bx-coffee me-1"></i> Take Break / Vault Drop
          </button>
        @endif
      </div>
    </div>
  </div>

  <!-- Audio Voice PA Engine Control Strip -->
  <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(90deg, #f8f9fc 0%, #edf2f9 100%); border-left: 4px solid #696cff !important;">
    <div class="card-body py-2 px-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
      <div class="d-flex align-items-center">
        <div class="avatar avatar-xs rounded bg-label-primary me-2 d-flex align-items-center justify-content-center">
          <i class="bx bx-volume-full text-primary fs-5"></i>
        </div>
        <div>
          <span class="fw-bold text-dark small">Teller Voice PA Engine:</span>
          <span class="badge bg-label-success ms-1" id="voiceStatusBadge" style="font-size: 10px;">
            <i class="bx bx-check-double me-1"></i> SBP BSC Bilingual Voice Active
          </span>
        </div>
      </div>

      <div class="d-flex align-items-center flex-wrap gap-2">
        <!-- Voice Language Mode -->
        <div class="d-flex align-items-center">
          <small class="text-muted me-1 small">Language:</small>
          <select class="form-select form-select-sm text-primary fw-semibold" id="voiceLangSelect" style="width: auto; font-size: 11px;" onchange="changeVoiceLanguage(this.value)">
            <option value="bilingual">Bilingual (English + اردو)</option>
            <option value="urdu">Urdu Only (صرف اردو)</option>
            <option value="english">English Only</option>
          </select>
        </div>

        <!-- Volume Slider -->
        <div class="d-flex align-items-center ms-2">
          <i class="bx bx-volume-low text-muted me-1 small"></i>
          <input type="range" class="form-range" style="width: 75px;" min="0.1" max="1.0" step="0.1" id="voiceVolRange" value="1.0" onchange="changeVoiceVolume(this.value)" title="Volume Slider">
        </div>

        <!-- Mute/Unmute -->
        <button type="button" class="btn btn-xs btn-outline-secondary" id="voiceMuteToggleBtn" onclick="toggleVoiceMute()">
          <i class="bx bx-volume-mute me-1" id="muteIcon"></i> <span id="muteText">Mute</span>
        </button>

        <!-- Test Speaker Button -->
        <button type="button" class="btn btn-xs btn-outline-warning fw-semibold ms-1" onclick="testTellerVoice()">
          <i class="bx bx-play-circle me-1"></i> Test Chime & Voice
        </button>
      </div>
    </div>
  </div>
@endif

@if(!$activeCounter)
  <div class="alert alert-warning">
    <i class="bx bx-error-circle me-1"></i> Please select or create a counter to begin teller operations.
  </div>
@else
  <div class="row g-4">
    <!-- Left Main Workbench Column -->
    <div class="col-lg-8">
      <!-- Active Serving / Called Token Card -->
      <div class="card shadow-sm mb-4 border-2 {{ $currentToken ? ($currentToken->status === 'serving' ? 'border-primary' : 'border-warning') : 'border-secondary' }}">
        <div class="card-header d-flex justify-content-between align-items-center bg-light border-bottom py-3">
          <div class="d-flex align-items-center">
            <span class="badge bg-primary fs-6 me-2">Counter #{{ $activeCounter->counter_number }}</span>
            <span class="fw-bold text-dark">{{ $activeCounter->name }}</span>
            @if($activeCounter->type === 'priority_dedicated')
              <span class="badge bg-label-danger ms-2" style="font-size: 10px;">
                <i class="bx bx-shield-quarter me-1"></i> SBP Dedicated Priority Desk
              </span>
            @endif
          </div>
          <div>
            @if($currentToken)
              {!! $currentToken->status_badge !!}
            @else
              <span class="badge bg-label-secondary">Ready / Idle</span>
            @endif
          </div>
        </div>

        <div class="card-body p-4 text-center">
          @if($currentToken)
            <!-- Token Display Banner -->
            <div class="mb-3">
              <span class="badge bg-label-{{ $currentToken->serviceCategory->color ?? 'primary' }} px-3 py-1 mb-2 fs-tiny">
                {{ $currentToken->serviceCategory->name ?? '' }} ({{ $currentToken->serviceCategory->name_ur ?? '' }})
              </span>
              <h1 class="display-3 fw-bolder text-primary mb-0 font-monospace" style="letter-spacing: 2px;">
                {{ $currentToken->token_number }}
              </h1>
              <div class="fs-4 fw-bolder text-dark mt-1">
                <span class="text-primary">{{ $currentToken->salutation ?? 'Mr.' }}</span> {{ $currentToken->customer_name }}
              </div>
              <div class="text-muted small">
                <i class="bx bx-phone me-1"></i> {{ $currentToken->customer_phone ?? 'Walk-in' }} &bull;
                <i class="bx bx-id-card me-1"></i> CNIC: {{ $currentToken->customer_cnic ?? 'N/A' }}
                @if($currentToken->account_number)
                  &bull; <i class="bx bx-credit-card me-1 text-primary"></i> A/C: <strong>{{ $currentToken->account_number }}</strong>
                @endif
              </div>
              @if($currentToken->transaction_purpose || $currentToken->expected_amount)
                <div class="mt-2">
                  <span class="badge bg-label-info px-3 py-1">
                    <i class="bx bx-briefcase me-1"></i> Purpose: <strong>{{ $currentToken->transaction_purpose ?? 'General Banking' }}</strong>
                    @if($currentToken->expected_amount)
                      &bull; Exp. Cash: <strong>PKR {{ number_format($currentToken->expected_amount, 2) }}</strong>
                    @endif
                  </span>
                </div>
              @endif
            </div>

            <!-- Priority Notice -->
            <div class="d-inline-flex align-items-center badge bg-label-{{ $currentToken->priorityCategory->badge_color ?? 'primary' }} px-3 py-2 rounded-pill mb-3">
              <i class="bx {{ $currentToken->priorityCategory->icon ?? 'bx-user' }} me-2 fs-5"></i>
              <span class="fw-bold">{{ $currentToken->priorityCategory->name }}</span>
              @if($currentToken->priorityCategory->code !== 'REGULAR')
                <span class="ms-1">({{ $currentToken->priorityCategory->weight_multiplier }}x Fast-Track)</span>
              @endif
            </div>

            <!-- Timing Metadata -->
            <div class="row g-2 justify-content-center mb-4">
              <div class="col-sm-3 col-6">
                <div class="p-2 border rounded bg-light">
                  <small class="text-muted d-block" style="font-size: 11px;">Issued At</small>
                  <strong>{{ $currentToken->issued_at?->format('h:i:s A') }}</strong>
                </div>
              </div>
              <div class="col-sm-3 col-6">
                <div class="p-2 border rounded bg-light">
                  <small class="text-muted d-block" style="font-size: 11px;">Waited</small>
                  <strong class="text-warning">{{ round($currentToken->waiting_time_seconds / 60, 1) }} Mins</strong>
                </div>
              </div>
              <div class="col-sm-3 col-6">
                <div class="p-2 border rounded bg-light">
                  <small class="text-muted d-block" style="font-size: 11px;">Recall Count</small>
                  <strong>{{ $currentToken->recall_count }}</strong>
                </div>
              </div>
            </div>

            <!-- Primary Action Buttons for Current Customer -->
            <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
              <!-- Recall Customer with Direct Sound -->
              <form action="{{ route('teller.recall', $currentToken->id) }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="counter_id" value="{{ $activeCounter->id }}">
                <button type="submit" class="btn btn-warning px-3 shadow-xs">
                  <i class="bx bx-bell me-1"></i> Recall Customer (Audio PA)
                </button>
              </form>

              <!-- Start Serving -->
              @if($currentToken->status === 'called')
                <form action="{{ route('teller.startServing', $currentToken->id) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-success px-4 fw-bold shadow-xs">
                    <i class="bx bx-play-circle me-1"></i> Start Serving
                  </button>
                </form>
              @endif

              <!-- Complete Service Modal Button -->
              <button type="button" class="btn btn-primary px-4 fw-bold shadow-xs" data-bs-toggle="modal" data-bs-target="#completeModal">
                <i class="bx bx-check-circle me-1"></i> Complete Transaction
              </button>

              <!-- Put on Hold -->
              <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#holdModal">
                <i class="bx bx-pause me-1"></i> Hold
              </button>

              <!-- Transfer -->
              <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#transferModal">
                <i class="bx bx-transfer me-1"></i> Transfer
              </button>

              <!-- No-Show / Skip -->
              <form action="{{ route('teller.noShow', $currentToken->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Mark customer as No-Show?')">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                  <i class="bx bx-user-x me-1"></i> No Show
                </button>
              </form>
            </div>

          @else
            <!-- Idle / Call Next State -->
            <div class="py-5">
              <div class="avatar avatar-xl rounded-circle bg-label-primary mx-auto mb-3" style="width: 80px; height: 80px;">
                <i class="bx bx-user-voice fs-1 text-primary" style="line-height: 80px;"></i>
              </div>
              <h3 class="fw-bold mb-1">Counter Available</h3>
              <p class="text-muted mb-4">
                You are online at <strong>Counter #{{ $activeCounter->counter_number }}</strong>. <br>
                Press Call Next to summon the highest priority waiting customer with automatic Urdu & English audio dispatch.
              </p>

              <form action="{{ route('teller.callNext') }}" method="POST">
                @csrf
                <input type="hidden" name="counter_id" value="{{ $activeCounter->id }}">
                <button type="submit" class="btn btn-primary btn-lg px-5 py-3 shadow-lg fs-5 fw-bold">
                  <i class="bx bx-bell me-2 fs-4"></i> CALL NEXT CUSTOMER
                </button>
              </form>
            </div>
          @endif
        </div>
      </div>

      <!-- Today's Completed by this Teller -->
      <div class="card shadow-sm">
        <div class="card-header border-bottom pb-3">
          <h5 class="mb-0 fw-bold"><i class="bx bx-history me-2 text-primary"></i>My Completed Transactions Today</h5>
        </div>
        <div class="table-responsive text-nowrap">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Token</th>
                <th>Customer</th>
                <th>Service</th>
                <th>Serve Time</th>
                <th>Txn Amount</th>
                <th>Reference</th>
              </tr>
            </thead>
            <tbody>
              @forelse($myCompletedTokens as $comp)
                <tr>
                  <td><strong class="text-primary">{{ $comp->token_number }}</strong></td>
                  <td>{{ $comp->customer_name }}</td>
                  <td><span class="badge bg-label-{{ $comp->serviceCategory->color ?? 'primary' }}">{{ $comp->serviceCategory->name ?? '' }}</span></td>
                  <td>{{ round($comp->serving_time_seconds / 60, 1) }}m</td>
                  <td>
                    @if($comp->transaction_amount)
                      <span class="fw-bold text-success">PKR {{ number_format($comp->transaction_amount, 2) }}</span>
                    @else
                      -
                    @endif
                  </td>
                  <td><small class="font-monospace text-muted">{{ $comp->transaction_reference ?? '-' }}</small></td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-3 text-muted">No completed transactions today yet.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Right Column: Live Waiting Queue -->
    <div class="col-lg-4">
      <!-- Call Next Quick Card if Serving -->
      @if($currentToken)
        <div class="card bg-label-primary border-0 mb-4 p-3 text-center">
          <small class="text-primary fw-bold text-uppercase">Next Action</small>
          <p class="small text-muted mb-2">Once this transaction is finalized, you can summon the next token.</p>
          <form action="{{ route('teller.callNext') }}" method="POST">
            @csrf
            <input type="hidden" name="counter_id" value="{{ $activeCounter->id }}">
            <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold">
              <i class="bx bx-fast-forward me-1"></i> Call Next (Auto-Complete Current)
            </button>
          </form>
        </div>
      @endif

      <!-- Waiting Queue List -->
      <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center border-bottom pb-3">
          <div>
            <h5 class="mb-0 fw-bold"><i class="bx bx-list-ol me-2 text-primary"></i>Waiting Queue</h5>
            <small class="text-muted">Eligible for this counter</small>
          </div>
          <span class="badge bg-warning fs-6 px-3">{{ $waitingTokens->count() }} Waiting</span>
        </div>
        <div class="card-body p-2" style="max-height: 520px; overflow-y: auto;">
          @forelse($waitingTokens as $idx => $wait)
            <div class="p-3 border rounded mb-2 bg-white shadow-xs d-flex align-items-center justify-content-between">
              <div>
                <div class="d-flex align-items-center mb-1">
                  <span class="badge bg-light text-dark font-monospace me-2">#{{ $idx + 1 }}</span>
                  <strong class="fs-6 text-primary me-2">{{ $wait->token_number }}</strong>
                  <span class="badge bg-label-{{ $wait->priorityCategory->badge_color ?? 'secondary' }}" style="font-size: 10px;">
                    <i class="bx {{ $wait->priorityCategory->icon ?? 'bx-user' }} me-1"></i>{{ $wait->priorityCategory->name }}
                  </span>
                </div>
                <div class="fw-semibold text-truncate small" style="max-width: 150px;">
                  <span class="text-primary">{{ $wait->salutation ?? 'Mr.' }}</span> {{ $wait->customer_name }}
                </div>
                <small class="text-muted">{{ $wait->serviceCategory->name ?? '' }}</small>
              </div>
              <div class="text-end">
                <span class="badge bg-label-warning d-block mb-1" style="font-size: 10px;">
                  <i class="bx bx-time me-1"></i>{{ $wait->created_at->diffForHumans(null, true) }}
                </span>
                <!-- Fast 1-Click Direct Call from Queue List -->
                <form action="{{ route('teller.callSpecific') }}" method="POST" class="d-inline">
                  @csrf
                  <input type="hidden" name="counter_id" value="{{ $activeCounter->id }}">
                  <input type="hidden" name="token_number" value="{{ $wait->token_number }}">
                  <button type="submit" class="btn btn-xs btn-outline-primary px-2" title="Directly summon this customer right now">
                    <i class="bx bx-phone-call me-1"></i> Call
                  </button>
                </form>
              </div>
            </div>
          @empty
            <div class="text-center py-5 text-muted">
              <i class="bx bx-check-double fs-1 text-success mb-2"></i>
              <div class="fw-bold">Queue is Empty</div>
              <small>No customers waiting for your assigned services.</small>
            </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  <!-- Complete Transaction Modal -->
  @if($currentToken)
    <div class="modal fade" id="completeModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form action="{{ route('teller.complete', $currentToken->id) }}" method="POST">
            @csrf
            <div class="modal-header border-bottom">
              <h5 class="modal-title fw-bold">Complete Token: {{ $currentToken->token_number }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label fw-semibold">Transaction Amount (PKR)</label>
                <div class="input-group">
                  <span class="input-group-text">PKR</span>
                  <input type="number" step="0.01" name="transaction_amount" class="form-control" placeholder="Optional transaction volume">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Transaction Reference / Receipt No</label>
                <input type="text" name="transaction_reference" class="form-control" placeholder="e.g. TXN-DEP-10293">
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Teller Service Notes</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Record internal notes for customer history audit"></textarea>
              </div>
            </div>
            <div class="modal-footer border-top">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-success fw-bold px-4">Save & Complete</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Hold Modal -->
    <div class="modal fade" id="holdModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form action="{{ route('teller.hold', $currentToken->id) }}" method="POST">
            @csrf
            <div class="modal-header border-bottom">
              <h5 class="modal-title fw-bold">Put Token On Hold</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label fw-semibold">Reason for Hold</label>
                <select name="hold_reason" class="form-select" required>
                  <option value="Customer gone for biometric verification">Customer gone for biometric verification</option>
                  <option value="Awaiting authorization / manager sign-off">Awaiting authorization / manager sign-off</option>
                  <option value="Missing KYC identity documents / photocopies">Missing KYC identity documents / photocopies</option>
                  <option value="Awaiting cash voucher deposit slip fill">Awaiting cash voucher deposit slip fill</option>
                </select>
              </div>
            </div>
            <div class="modal-footer border-top">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-warning fw-bold">Confirm Hold</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Transfer Modal -->
    <div class="modal fade" id="transferModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form action="{{ route('teller.transfer', $currentToken->id) }}" method="POST">
            @csrf
            <div class="modal-header border-bottom">
              <h5 class="modal-title fw-bold">Transfer Customer to Another Counter</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label fw-semibold">Destination Counter</label>
                <select name="to_counter_id" class="form-select" required>
                  @foreach($counters as $targetCnt)
                    @if($targetCnt->id != $activeCounter->id)
                      <option value="{{ $targetCnt->id }}">
                        #{{ $targetCnt->counter_number }} - {{ $targetCnt->name }} ({{ $targetCnt->currentUser->name ?? 'No Teller' }})
                      </option>
                    @endif
                  @endforeach
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Transfer Reason & Instructions</label>
                <textarea name="transfer_notes" class="form-control" rows="2" placeholder="e.g. Deposit verified, proceed for pay order issuance"></textarea>
              </div>
            </div>
            <div class="modal-footer border-top">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-info fw-bold">Transfer Immediately</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif

  <!-- Direct Call Specific Token Modal -->
  <div class="modal fade" id="callSpecificModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form action="{{ route('teller.callSpecific') }}" method="POST">
          @csrf
          <input type="hidden" name="counter_id" value="{{ $activeCounter->id }}">
          <div class="modal-header border-bottom bg-primary text-white">
            <h5 class="modal-title fw-bold text-white"><i class="bx bx-dialpad me-2"></i>Direct Customer Call (Jump Queue)</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="text-muted small mb-3">
              Directly summon an authorized customer ticket returned from photocopy, manager room, or biometric verification.
            </p>
            <div class="mb-3">
              <label class="form-label fw-bold">Enter Exact Token Number</label>
              <input type="text" name="token_number" id="directTokenInput" class="form-control form-control-lg font-monospace text-uppercase fw-bold text-center text-primary" placeholder="e.g. C-101" required style="letter-spacing: 2px;">
            </div>
            <!-- Quick Token Chips from Waiting Queue -->
            @if($waitingTokens->isNotEmpty())
              <label class="form-label small text-muted">Quick Pick from Waiting Queue:</label>
              <div class="d-flex flex-wrap gap-1 mb-2">
                @foreach($waitingTokens->take(8) as $qToken)
                  <button type="button" class="btn btn-xs btn-outline-secondary font-monospace" onclick="document.getElementById('directTokenInput').value='{{ $qToken->token_number }}'">
                    {{ $qToken->token_number }}
                  </button>
                @endforeach
              </div>
            @endif
          </div>
          <div class="modal-footer border-top">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary fw-bold px-4">
              <i class="bx bx-bell me-1"></i> Summon Token Immediately
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Cash Drawer Denomination Counter Modal -->
  <div class="modal fade" id="denominationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <form action="{{ route('teller.updateCashDrawer', $activeCounter->id) }}" method="POST" id="denomForm">
          @csrf
          <div class="modal-header border-bottom bg-success text-white">
            <h5 class="modal-title fw-bold text-white"><i class="bx bx-calculator me-2"></i>Cash Drawer Physical Denomination Tally</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="text-muted small mb-3">
              Standard Bank Cashier Denomination Sheet (SBP Currency Verification & Cash Drawer Reconciliation).
            </p>
            <div class="table-responsive">
              <table class="table table-bordered table-sm align-middle text-center mb-3">
                <thead class="table-light">
                  <tr>
                    <th>Currency Note</th>
                    <th style="width: 140px;">Piece Count (تعداد)</th>
                    <th>Subtotal (PKR)</th>
                  </tr>
                </thead>
                <tbody>
                  @php
                    $denoms = [5000, 1000, 500, 100, 50, 20, 10];
                  @endphp
                  @foreach($denoms as $d)
                    <tr>
                      <td class="fw-bold text-start ps-3">
                        <span class="badge bg-label-dark me-2">₨ {{ number_format($d) }}</span> Note
                      </td>
                      <td>
                        <input type="number" min="0" value="0" class="form-control form-control-sm text-center denom-input fw-bold" data-unit="{{ $d }}" oninput="calculateDenominations()">
                      </td>
                      <td class="text-end pe-3 fw-bold denom-subtotal" id="subtotal_{{ $d }}">
                        PKR 0.00
                      </td>
                    </tr>
                  @endforeach
                  <tr>
                    <td class="fw-bold text-start ps-3">
                      <span class="badge bg-label-secondary me-2">Coins & Loose</span> PKR Cash
                    </td>
                    <td>
                      <input type="number" min="0" step="1" value="0" class="form-control form-control-sm text-center fw-bold" id="looseCoinsInput" oninput="calculateDenominations()">
                    </td>
                    <td class="text-end pe-3 fw-bold" id="subtotal_coins">
                      PKR 0.00
                    </td>
                  </tr>
                </tbody>
                <tfoot class="table-dark">
                  <tr>
                    <th class="text-start ps-3">TOTAL PIECES: <span id="totalPiecesBadge" class="text-warning">0</span> Notes</th>
                    <th class="text-end">GRAND TOTAL:</th>
                    <th class="text-end pe-3 fs-5 text-warning fw-bolder font-monospace" id="grandTotalDisplay">
                      PKR 0.00
                    </th>
                  </tr>
                </tfoot>
              </table>
            </div>

            <!-- Hidden input that carries the calculated grand total -->
            <input type="hidden" name="current_cash_drawer" id="computedDrawerTotal" value="{{ $activeCounter->current_cash_drawer }}">
          </div>
          <div class="modal-footer border-top">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success fw-bold px-4">
              <i class="bx bx-check-shield me-1"></i> Confirm & Sync Cash Drawer
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Take Break Modal -->
  <div class="modal fade" id="takeBreakModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form action="{{ route('teller.takeBreak', $activeCounter->id) }}" method="POST">
          @csrf
          <div class="modal-header border-bottom">
            <h5 class="modal-title fw-bold">Pause Station & Take Break</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label fw-bold">Select Official Break Reason</label>
              <select name="break_reason" class="form-select" required>
                <option value="Lunch Break">Lunch Break (دوپہر کا کھانا)</option>
                <option value="Prayer Break">Prayer Break (نماز کا وقفہ)</option>
                <option value="Cash Balancing & Vault Drop">Cash Balancing & Vault Drop (کیش گنتی و والٹ ٹرانسفر)</option>
                <option value="Tea Break">Tea Break (چائے کا وقفہ)</option>
                <option value="System Reconciliation">System Reconciliation (سسٹم آڈٹ)</option>
              </select>
            </div>
          </div>
          <div class="modal-footer border-top">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-warning fw-bold">Pause Counter Station</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Emergency / Manager Override Injection Modal -->
  <div class="modal fade" id="emergencyInjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form action="{{ route('admin.manager.injectEmergencyToken') }}" method="POST">
          @csrf
          <input type="hidden" name="branch_id" value="{{ $selectedBranchId }}">
          <div class="modal-header border-bottom bg-danger text-white">
            <h5 class="modal-title fw-bold text-white"><i class="bx bx-shield-plus me-2"></i>Manager Emergency Priority Injection</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="alert alert-danger py-2 small mb-3">
              <i class="bx bx-info-circle me-1"></i> Manager Override forces this token to the absolute top of the queue with maximum precedence score (999).
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Customer Full Name</label>
              <input type="text" name="customer_name" class="form-control" placeholder="e.g. VIP Diplomat / Medical Emergency Customer" required>
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
              <label class="form-label fw-bold">Mandatory Manager Justification / Reason</label>
              <input type="text" name="override_reason" class="form-control" placeholder="e.g. High net-worth corporate urgent drawdown / Senior medical case" required>
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
            <button type="submit" class="btn btn-danger fw-bold">Authorize & Inject at Top of Queue</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endif
@endsection

@push('scripts')
<script>
  // Web Audio Context for Authentic Bank Chime
  let tellerAudioCtx = null;
  let isVoiceMuted = localStorage.getItem('sarraf_teller_voice_mute') === 'true';
  let voiceVolume = parseFloat(localStorage.getItem('sarraf_teller_voice_vol') || '1.0');
  let voiceLangMode = localStorage.getItem('sarraf_teller_voice_lang') || 'bilingual';

  // Initialize UI controls from storage
  document.addEventListener('DOMContentLoaded', () => {
    const muteBtn = document.getElementById('voiceMuteToggleBtn');
    const muteIcon = document.getElementById('muteIcon');
    const muteText = document.getElementById('muteText');
    const volInput = document.getElementById('voiceVolRange');
    const langSelect = document.getElementById('voiceLangSelect');
    const badge = document.getElementById('voiceStatusBadge');

    if (volInput) volInput.value = voiceVolume;
    if (langSelect) langSelect.value = voiceLangMode;

    if (isVoiceMuted) {
      if (muteIcon) muteIcon.className = 'bx bx-volume-full me-1';
      if (muteText) muteText.innerText = 'Unmute';
      if (badge) {
        badge.className = 'badge bg-label-danger ms-1';
        badge.innerHTML = '<i class="bx bx-volume-mute me-1"></i> Audio Muted';
      }
    }
  });

  function toggleVoiceMute() {
    isVoiceMuted = !isVoiceMuted;
    localStorage.setItem('sarraf_teller_voice_mute', isVoiceMuted);

    const muteIcon = document.getElementById('muteIcon');
    const muteText = document.getElementById('muteText');
    const badge = document.getElementById('voiceStatusBadge');

    if (isVoiceMuted) {
      if (muteIcon) muteIcon.className = 'bx bx-volume-full me-1';
      if (muteText) muteText.innerText = 'Unmute';
      if (badge) {
        badge.className = 'badge bg-label-danger ms-1';
        badge.innerHTML = '<i class="bx bx-volume-mute me-1"></i> Audio Muted';
      }
    } else {
      if (muteIcon) muteIcon.className = 'bx bx-volume-mute me-1';
      if (muteText) muteText.innerText = 'Mute';
      if (badge) {
        badge.className = 'badge bg-label-success ms-1';
        badge.innerHTML = '<i class="bx bx-check-double me-1"></i> Voice Engine Online';
      }
    }
  }

  function changeVoiceVolume(val) {
    voiceVolume = parseFloat(val);
    localStorage.setItem('sarraf_teller_voice_vol', voiceVolume);
  }

  function changeVoiceLanguage(mode) {
    voiceLangMode = mode;
    localStorage.setItem('sarraf_teller_voice_lang', voiceLangMode);
  }

  // Play Bank Chime using Web Audio API (Two-Tone Ding Dong Airport / Bank Frequency)
  function playBankChime(callback) {
    if (isVoiceMuted) {
      if (callback) callback();
      return;
    }
    try {
      const AudioContext = window.AudioContext || window.webkitAudioContext;
      tellerAudioCtx = tellerAudioCtx || new AudioContext();
      if (tellerAudioCtx.state === 'suspended') {
        tellerAudioCtx.resume();
      }

      const now = tellerAudioCtx.currentTime;

      // Note 1: 587.33 Hz (D5)
      const osc1 = tellerAudioCtx.createOscillator();
      const gain1 = tellerAudioCtx.createGain();
      osc1.type = 'sine';
      osc1.frequency.setValueAtTime(587.33, now);
      gain1.gain.setValueAtTime(0.3 * voiceVolume, now);
      gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.6);
      osc1.connect(gain1);
      gain1.connect(tellerAudioCtx.destination);
      osc1.start(now);
      osc1.stop(now + 0.6);

      // Note 2: 440 Hz (A4)
      const osc2 = tellerAudioCtx.createOscillator();
      const gain2 = tellerAudioCtx.createGain();
      osc2.type = 'sine';
      osc2.frequency.setValueAtTime(440, now + 0.35);
      gain2.gain.setValueAtTime(0.35 * voiceVolume, now + 0.35);
      gain2.gain.exponentialRampToValueAtTime(0.001, now + 1.2);
      osc2.connect(gain2);
      gain2.connect(tellerAudioCtx.destination);
      osc2.start(now + 0.35);
      osc2.stop(now + 1.2);

      setTimeout(() => {
        if (callback) callback();
      }, 1200);
    } catch (e) {
      console.warn("Chime error:", e);
      if (callback) callback();
    }
  }

  // Web Speech API Bilingual Voice Synthesizer
  function speakAnnouncement(textEn, textUr) {
    if (isVoiceMuted || !('speechSynthesis' in window)) return;

    playBankChime(() => {
      if (voiceLangMode === 'urdu') {
        speakUrduOnly(textUr);
      } else if (voiceLangMode === 'english') {
        speakEnglishOnly(textEn);
      } else {
        // Bilingual: English first, then Urdu immediately after
        const utteranceEn = new SpeechSynthesisUtterance(textEn);
        utteranceEn.rate = 0.9;
        utteranceEn.volume = voiceVolume;
        utteranceEn.pitch = 1.0;
        utteranceEn.lang = 'en-US';

        utteranceEn.onend = () => {
          setTimeout(() => {
            speakUrduOnly(textUr);
          }, 300);
        };

        window.speechSynthesis.speak(utteranceEn);
      }
    });
  }

  function speakUrduOnly(textUr) {
    const utteranceUr = new SpeechSynthesisUtterance(textUr);
    utteranceUr.rate = 0.85;
    utteranceUr.volume = voiceVolume;
    utteranceUr.pitch = 1.0;
    utteranceUr.lang = 'ur-PK';
    window.speechSynthesis.speak(utteranceUr);
  }

  function speakEnglishOnly(textEn) {
    const utteranceEn = new SpeechSynthesisUtterance(textEn);
    utteranceEn.rate = 0.9;
    utteranceEn.volume = voiceVolume;
    utteranceEn.pitch = 1.0;
    utteranceEn.lang = 'en-US';
    window.speechSynthesis.speak(utteranceEn);
  }

  function testTellerVoice() {
    speakAnnouncement(
      "Token Number C 1 0 1, please proceed to Counter 01",
      "ٹوکن نمبر سی ایک سو ایک، کاؤنٹر نمبر ایک پر تشریف لائیں"
    );
  }

  // Auto-play Announcement if redirected from Call Next, Call Specific, or Recall
  @if(session('announcement_en') && session('announcement_ur'))
    document.addEventListener('DOMContentLoaded', () => {
      setTimeout(() => {
        speakAnnouncement(
          "{{ session('announcement_en') }}",
          "{{ session('announcement_ur') }}"
        );
      }, 400);
    });
  @endif

  // Denomination Breakdown Real-time Calculator
  function calculateDenominations() {
    let grandTotal = 0;
    let totalPieces = 0;

    const inputs = document.querySelectorAll('.denom-input');
    inputs.forEach(input => {
      const unit = parseInt(input.dataset.unit);
      const count = parseInt(input.value) || 0;
      const subtotal = unit * count;

      grandTotal += subtotal;
      totalPieces += count;

      const subtotalEl = document.getElementById('subtotal_' + unit);
      if (subtotalEl) {
        subtotalEl.innerText = 'PKR ' + subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      }
    });

    // Loose coins
    const looseInput = document.getElementById('looseCoinsInput');
    const looseVal = parseFloat(looseInput ? looseInput.value : 0) || 0;
    grandTotal += looseVal;

    const coinsSubtotalEl = document.getElementById('subtotal_coins');
    if (coinsSubtotalEl) {
      coinsSubtotalEl.innerText = 'PKR ' + looseVal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Grand total & pieces display
    const grandDisplay = document.getElementById('grandTotalDisplay');
    const piecesBadge = document.getElementById('totalPiecesBadge');
    const computedTotal = document.getElementById('computedDrawerTotal');

    if (grandDisplay) {
      grandDisplay.innerText = 'PKR ' + grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    if (piecesBadge) {
      piecesBadge.innerText = totalPieces.toLocaleString();
    }
    if (computedTotal) {
      computedTotal.value = grandTotal.toFixed(2);
    }
  }
</script>
@endpush
