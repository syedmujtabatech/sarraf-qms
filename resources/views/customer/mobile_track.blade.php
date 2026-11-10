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
        <h6 class="mb-0 fw-bold text-white">Sarraf Mobile E-Pass</h6>
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
      <i class="bx bx-bell bx-tada text-danger me-1"></i> {{ $token->customer_name }}, YOUR TURN HAS ARRIVED!
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
        <span class="badge bg-primary text-uppercase px-3 py-1 mb-0 font-monospace" style="font-size: 12px;">
          {{ $token->serviceCategory->name }}
        </span>
      </div>

      <div class="card-body p-4 text-center">
        <small class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Queue Token Number</small>
        <h1 class="display-3 fw-bolder text-primary mb-1 font-monospace tracking-wide" id="tokenDisplayNumber">
          {{ $token->token_number }}
        </h1>

        <div class="fs-4 fw-bolder text-dark mt-1">
          {{ $token->customer_name }}
        </div>
        @if($token->customer_phone)
          <div class="small text-muted font-monospace"><i class="bx bx-phone me-1"></i> {{ $token->customer_phone }}</div>
        @endif
        
        <!-- Priority Badge -->
        <div class="mt-2">
          <span class="badge bg-{{ $token->priorityCategory->badge_color ?? 'primary' }} px-3 py-1 rounded-pill">
            <i class="bx {{ $token->priorityCategory->icon ?? 'bx-user' }} me-1"></i>
            {{ $token->priorityCategory->name }}
          </span>
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
        <p class="text-muted small mb-3">Thank you for visiting our branch. Please take a moment to rate our service.</p>
        <a href="{{ route('feedback.index', ['token_id' => $token->id]) }}" class="btn btn-warning btn-lg fw-bold w-100 py-3 shadow">
          <i class="bx bx-star me-1"></i> Rate Your Experience (1-5 Stars)
        </a>
      </div>
    </div>

    <!-- Branch Info Card -->
    <div class="card shadow-sm border-0 rounded-4 p-3 bg-white">
      <div class="d-flex align-items-center mb-1">
        <i class="bx bx-buildings text-primary fs-4 me-2"></i>
        <h6 class="mb-0 fw-bold text-dark">{{ $token->branch->name }}</h6>
      </div>
      <p class="text-muted small mb-0" style="font-size: 12px;">
        {{ $token->branch->address ?? 'Branch Location' }} &bull; Phone: {{ $token->branch->phone ?? 'UAN: 111-SARRAF' }}
      </p>
    </div>
  </main>

  <!-- Mobile Footer -->
  <footer class="p-3 text-center text-muted small border-top bg-white">
    <div>Sarraf Banking Queue System &bull; Live Mobile Pass</div>
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

  function notifyPhoneUser(counterNum) {
    if ('vibrate' in navigator) {
      navigator.vibrate([300, 150, 300, 150, 500]);
    }
    try {
      const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
      const osc = audioCtx.createOscillator();
      const gain = audioCtx.createGain();
      osc.connect(gain);
      gain.connect(audioCtx.destination);
      osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
      osc.frequency.setValueAtTime(880, audioCtx.currentTime + 0.15); // A5
      gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.5);
      osc.start();
      osc.stop(audioCtx.currentTime + 0.5);
    } catch(e) {}
  }

  function pollMobileStatus() {
    fetch("{{ route('mobile.status', $token->token_number) }}")
      .then(res => res.json())
      .then(data => {
        // Update status badge
        const badgeWrap = document.getElementById('statusBadgeWrap');
        const calledBanner = document.getElementById('calledBanner');
        const waitingStatsCard = document.getElementById('waitingStatsCard');
        const servingCounterCard = document.getElementById('servingCounterCard');
        const feedbackPromptCard = document.getElementById('feedbackPromptCard');

        if (data.status === 'called') {
          calledBanner.classList.remove('d-none');
          document.getElementById('calledCounterText').innerText = 'Please proceed to ' + (data.counter_name || ('Counter #' + data.counter_number));
          if (previousStatus !== 'called') {
            notifyPhoneUser(data.counter_number);
          }
          badgeWrap.innerHTML = '<span class="badge bg-warning text-dark fs-6 px-3 py-2"><i class="bx bx-bell bx-tada me-1"></i> CALLED TO COUNTER</span>';
        } else if (data.status === 'serving') {
          calledBanner.classList.add('d-none');
          servingCounterCard.classList.remove('d-none');
          document.getElementById('cardCounterName').innerText = (data.counter_name || ('Counter #' + data.counter_number));
          document.getElementById('cardTellerName').innerText = 'Serving Teller: ' + (data.teller_name || 'Counter Operator');
          badgeWrap.innerHTML = '<span class="badge bg-primary fs-6 px-3 py-2"><i class="bx bx-user-check me-1"></i> CURRENTLY SERVING</span>';
        } else if (data.status === 'completed') {
          calledBanner.classList.add('d-none');
          waitingStatsCard.classList.add('d-none');
          servingCounterCard.classList.add('d-none');
          feedbackPromptCard.classList.remove('d-none');
          badgeWrap.innerHTML = '<span class="badge bg-success fs-6 px-3 py-2"><i class="bx bx-check-double me-1"></i> SERVICE COMPLETED</span>';
        } else if (data.status === 'no_show') {
          calledBanner.classList.add('d-none');
          badgeWrap.innerHTML = '<span class="badge bg-danger fs-6 px-3 py-2"><i class="bx bx-x-circle me-1"></i> NO SHOW / CANCELLED</span>';
        } else {
          badgeWrap.innerHTML = '<span class="badge bg-info fs-6 px-3 py-2"><i class="bx bx-time me-1"></i> IN WAITING QUEUE</span>';
        }

        // Update counts
        document.getElementById('tokensAheadCount').innerText = data.tokens_ahead;
        document.getElementById('estWaitMins').innerText = '~' + data.estimated_minutes + 'm';

        previousStatus = data.status;
      })
      .catch(err => console.log('Mobile tracker polling error', err));
  }

  // Poll every 3 seconds
  setInterval(pollMobileStatus, 3000);
</script>
@endpush
