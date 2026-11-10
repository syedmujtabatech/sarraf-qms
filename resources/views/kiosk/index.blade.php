@extends('layouts.blank')

@section('title', 'Self-Service Touchscreen Kiosk | BankFlow-QMS')

@section('content')
<div class="kiosk-wrapper min-vh-100 d-flex flex-column bg-light" style="font-family: 'Public Sans', sans-serif;">
  <!-- Kiosk Top Header -->
  <header class="bg-primary text-white py-3 px-4 shadow-sm">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center">
        <div class="p-2 bg-white text-primary rounded-3 me-3 shadow-xs">
          <i class="bx bxs-bank fs-2"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bolder text-white">Sarraf Self-Service Kiosk</h3>
          <small class="text-white-50">{{ $currentBranch->name }} &bull; صراف &bull; Branch Code: {{ $currentBranch->code }}</small>
        </div>
      </div>

      <div class="d-flex align-items-center gap-3">
        <!-- Branch Switcher for Demo -->
        <form method="GET" action="{{ route('kiosk.index') }}" class="d-none d-md-block">
          <select class="form-select form-select-sm bg-white text-primary fw-semibold" name="branch_id" onchange="this.form.submit()">
            @foreach($branches as $b)
              <option value="{{ $b->id }}" {{ $b->id == $selectedBranchId ? 'selected' : '' }}>
                {{ $b->code }} - {{ $b->name }}
              </option>
            @endforeach
          </select>
        </form>

        <!-- Bilingual Language Pill -->
        <button type="button" class="btn btn-warning fw-bold px-3 py-2 shadow-sm rounded-pill" id="langToggleBtn" onclick="toggleLanguage()">
          <i class="bx bx-globe me-1"></i> <span id="langLabel">اردو میں دیکھیں</span>
        </button>

        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light btn-sm rounded-pill">
          <i class="bx bx-home-alt"></i> Admin
        </a>
      </div>
    </div>
  </header>

  <!-- SBP Mandatory Regulatory Priority Banner -->
  <div class="bg-danger text-white py-2 px-4 shadow-xs">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center">
        <i class="bx bx-accessibility fs-4 me-2"></i>
        <span class="small fw-bold">
          SBP Policy & Circular 01/2018: Special Persons, Senior Citizens (60+) & Pensioners are provided Fast-Track Priority Service.
        </span>
      </div>
      
    </div>
  </div>

  <!-- Main Touch Screen Body -->
  <main class="flex-grow-1 container py-4 d-flex flex-column justify-content-center">
    @php
      $currentHost = request()->getHttpHost();
      $isLocal = in_array(request()->getHost(), ['127.0.0.1', 'localhost']);
      if ($isLocal && !empty(config('app.url')) && !str_contains(config('app.url'), 'localhost') && !str_contains(config('app.url'), '127.0.0.1')) {
          $baseUrl = rtrim(config('app.url'), '/');
      } else {
          $scheme = (request()->isSecure() || request()->header('x-forwarded-proto') === 'https') ? 'https' : request()->getScheme();
          $baseUrl = $scheme . '://' . $currentHost;
      }
      $mobileJoinUrl = $baseUrl . '/join';
    @endphp

    <!-- Entrance Mobile QR Quick Pass Card -->
    <div class="card bg-white border border-primary border-opacity-25 shadow-sm rounded-4 mb-4 p-3 text-center">
      <div class="row align-items-center">
        <div class="col-md-2 col-4 mx-auto">
          <img src="https://api.qrserver.com/v1/create-qr-code/?size=110x110&data={{ urlencode($mobileJoinUrl) }}" 
               alt="QR Code" width="95" height="95" class="border p-1 rounded shadow-xs" />
        </div>
        <div class="col-md-7 col-12 text-md-start text-center mt-2 mt-md-0">
          <span class="badge bg-label-primary px-3 py-1 mb-1 fw-bold">MOBILE TOUCHLESS E-PASS</span>
          <h5 class="fw-bold mb-1 text-dark">Scan with Camera to Get Mobile Pass</h5>
          <p class="text-muted small mb-0">
            Scan this QR code with your mobile camera to choose service, enter your name, and receive your digital queue pass with live status updates.
          </p>
        </div>
        <div class="col-md-3 col-12 text-md-end text-center mt-2 mt-md-0">
          <a href="{{ route('mobile.join') }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-xs">
            <i class="bx bx-mobile me-1"></i> Open Mobile Pass
          </a>
        </div>
      </div>
    </div>

    <div class="text-center mb-4">
      <h2 class="fw-bold text-dark mb-1" id="screenHeading">Please Select Your Banking Service</h2>
      <p class="text-muted small mb-0">Select your banking service to proceed</p>
    </div>

    <!-- Service Categories Grid -->
    <div class="row g-3 justify-content-center">
      @foreach($services as $srv)
        <div class="col-lg-4 col-md-6">
          <div class="card h-100 border-2 border-transparent shadow-sm touch-card p-3 rounded-4 cursor-pointer"
               onclick="selectService({{ $srv->id }}, '{{ addslashes($srv->name) }}', '{{ addslashes($srv->name_ur) }}', '{{ $srv->prefix }}')"
               style="transition: all 0.2s ease-in-out; cursor: pointer; border-left: 6px solid var(--bs-{{ $srv->color }}) !important;">
            <div class="card-body p-2 d-flex align-items-center">
              <div class="avatar avatar-xl me-3 rounded-4 bg-label-{{ $srv->color }} d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                <i class="bx {{ $srv->icon }} fs-1 text-{{ $srv->color }}"></i>
              </div>
              <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center">
                  <span class="badge bg-{{ $srv->color }} font-monospace mb-1">{{ $srv->prefix }}</span>
                  <small class="text-muted"><i class="bx bx-time me-1"></i>~{{ $srv->avg_service_time_mins }}m</small>
                </div>
                <h5 class="fw-bold mb-1 text-dark">{{ $srv->name }}</h5>
                
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </main>

  <!-- Kiosk Footer -->
  <footer class="bg-white border-top py-3 text-center text-muted small">
    <div class="container d-flex justify-content-between align-items-center flex-wrap">
      <div>
        <i class="bx bx-printer me-1 text-primary"></i> Automatic Thermal Ticket Dispenser &bull; Touch any button to proceed
      </div>
      <div>
        Need Help? Please approach the Customer Service Greeter Desk or call 111-727-111
      </div>
    </div>
  </footer>
