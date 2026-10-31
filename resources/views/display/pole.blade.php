@extends('layouts.blank')

@section('title', "Counter #{$counter->counter_number} Pole Display | Sarraf-QMS")

@section('body-class', 'bg-black text-white overflow-hidden')

@section('content')
<div class="pole-display-container vh-100 d-flex flex-column justify-content-between p-4" style="background: radial-gradient(circle, #101728 0%, #050811 100%); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
  
  <!-- Pole Header -->
  <header class="d-flex justify-content-between align-items-center pb-3 border-bottom border-secondary border-opacity-50">
    <div class="d-flex align-items-center">
      <div class="avatar avatar-md bg-primary text-white rounded-3 me-3 d-flex align-items-center justify-content-center">
        <i class="bx bxs-bank fs-3"></i>
      </div>
      <div>
        <h4 class="mb-0 fw-bold text-white tracking-wide">صراف کیو ایم ایس &bull; SARRAF-QMS</h4>
        <small class="text-white-50">{{ $counter->branch->name }} (Code: {{ $counter->branch->code }})</small>
      </div>
    </div>
    
    <div class="text-end">
      <div class="fs-5 fw-bold font-monospace text-warning" id="poleClock">{{ now()->format('h:i:s A') }}</div>
      <span class="badge bg-secondary" id="counterStatusBadge">{{ strtoupper($counter->status) }}</span>
    </div>
  </header>

  <!-- Central High-Impact LED Matrix Simulation -->
  <main class="text-center my-auto py-4">
    <!-- Counter Badge Ribbon -->
    <div class="mb-3">
      <span class="px-5 py-2 rounded-pill fw-bolder fs-3 text-uppercase shadow-lg d-inline-block" 
            style="background: linear-gradient(135deg, #696cff 0%, #3f42c9 100%); letter-spacing: 3px; border: 2px solid rgba(255,255,255,0.3);">
        <i class="bx bx-desktop me-2"></i> COUNTER <span id="counterNumDisplay">{{ $counter->counter_number }}</span>
      </span>
      <div class="mt-2 text-white-50 fs-5 fw-semibold urdu-font">کاؤنٹر نمبر {{ $counter->counter_number }}</div>
    </div>

    <!-- Active Serving Token Glowing Digits -->
    <div id="tokenContainer" class="my-4">
      @if($counter->currentToken)
        <div class="badge bg-label-info px-4 py-1 mb-2 fs-5" id="serviceNameDisplay">
          {{ $counter->currentToken->serviceCategory->name ?? '' }}
        </div>
        <div class="display-1 fw-bolder font-monospace token-glow my-2" id="tokenNumDisplay" style="font-size: 8rem; letter-spacing: 6px; color: #ffab00;">
          {{ $counter->currentToken->token_number }}
        </div>
        <div class="fs-2 text-white fw-bold mb-1" id="customerNameDisplay">
          <span class="text-warning">{{ $counter->currentToken->salutation ?? 'Mr.' }}</span> {{ $counter->currentToken->customer_name }}
        </div>
        @if($counter->currentToken->transaction_purpose)
          <div class="badge bg-secondary mb-2" style="font-size: 13px;">
            {{ $counter->currentToken->transaction_purpose }} @if($counter->currentToken->expected_amount) (PKR {{ number_format($counter->currentToken->expected_amount, 0) }}) @endif
          </div>
        @endif
        <div class="fs-4 text-success fw-bold tracking-widest text-uppercase" id="statusPrompt">
          <i class="bx bx-right-arrow-alt animate-arrow"></i> PLEASE PROCEED TO COUNTER / تشریف لائیں
        </div>
      @elseif($counter->status === 'break')
        <div class="py-5">
          <i class="bx bx-coffee fs-1 text-warning mb-3" style="font-size: 5rem !important;"></i>
          <div class="display-4 fw-bold text-danger">COUNTER TEMPORARILY PAUSED</div>
          <div class="fs-3 text-white-50 mt-2">Reason: {{ $counter->break_reason ?? 'Official Break' }}</div>
          <div class="fs-4 text-warning urdu-font mt-2">کاؤنٹر پر مختصر وقفہ ہے، براہِ کرم انتظار فرمائیں</div>
        </div>
      @else
        <div class="py-5">
          <i class="bx bx-check-circle fs-1 text-success mb-3" style="font-size: 5rem !important;"></i>
          <div class="display-4 fw-bold text-success">STATION READY</div>
          <div class="fs-3 text-white-50 mt-2">Awaiting Next Customer Ticket</div>
          <div class="fs-4 text-info urdu-font mt-2">کاؤنٹر تیار ہے، اگلے کسٹمر کا انتظار ہے</div>
        </div>
      @endif
    </div>
  </main>

  <!-- Pole Footer Ticker -->
  <footer class="pt-3 border-top border-secondary border-opacity-50 d-flex justify-content-between align-items-center">
    <div class="small text-white-50">
      <i class="bx bx-user me-1 text-primary"></i> Teller Desk Officer: <strong class="text-white" id="tellerNameDisplay">{{ $counter->currentUser->name ?? 'Staff In-charge' }}</strong>
    </div>
    <div class="d-flex align-items-center gap-3">
      <button class="btn btn-xs btn-outline-secondary" onclick="toggleFullScreen()">
        <i class="bx bx-fullscreen me-1"></i> Fullscreen
      </button>
      <div class="pulse-dot"></div>
      <small class="text-white-50">Real-time Pole Sync</small>
    </div>
  </footer>
