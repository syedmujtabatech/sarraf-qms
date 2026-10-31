@extends('layouts.blank')

@section('title', 'Live Token Status: ' . $token->token_number)

@section('body-class', 'bg-light')

@section('content')
<div class="mobile-tracker-container min-vh-100 d-flex flex-column" style="max-width: 480px; margin: 0 auto; background: #f5f5f9;">
  <!-- Mobile App Header -->
  <header class="bg-primary text-white p-3 shadow-sm d-flex justify-content-between align-items-center sticky-top">
    <div class="d-flex align-items-center">
      <div class="p-2 bg-white text-primary rounded-3 me-2 shadow-xs">
        <i class="bx bxs-bank fs-4"></i>
      </div>
      <div>
        <h6 class="mb-0 fw-bold text-white">Sarraf Mobile E-Pass (صراف)</h6>
        <small class="text-white-50" style="font-size: 11px;">{{ $token->branch->name }}</small>
      </div>
    </div>

    <!-- Live Status Pulse -->
    <span class="badge bg-white text-primary px-2 py-1 rounded-pill small fw-semibold d-flex align-items-center">
      <span class="spinner-grow spinner-grow-sm text-success me-1" role="status" style="width: 8px; height: 8px;"></span>
      LIVE
    </span>
  </header>

  <!-- Notification Banner when Token is Called -->
  <div id="calledBanner" class="alert alert-warning mb-0 border-0 rounded-0 py-3 px-3 shadow text-center d-none" style="animation: pulseCard 1s infinite alternate;">
    <div class="fs-4 fw-bolder text-dark mb-1">
      <i class="bx bx-bell bx-tada text-danger me-1"></i> {{ $token->salutation ?? 'Mr.' }} {{ $token->customer_name }}, YOUR TURN HAS ARRIVED!
    </div>
    <div class="urdu-font fs-5 text-dark mb-1" style="line-height: 1.2;">
      آپ کا نمبر بلایا گیا ہے! براہِ کرم کاؤنٹر پر تشریف لائیں
    </div>
    <div class="fs-5 fw-bold text-primary" id="calledCounterText">
      Please proceed to Counter #{{ $token->counter->counter_number ?? '-' }}
    </div>
  </div>

  <!-- Main Mobile Content -->
  <main class="flex-grow-1 p-3">
    <!-- Big Token Pass Card -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-3">
      <div class="card-header bg-label-primary text-center py-3 border-bottom border-light">
        <span class="badge bg-primary text-uppercase px-3 py-1 mb-1 font-monospace" style="font-size: 11px;">
          {{ $token->serviceCategory->name }}
        </span>
        <div class="urdu-font text-muted small" style="line-height: 1;">
          {{ $token->serviceCategory->name_ur }}
        </div>
      </div>

      <div class="card-body p-4 text-center">
        <small class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Queue Token Number</small>
        <h1 class="display-3 fw-bolder text-primary mb-1 font-monospace tracking-wide" id="tokenDisplayNumber">
          {{ $token->token_number }}
        </h1>

        <div class="fs-4 fw-bolder text-dark mt-1">
          <span class="text-primary">{{ $token->salutation ?? 'Mr.' }}</span> {{ $token->customer_name }}
        </div>
        @if($token->account_number)
          <div class="small text-muted font-monospace"><i class="bx bx-credit-card me-1"></i> A/C: {{ $token->account_number }}</div>
        @endif
        @if($token->transaction_purpose)
          <div class="mt-1">
            <span class="badge bg-label-info" style="font-size: 11px;">
              {{ $token->transaction_purpose }} @if($token->expected_amount) &bull; PKR {{ number_format($token->expected_amount, 0) }} @endif
            </span>
          </div>
        @endif
        
        <!-- Priority Badge -->
        <div class="mt-2">
          <span class="badge bg-{{ $token->priorityCategory->badge_color ?? 'primary' }} px-3 py-1 rounded-pill">
            <i class="bx {{ $token->priorityCategory->icon ?? 'bx-user' }} me-1"></i>
            {{ $token->priorityCategory->name }}
          </span>
          @if($token->priorityCategory->code !== 'REGULAR')
            <small class="d-block text-danger fw-semibold mt-1" style="font-size: 11px;">
              * SBP Fast-Track Priority Precedence Active *
            </small>
          @endif
        </div>
      </div>

      <!-- Current Real-Time Status Bar -->
      <div class="p-3 text-center border-top bg-light" id="statusBoxContainer">
        <small class="text-muted d-block mb-1" style="font-size: 11px;">Current Queue Status</small>
        <div id="statusBadgeWrap">
          {!! $token->status_badge !!}
        </div>
      </div>
    </div>

    <!-- Live Waiting Stats (Hidden when completed) -->
    <div class="card shadow-sm border-0 rounded-4 p-3 mb-3" id="waitingStatsCard">
      <div class="row g-2 text-center">
        <div class="col-6 border-end">
          <div class="p-2">
            <small class="text-muted d-block" style="font-size: 11px;">Customers Ahead</small>
            <h2 class="fw-bolder mb-0 text-dark" id="tokensAheadCount">{{ $waitStats['tokens_ahead'] }}</h2>
            <small class="text-muted" style="font-size: 10px;">in this service line</small>
          </div>
        </div>
        <div class="col-6">
          <div class="p-2">
            <small class="text-muted d-block" style="font-size: 11px;">Estimated Wait</small>
            <h2 class="fw-bolder mb-0 text-warning" id="estWaitMins">~{{ $waitStats['estimated_minutes'] }}m</h2>
            <small class="text-muted" style="font-size: 10px;">approximate time</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Serving Counter Details Card -->
    <div class="card shadow-sm border-0 rounded-4 p-3 mb-3 d-none" id="servingCounterCard">
      <div class="d-flex align-items-center">
        <div class="avatar avatar-md rounded-circle bg-label-success me-3">
          <i class="bx bx-desktop fs-3 text-success"></i>
        </div>
        <div class="flex-grow-1">
          <small class="text-muted d-block" style="font-size: 11px;">Designated Station</small>
          <h5 class="fw-bold mb-0 text-dark" id="cardCounterName">Counter #--</h5>
          <small class="text-muted" id="cardTellerName">Serving Teller</small>
        </div>
      </div>
    </div>

    <!-- Feedback Button (Shown when completed) -->
    <div class="d-none" id="feedbackPromptCard">
      <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white mb-3">
        <i class="bx bx-check-circle fs-1 text-success mb-2"></i>
        <h5 class="fw-bold mb-1">Service Completed!</h5>
        <p class="text-muted small mb-3">Thank you for visiting State Bank. Please take a moment to rate our service.</p>
        <a href="{{ route('feedback.index', ['token_id' => $token->id]) }}" class="btn btn-warning btn-lg fw-bold w-100 py-3 shadow">
          <i class="bx bx-star me-1"></i> Rate Your Experience (1-5 Stars)
        </a>
      </div>
    </div>

    <!-- SBP Customer Rights Accordion -->
    <div class="card shadow-sm border-0 rounded-4 p-3 bg-white">
      <div class="d-flex align-items-center mb-2">
        <i class="bx bx-shield-quarter text-primary fs-4 me-2"></i>
        <h6 class="mb-0 fw-bold text-dark">State Bank Priority Guidelines</h6>
      </div>
      <p class="text-muted small mb-0" style="font-size: 12px;">
        As per State Bank of Pakistan (SBP) instructions, special persons (PWDs), senior citizens (60+) and pensioners receive fast-track service precedence. For assistance, contact the branch manager.
      </p>
    </div>
  </main>

  <!-- Mobile Footer -->
  <footer class="p-3 text-center text-muted small border-top bg-white">
    <div>State Bank Electronic Queue System &bull; Live Mobile Pass</div>
    <div class="mt-1" style="font-size: 10px;">Token Issued: {{ $token->created_at->format('d M Y, h:i A') }}</div>
  </footer>