</div>

<!-- Modal: Priority Selection & Customer Details (SBP / SBI Compliant) -->
<div class="modal fade" id="kioskTokenModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-4 shadow-lg border-0">
      <form action="{{ route('kiosk.generate') }}" method="POST" id="kioskForm">
        @csrf
        <input type="hidden" name="branch_id" value="{{ $selectedBranchId }}">
        <input type="hidden" name="service_category_id" id="selectedServiceId">

        <div class="modal-header bg-primary text-white py-3 rounded-top-4">
          <div>
            <h5 class="modal-title fw-bold text-white mb-0" id="modalServiceTitle">Service Selected</h5>
            
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-4">
          <!-- Step 1: Priority Category Selection (SBP Mandate) -->
          <div class="mb-4">
            <h6 class="fw-bold text-dark mb-1">
              <i class="bx bx-check-shield text-danger me-1"></i> Select Customer Priority Category (SBP Guidelines)
            </h6>
            

            <div class="row g-2">
              @foreach($priorities as $pri)
                <div class="col-md-6">
                  <label class="priority-label w-100 cursor-pointer">
                    <input type="radio" name="priority_category_id" value="{{ $pri->id }}" class="btn-check" {{ $pri->code === 'REGULAR' ? 'checked' : '' }}>
                    <div class="btn btn-outline-{{ $pri->badge_color }} w-100 p-3 text-start rounded-3 d-flex align-items-center justify-content-between">
                      <div class="d-flex align-items-center">
                        <i class="bx {{ $pri->icon }} fs-3 me-2"></i>
                        <div>
                          <div class="fw-bold">{{ $pri->name }}</div>
                          
                        </div>
                      </div>
                      @if($pri->weight_multiplier > 1)
                        <span class="badge bg-{{ $pri->badge_color }} text-white">Priority</span>
                      @endif
                    </div>
                  </label>
                </div>
              @endforeach
            </div>
          </div>

          <!-- Step 2: Customer Details & Salutation (For Audio Calling & Slip) -->
          <div class="p-3 bg-light rounded-3 border">
            <h6 class="fw-bold mb-1 text-dark">
              <i class="bx bx-user-pin text-primary me-1"></i> Customer Identification & Title (صارف کی تفصیلات)
            </h6>
            

            <!-- Salutation Touch Pills -->
            <div class="mb-3">
              <label class="form-label small fw-semibold text-muted d-block">Salutation / القاب:</label>
              <div class="d-flex flex-wrap gap-1">
                @php
                  $salutes = [
                    'Mr.' => 'جناب (Mr.)',
                    'Mrs.' => 'محترمہ (Mrs.)',
                    'Ms.' => 'محترمہ (Ms.)',
                    'Dr.' => 'ڈاکٹر (Dr.)',
                    'Haji' => 'حاجی صاحب',
                    'Syed' => 'سید صاحب',
                  ];
                @endphp
                @foreach($salutes as $k => $l)
                  <label class="cursor-pointer">
                    <input type="radio" name="salutation" value="{{ $k }}" class="btn-check" {{ $loop->first ? 'checked' : '' }}>
                    <span class="btn btn-sm btn-outline-secondary rounded-pill px-3">{{ $l }}</span>
                  </label>
                @endforeach
              </div>
            </div>

            <div class="row g-2 mb-2">
              <div class="col-md-6">
                <label class="form-label small fw-bold">Customer Full Name (پورا نام)</label>
                <input type="text" name="customer_name" class="form-control" placeholder="e.g. Tariq Mehmood (یا خالی چھوڑیں)">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-bold">Mobile Number (موبائل نمبر)</label>
                <input type="text" name="customer_phone" class="form-control" placeholder="e.g. 0300-1234567">
              </div>
            </div>

            <div class="row g-2">
              <div class="col-md-4">
                <label class="form-label small text-muted">CNIC Number</label>
                <input type="text" name="customer_cnic" class="form-control form-control-sm" placeholder="42101-1234567-1">
              </div>
              <div class="col-md-4">
                <label class="form-label small text-muted">Account Number</label>
                <input type="text" name="account_number" class="form-control form-control-sm" placeholder="Optional Account #">
              </div>
              <div class="col-md-4">
                <label class="form-label small text-muted">Expected Cash (PKR)</label>
                <input type="number" step="100" name="expected_amount" class="form-control form-control-sm" placeholder="e.g. 50000">
              </div>
            </div>

            <div class="alert alert-info py-2 small mt-3 mb-0">
              <i class="bx bx-volume-full me-1"></i> <strong>Audio Voice Calling:</strong> جب کاؤنٹر سے آپ کو بلایا جائے گا تو آپ کا نام آواز میں بولا جائے گا (مثلاً: "ٹوکن نمبر C-104، جناب طارق محمود، کاؤنٹر نمبر 1 پر تشریف لائیں")۔
            </div>
          </div>
        </div>

        <div class="modal-footer bg-light py-3 border-top rounded-bottom-4 d-flex justify-content-between">
          <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal">
            <i class="bx bx-left-arrow-alt me-1"></i> Back
          </button>
          <button type="submit" class="btn btn-success btn-lg px-5 py-2 fw-bold shadow">
            <i class="bx bx-printer me-2 fs-5"></i> DISPENSE TOKEN SLIP / ٹوکن حاصل کریں
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .touch-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
  }
  .priority-label input[type="radio"]:checked + div {
    border-width: 2px !important;
    box-shadow: 0 0 0 3px rgba(105, 108, 255, 0.25);
  }
