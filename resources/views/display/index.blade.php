@extends('layouts.blank')

@section('title', 'Lobby TV Queue Display | BankFlow-QMS')

@section('body-class', 'bg-dark text-white overflow-hidden')

@section('content')
<div class="display-screen-container vh-100 d-flex flex-column" style="background-color: #0b132b; color: #fff;">
  <!-- Top Electronic Header -->
  <header class="py-2 px-4 d-flex justify-content-between align-items-center shadow-lg" style="background-color: #1c2541; border-bottom: 3px solid #696cff;">
    <div class="d-flex align-items-center">
      <div class="p-2 bg-primary text-white rounded-3 me-3">
        <i class="bx bxs-bank fs-2"></i>
      </div>
      <div>
        <h2 class="mb-0 fw-bolder text-white tracking-wide">STATE BANK ELECTRONIC QUEUE DISPLAY</h2>
        <div class="text-white-50 small">
          {{ $currentBranch->name }} &bull; Branch Code: <strong>{{ $currentBranch->code }}</strong>
        </div>
      </div>
    </div>

    <!-- Live Digital Clock & Audio Toggle -->
    <div class="d-flex align-items-center gap-3">
      <!-- Audio Announcement Status Badge -->
      <button type="button" id="audioToggleBtn" class="btn btn-sm btn-outline-warning d-flex align-items-center" onclick="enableAudioAndTest()">
        <i class="bx bx-volume-full me-1 fs-5"></i>
        <span id="audioStatusText">Voice Announcements: ACTIVE (Click to Test)</span>
      </button>

      <!-- Digital Clock -->
      <div class="text-end px-3 py-1 rounded bg-black bg-opacity-25 border border-secondary">
        <div class="fs-4 fw-bolder font-monospace text-warning" id="liveClock">{{ now()->format('h:i:s A') }}</div>
        <small class="text-white-50" style="font-size: 11px;">{{ now()->format('l, d M Y') }}</small>
      </div>

      <!-- Branch switch -->
      <form method="GET" action="{{ route('display.index') }}" class="d-none d-md-block">
        <select class="form-select form-select-sm bg-dark text-white border-secondary" name="branch_id" onchange="this.form.submit()">
          @foreach($branches as $b)
            <option value="{{ $b->id }}" {{ $b->id == $selectedBranchId ? 'selected' : '' }}>
              {{ $b->code }} - {{ $b->name }}
            </option>
          @endforeach
        </select>
      </form>
    </div>
  </header>

  <!-- Main Split TV Screen Content -->
  <main class="flex-grow-1 p-3 d-flex gap-3 overflow-hidden">
    <!-- Left Column (65%): Active Serving & Called Tokens -->
    <div class="flex-grow-1 d-flex flex-column" style="flex: 2;">
      <!-- Active Counter Grid Title -->
      <div class="d-flex justify-content-between align-items-center px-2 py-1 mb-2">
        <h4 class="mb-0 fw-bold text-uppercase text-info tracking-wider">
          <i class="bx bx-broadcast me-2 pulse-dot"></i> Now Serving / اب بلائے جا رہے ہیں
        </h4>
        <span class="badge bg-primary px-3 py-1 fs-6">Counter Displays</span>
      </div>

      <!-- High Impact Large Display Grid -->
      <div class="row g-2 flex-grow-1" id="activeCountersGrid">
        @forelse($activeCounters as $cnt)
          <div class="col-md-6 d-flex flex-column">
            <div class="p-3 rounded-4 h-100 d-flex flex-column justify-content-between border-3 shadow-lg {{ $cnt->currentToken ? ($cnt->currentToken->status === 'called' ? 'called-flash border-warning bg-warning bg-opacity-10' : 'border-primary bg-primary bg-opacity-10') : 'border-secondary bg-black bg-opacity-20' }}"
                 style="border: 2px solid {{ $cnt->currentToken ? '#696cff' : '#434968' }};">
              <!-- Counter Number Header -->
              <div class="d-flex justify-content-between align-items-center border-bottom border-secondary pb-2 mb-2">
                <span class="fs-4 fw-bolder text-white">
                  COUNTER <span class="badge bg-primary fs-4 px-3 ms-1">{{ $cnt->counter_number }}</span>
                </span>
                @if($cnt->type === 'priority_dedicated')
                  <span class="badge bg-danger fs-tiny px-2 py-1">
                    <i class="bx bx-accessibility me-1"></i> SBP Priority Desk
                  </span>
                @else
                  <span class="badge bg-label-info fs-tiny">{{ $cnt->name }}</span>
                @endif
              </div>

              <!-- Big Token Calling Block -->
              <div class="text-center py-2 flex-grow-1 d-flex flex-column justify-content-center">
                @if($cnt->currentToken)
                  <span class="badge bg-label-{{ $cnt->currentToken->serviceCategory->color ?? 'primary' }} mb-1 text-uppercase align-self-center px-3" style="font-size: 12px;">
                    {{ $cnt->currentToken->serviceCategory->name ?? '' }}
                  </span>
                  <div class="display-2 fw-bolder text-warning font-monospace tracking-wide mb-0 text-shadow">
                    {{ $cnt->currentToken->token_number }}
                  </div>
                  <div class="fs-4 text-white fw-bold mt-1 text-truncate">
                    <span class="text-warning">{{ $cnt->currentToken->salutation ?? 'Mr.' }}</span> {{ $cnt->currentToken->customer_name }}
                  </div>
                  @if($cnt->currentToken->transaction_purpose)
                    <span class="badge bg-label-info text-truncate align-self-center my-1" style="font-size: 11px;">
                      {{ $cnt->currentToken->transaction_purpose }} @if($cnt->currentToken->expected_amount) (PKR {{ number_format($cnt->currentToken->expected_amount, 0) }}) @endif
                    </span>
                  @endif
                  @if($cnt->currentToken->priorityCategory->code !== 'REGULAR')
                    <span class="badge bg-{{ $cnt->currentToken->priorityCategory->badge_color ?? 'primary' }} align-self-center mt-1">
                      <i class="bx {{ $cnt->currentToken->priorityCategory->icon ?? 'bx-user' }} me-1"></i>
                      {{ $cnt->currentToken->priorityCategory->name }}
                    </span>
                  @endif
                @else
                  <div class="text-white-50 py-4">
                    <i class="bx bx-user-check fs-1 text-secondary mb-1"></i>
                    <div class="fs-5 fw-bold">COUNTER READY</div>
                    <small>Available for Next Customer</small>
                  </div>
                @endif
              </div>

              <!-- Footer with Arrow Indicator -->
              <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary text-white-50 small">
                <span>{{ $cnt->services->pluck('prefix')->implode(', ') }} Services</span>
                <span class="text-warning fw-bold">
                  @if($cnt->currentToken)
                    <i class="bx bx-right-arrow-alt fs-4 animate-arrow"></i> Proceed to Counter
                  @else
                    Idle
                  @endif
                </span>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12 text-center py-5 text-white-50">
            No counters configured.
          </div>
        @endforelse
      </div>
    </div>

    <!-- Right Column (35%): SBP Info, Currency/Notice Ticker, Waiting Queue -->
    <div class="d-flex flex-column gap-3" style="flex: 1.1; max-width: 480px;">
      <!-- SBP Public Awareness & Exchange Rates Board -->
      <div class="card bg-black bg-opacity-25 border border-secondary shadow-lg text-white">
        <div class="card-header py-2 px-3 border-bottom border-secondary d-flex justify-content-between align-items-center">
          <span class="small fw-bold text-warning text-uppercase">
            <i class="bx bx-shield-quarter me-1"></i> SBP Customer Protection Notice
          </span>
          <span class="badge bg-label-info" style="font-size: 9px;">SBP BSC</span>
        </div>
        <div class="card-body p-3 small text-white-50">
          <p class="mb-2 text-white">
            <i class="bx bx-check-circle text-success me-1"></i> <strong>Special Persons & Pensioners:</strong> Please utilize Counter #01 for fast-track service without standing in general queue.
          </p>
          <div class="urdu-font text-warning text-end small" style="line-height: 1.4;">
            معذور افراد، پنشنرز اور 60 سال سے زائد بزرگ شہریوں کے لیے کاؤنٹر نمبر 1 پر ترجیحی سہولت دستیاب ہے۔
          </div>
        </div>
      </div>

      <!-- Up Next in Queue Table -->
      <div class="card bg-black bg-opacity-25 border border-secondary shadow-lg flex-grow-1 d-flex flex-column text-white">
        <div class="card-header py-2 px-3 border-bottom border-secondary d-flex justify-content-between align-items-center">
          <span class="small fw-bold text-uppercase text-info">
            <i class="bx bx-time-five me-1"></i> Up Next in Queue / اگلا ٹوکن
          </span>
          <span class="badge bg-warning text-dark font-monospace" id="waitingCountBadge">{{ $waitingTokens->count() }} Waiting</span>
        </div>
        <div class="card-body p-2 flex-grow-1 overflow-auto" id="waitingTokensList">
          @forelse($waitingTokens as $idx => $wait)
            <div class="p-2 mb-2 rounded bg-white bg-opacity-10 border border-secondary d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center">
                <span class="badge bg-light text-dark font-monospace me-2">#{{ $idx + 1 }}</span>
                <div>
                  <strong class="fs-5 text-warning font-monospace">{{ $wait->token_number }}</strong>
                  <div class="small text-white-50 text-truncate" style="max-width: 140px;">
                    <span class="text-warning">{{ $wait->salutation ?? 'Mr.' }}</span> {{ $wait->customer_name }}
                  </div>
                </div>
              </div>
              <div class="text-end">
                <span class="badge bg-{{ $wait->priorityCategory->badge_color ?? 'primary' }}" style="font-size: 10px;">
                  {{ $wait->priorityCategory->name }}
                </span>
                <small class="d-block text-white-50" style="font-size: 10px;">{{ $wait->serviceCategory->name ?? '' }}</small>
              </div>
            </div>
          @empty
            <div class="text-center py-4 text-white-50 small">
              Waiting area is clear.
            </div>
          @endforelse
        </div>
      </div>
    </div>
  </main>

  <!-- Bottom News & Policy Ticker -->
  <footer class="py-2 px-3 bg-black border-top border-secondary text-white d-flex align-items-center overflow-hidden">
    <span class="badge bg-danger text-uppercase px-3 py-1 me-3 fw-bold flex-shrink-0">
      <i class="bx bx-bell me-1"></i> BANK NOTICE
    </span>
    <marquee class="small text-white-50" scrollamount="6">
      Welcome to State Bank Banking Services &bull; All transactions are verified as per SBP guidelines &bull; Senior Citizens (60+), Pensioners and Special Persons are provided immediate priority assistance &bull; Keep your token slip until your number is announced &bull; For feedback, scan the QR code on your slip &bull; Digital Banking App is available 24/7.
    </marquee>
  </footer>