</div>
@endsection

@push('styles')
<style>
  @keyframes pulseCard {
    from { transform: scale(0.98); }
    to { transform: scale(1.02); }
  }
</style>
@endpush

@push('scripts')
<script>
  const tokenNumber = "{{ $token->token_number }}";
  let previousStatus = "{{ $token->status }}";

  // Function to play phone ding & vibrate phone when called
  function notifyPhoneUser(counterNum) {
    // 1. Mobile Vibration API
    if ('vibrate' in navigator) {
      navigator.vibrate([300, 150, 300, 150, 500]);
    }

    // 2. Audio Chime
    try {
      const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
      const now = audioCtx.currentTime;
      const osc = audioCtx.createOscillator();
      const gain = audioCtx.createGain();
      osc.type = 'triangle';
      osc.frequency.setValueAtTime(880, now);
      gain.gain.setValueAtTime(0.3, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 1);
      osc.connect(gain);
      gain.connect(audioCtx.destination);
      osc.start(now);
      osc.stop(now + 1);
    } catch(e) {}
  }

  function pollMobileStatus() {
    $.ajax({
      url: "{{ url('m/api/status') }}/" + tokenNumber,
      method: 'GET',
      success: function(res) {
        // Update live counters
        $('#tokensAheadCount').text(res.tokens_ahead);
        $('#estWaitMins').text('~' + res.estimated_minutes + 'm');

        // Check for Status Change
        if (res.status !== previousStatus) {
          if (res.status === 'called') {
            notifyPhoneUser(res.counter_number);
          }
          previousStatus = res.status;
        }

        // Render Status Specific UI
        if (res.status === 'called') {
          $('#calledBanner').removeClass('d-none');
          $('#calledCounterText').text('Please proceed to Counter #' + res.counter_number);
          $('#servingCounterCard').removeClass('d-none');
          $('#cardCounterName').text('Counter #' + res.counter_number);
          $('#cardTellerName').text(res.teller_name ? 'Teller: ' + res.teller_name : 'Staff Desk');
          $('#statusBadgeWrap').html('<span class="badge bg-warning fs-6 pulse-dot"><i class="bx bx-bell me-1"></i> Called to Counter #' + res.counter_number + '</span>');
        } else if (res.status === 'serving') {
          $('#calledBanner').addClass('d-none');
          $('#servingCounterCard').removeClass('d-none');
          $('#cardCounterName').text('Counter #' + res.counter_number);
          $('#cardTellerName').text(res.teller_name ? 'Teller: ' + res.teller_name : 'Staff Desk');
          $('#statusBadgeWrap').html('<span class="badge bg-info fs-6"><i class="bx bx-user-voice me-1"></i> Currently Serving at Counter #' + res.counter_number + '</span>');
        } else if (res.status === 'completed') {
          $('#calledBanner').addClass('d-none');
          $('#waitingStatsCard').addClass('d-none');
          $('#servingCounterCard').addClass('d-none');
          $('#feedbackPromptCard').removeClass('d-none');
          $('#statusBadgeWrap').html('<span class="badge bg-success fs-6"><i class="bx bx-check-circle me-1"></i> Transaction Completed</span>');
        } else if (res.status === 'waiting') {
          $('#statusBadgeWrap').html('<span class="badge bg-warning fs-6"><i class="bx bx-time-five me-1"></i> Waiting in Queue</span>');
        }
      },
      error: function(err) {
        console.warn("Poll error:", err);
      }
    });
  }

  // Poll status every 3 seconds
  setInterval(pollMobileStatus, 3000);
</script>
@endpush