</div>
@endsection

@push('styles')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@600;700&family=Noto+Nastaliq+Urdu:wght@700&display=swap');

  .urdu-font {
    font-family: 'Noto Nastaliq Urdu', serif;
  }

  .token-glow {
    font-family: 'Chakra Petch', monospace;
    text-shadow: 0 0 25px rgba(255, 171, 0, 0.8), 0 0 50px rgba(255, 171, 0, 0.4);
    animation: pulseToken 2s infinite alternate;
  }

  @keyframes pulseToken {
    from { transform: scale(1); filter: brightness(1); }
    to { transform: scale(1.02); filter: brightness(1.15); }
  }

  .pulse-dot {
    width: 10px;
    height: 10px;
    background-color: #28c76f;
    border-radius: 50%;
    box-shadow: 0 0 10px #28c76f;
    animation: pulseGlow 1.5s infinite;
  }

  @keyframes pulseGlow {
    0% { transform: scale(0.9); opacity: 0.7; }
    50% { transform: scale(1.3); opacity: 1; }
    100% { transform: scale(0.9); opacity: 0.7; }
  }

  .animate-arrow {
    display: inline-block;
    animation: arrowJump 0.8s infinite alternate;
  }

  @keyframes arrowJump {
    from { transform: translateX(0); }
    to { transform: translateX(12px); }
  }
</style>
@endpush

@push('scripts')
<script>
  const counterId = {{ $counter->id }};
  const apiUrl = "{{ url('display/pole-api') }}/" + counterId;

  function toggleFullScreen() {
    if (!document.fullscreenElement) {
      document.documentElement.requestFullscreen();
    } else {
      if (document.exitFullscreen) {
        document.exitFullscreen();
      }
    }
  }

  function fetchPoleStatus() {
    $.ajax({
      url: apiUrl,
      method: 'GET',
      success: function(res) {
        if (res.server_time) {
          document.getElementById('poleClock').innerText = res.server_time;
        }

        const container = document.getElementById('tokenContainer');
        const badge = document.getElementById('counterStatusBadge');
        const tellerEl = document.getElementById('tellerNameDisplay');

        if (tellerEl && res.teller_name) {
          tellerEl.innerText = res.teller_name;
        }

        if (badge) {
          badge.innerText = (res.status || 'ONLINE').toUpperCase();
        }

        if (res.status === 'break') {
          container.innerHTML = `
            <div class="py-5">
              <i class="bx bx-coffee fs-1 text-warning mb-3" style="font-size: 5rem !important;"></i>
              <div class="display-4 fw-bold text-danger">COUNTER TEMPORARILY PAUSED</div>
              <div class="fs-3 text-white-50 mt-2">Reason: ${res.break_reason || 'Official Break'}</div>
              <div class="fs-4 text-warning urdu-font mt-2">کاؤنٹر پر مختصر وقفہ ہے، براہِ کرم انتظار فرمائیں</div>
            </div>
          `;
        } else if (res.current_token) {
          container.innerHTML = `
            <div class="badge bg-label-info px-4 py-1 mb-2 fs-5">
              ${res.current_token.service || ''}
            </div>
            <div class="display-1 fw-bolder font-monospace token-glow my-2" style="font-size: 8rem; letter-spacing: 6px; color: #ffab00;">
              ${res.current_token.token_number}
            </div>
            <div class="fs-2 text-white fw-bold mb-1">
              ${res.current_token.customer_name || 'Customer'}
            </div>
            <div class="fs-4 text-success fw-bold tracking-widest text-uppercase">
              <i class="bx bx-right-arrow-alt animate-arrow"></i> PLEASE PROCEED TO COUNTER / تشریف لائیں
            </div>
          `;
        } else {
          container.innerHTML = `
            <div class="py-5">
              <i class="bx bx-check-circle fs-1 text-success mb-3" style="font-size: 5rem !important;"></i>
              <div class="display-4 fw-bold text-success">STATION READY</div>
              <div class="fs-3 text-white-50 mt-2">Awaiting Next Customer Ticket</div>
              <div class="fs-4 text-info urdu-font mt-2">کاؤنٹر تیار ہے، اگلے کسٹمر کا انتظار ہے</div>
            </div>
          `;
        }
      },
      error: function(err) {
        console.warn("Pole poll error:", err);
      }
    });
  }

  setInterval(fetchPoleStatus, 2500);
</script>
@endpush