</div>
@endsection

@push('styles')
<style>
  .called-flash {
    animation: borderBlink 1.5s infinite;
  }
  @keyframes borderBlink {
    0% { border-color: #ffab00; box-shadow: 0 0 20px rgba(255, 171, 0, 0.4); }
    50% { border-color: #696cff; box-shadow: 0 0 10px rgba(105, 108, 255, 0.2); }
    100% { border-color: #ffab00; box-shadow: 0 0 20px rgba(255, 171, 0, 0.4); }
  }
  .animate-arrow {
    display: inline-block;
    animation: arrowBounce 1s infinite alternate;
  }
  @keyframes arrowBounce {
    from { transform: translateX(0); }
    to { transform: translateX(6px); }
  }
  .text-shadow {
    text-shadow: 0 2px 10px rgba(0,0,0,0.7);
  }
</style>
@endpush

@push('scripts')
<script>
  const branchId = {{ $selectedBranchId }};
  let audioContext = null;

  // Initialize Web Audio Chime Sound (Two-tone Ding Dong Airport / Bank chime)
  function playBankChime(callback) {
    try {
      const AudioContext = window.AudioContext || window.webkitAudioContext;
      audioContext = audioContext || new AudioContext();

      if (audioContext.state === 'suspended') {
        audioContext.resume();
      }

      const now = audioContext.currentTime;

      // Note 1: 587.33 Hz (D5)
      const osc1 = audioContext.createOscillator();
      const gain1 = audioContext.createGain();
      osc1.type = 'sine';
      osc1.frequency.setValueAtTime(587.33, now);
      gain1.gain.setValueAtTime(0.3, now);
      gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.6);
      osc1.connect(gain1);
      gain1.connect(audioContext.destination);
      osc1.start(now);
      osc1.stop(now + 0.6);

      // Note 2: 440 Hz (A4)
      const osc2 = audioContext.createOscillator();
      const gain2 = audioContext.createGain();
      osc2.type = 'sine';
      osc2.frequency.setValueAtTime(440, now + 0.35);
      gain2.gain.setValueAtTime(0.35, now + 0.35);
      gain2.gain.exponentialRampToValueAtTime(0.001, now + 1.2);
      osc2.connect(gain2);
      gain2.connect(audioContext.destination);
      osc2.start(now + 0.35);
      osc2.stop(now + 1.2);

      setTimeout(() => {
        if (callback) callback();
      }, 1250);
    } catch (e) {
      console.warn("Audio chime error:", e);
      if (callback) callback();
    }
  }

  // Web Speech API Bilingual Voice Synthesizer (English & Urdu)
  function speakAnnouncement(textEn, textUr) {
    if (!('speechSynthesis' in window)) {
      console.warn("Speech synthesis not supported in this browser.");
      return;
    }

    playBankChime(() => {
      // 1. Speak English announcement
      const utteranceEn = new SpeechSynthesisUtterance(textEn);
      utteranceEn.rate = 0.9;
      utteranceEn.pitch = 1.0;
      utteranceEn.lang = 'en-US';

      utteranceEn.onend = () => {
        // 2. Speak Urdu announcement immediately following
        setTimeout(() => {
          const utteranceUr = new SpeechSynthesisUtterance(textUr);
          utteranceUr.rate = 0.85;
          utteranceUr.pitch = 1.0;
          utteranceUr.lang = 'ur-PK'; // Urdu locale
          window.speechSynthesis.speak(utteranceUr);
        }, 300);
      };

      window.speechSynthesis.speak(utteranceEn);
    });
  }

  // User interaction test trigger
  function enableAudioAndTest() {
    speakAnnouncement("Token Number C 102, please proceed to Counter 2", "ٹوکن نمبر سی ایک سو دو، کاؤنٹر نمبر دو پر تشریف لائیں");
    document.getElementById('audioStatusText').innerText = "Voice Announcements: READY & ONLINE";
  }

  // Real-time Polling Engine (Every 3.5 seconds)
  function pollQueueUpdates() {
    $.ajax({
      url: "{{ url('display/poll') }}/" + branchId,
      method: "GET",
      success: function(data) {
        if (data.server_time) {
          document.getElementById('liveClock').innerText = data.server_time;
        }

        // Play pending voice announcements (SBP BSC Tender Spec)
        if (data.announcements && data.announcements.length > 0) {
          data.announcements.forEach((ann, idx) => {
            setTimeout(() => {
              speakAnnouncement(ann.voice_text_en, ann.voice_text_ur);
            }, idx * 4500);
          });
        }
      },
      error: function(err) {
        console.error("Polling error:", err);
      }
    });
  }

  setInterval(pollQueueUpdates, 3500);
</script>
@endpush