</style>
@endpush

@push('scripts')
<script>
  let isUrdu = false;

  function toggleLanguage() {
    isUrdu = !isUrdu;
    const langLabel = document.getElementById('langLabel');
    const screenHeading = document.getElementById('screenHeading');
    const screenHeadingUrdu = document.getElementById('screenHeadingUrdu');

    if (isUrdu) {
      langLabel.innerText = "Switch to English";
      screenHeading.innerText = "براہ کرم مطلوبہ بینکاری سروس کا انتخاب فرمائیں";
      screenHeadingUrdu.innerText = "Please Select Your Banking Service";
      screenHeading.classList.add('urdu-font');
    } else {
      langLabel.innerText = "اردو میں دیکھیں";
      screenHeading.innerText = "Please Select Your Banking Service";
      screenHeadingUrdu.innerText = "براہ کرم مطلوبہ بینکاری سروس کا انتخاب فرمائیں";
      screenHeading.classList.remove('urdu-font');
    }
  }

  function selectService(serviceId, nameEn, nameUr, prefix) {
    document.getElementById('selectedServiceId').value = serviceId;
    document.getElementById('modalServiceTitle').innerText = prefix + ' - ' + nameEn;
    document.getElementById('modalServiceTitleUrdu').innerText = nameUr;

    // Play a friendly kiosk touch click sound
    try {
      const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
      const osc = audioCtx.createOscillator();
      const gain = audioCtx.createGain();
      osc.connect(gain);
      gain.connect(audioCtx.destination);
      osc.frequency.value = 650;
      gain.gain.setValueAtTime(0.1, audioCtx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.15);
      osc.start();
      osc.stop(audioCtx.currentTime + 0.15);
    } catch (e) {}

    const modal = new bootstrap.Modal(document.getElementById('kioskTokenModal'));
    modal.show();
  }
</script>
@endpush
