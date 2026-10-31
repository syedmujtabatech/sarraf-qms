@extends('layouts.blank')

@section('title', 'Get Mobile Queue Pass | Sarraf-QMS')

@section('body-class', 'bg-light')

@section('content')
<div class="mobile-join-container min-vh-100 d-flex flex-column" style="max-width: 540px; margin: 0 auto; background: #ffffff;">
  <!-- Header -->
  <header class="bg-primary text-white p-3 shadow-sm sticky-top">
    <div class="d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center">
        <div class="p-2 bg-white text-primary rounded-3 me-2 shadow-xs">
          <i class="bx bxs-bank fs-4"></i>
        </div>
        <div>
          <h5 class="mb-0 fw-bold text-white">صراف کیو ایم ایس &bull; Sarraf-QMS</h5>
          <small class="text-white-50" style="font-size: 11px;">{{ $currentBranch->name }} &bull; Branch Code: {{ $currentBranch->code }}</small>
        </div>
      </div>
      <span class="badge bg-warning text-dark fw-bold">E-Pass</span>
    </div>
  </header>

  <!-- Welcome Notice -->
  <div class="bg-label-primary p-3 border-bottom text-center">
    <h5 class="fw-bold mb-1 text-primary">Digital Queue Token Pass</h5>
    <p class="urdu-font fs-6 text-dark mb-0">موبائل قطار ٹوکن حاصل کریں — کاؤنٹر پر آپ کا نام پکارا جائے گا</p>
  </div>

  <main class="flex-grow-1 p-3">
    <form action="{{ route('mobile.join.post') }}" method="POST" id="joinForm">
      @csrf
      <input type="hidden" name="branch_id" value="{{ $selectedBranchId }}">

      <!-- Step 1: Select Service -->
      <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <label class="form-label fw-bold mb-0 text-dark">
            <span class="badge bg-primary rounded-circle me-1">1</span> Select Banking Service Required:
          </label>
          <small class="urdu-font text-primary">مطلوبہ سروس منتخب کریں</small>
        </div>

        <div class="row g-2">
          @foreach($services as $idx => $s)
            <div class="col-6">
              <label class="w-100 cursor-pointer">
                <input type="radio" name="service_category_id" value="{{ $s->id }}" class="btn-check" {{ $idx == 0 ? 'checked' : '' }} required>
                <div class="btn btn-outline-primary text-start w-100 p-2 h-100 rounded-3 d-flex flex-column justify-content-between service-card" style="border-width: 2px;">
                  <div class="d-flex align-items-center mb-1">
                    <span class="badge bg-{{ $s->color }} me-1 font-monospace">{{ $s->prefix }}</span>
                    <i class="bx {{ $s->icon }} text-{{ $s->color }} fs-5 ms-auto"></i>
                  </div>
                  <strong class="d-block text-dark small" style="line-height: 1.2;">{{ $s->name }}</strong>
                  <span class="urdu-font text-muted" style="font-size: 11px; line-height: 1.2;">{{ $s->name_ur }}</span>
                </div>
              </label>
            </div>
          @endforeach
        </div>
      </div>

      <!-- Step 2: Customer Personal Details -->
      <div class="mb-4 p-3 bg-light rounded-3 border">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <label class="form-label fw-bold mb-0 text-dark">
            <span class="badge bg-primary rounded-circle me-1">2</span> Customer Details & Salutation:
          </label>
          <small class="urdu-font text-primary">صارف کی تفصیلات</small>
        </div>

        <!-- Salutation Title Pills -->
        <div class="mb-3">
          <label class="form-label small fw-semibold text-muted d-block">Salutation / القاب:</label>
          <div class="d-flex flex-wrap gap-1">
            @php
              $salutations = [
                'Mr.' => 'جناب (Mr.)',
                'Mrs.' => 'محترمہ (Mrs.)',
                'Ms.' => 'محترمہ (Ms.)',
                'Dr.' => 'ڈاکٹر (Dr.)',
                'Haji' => 'حاجی صاحب',
                'Syed' => 'سید صاحب',
              ];
            @endphp
            @foreach($salutations as $key => $label)
              <label class="cursor-pointer">
                <input type="radio" name="salutation" value="{{ $key }}" class="btn-check" {{ $loop->first ? 'checked' : '' }}>
                <span class="btn btn-xs btn-outline-secondary rounded-pill px-3">{{ $label }}</span>
              </label>
            @endforeach
          </div>
        </div>

        <!-- Full Name -->
        <div class="mb-3">
          <label class="form-label small fw-bold">Full Name (پورا نام) <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text bg-white"><i class="bx bx-user text-primary"></i></span>
            <input type="text" name="customer_name" class="form-control" placeholder="e.g. Tariq Mehmood" required>
          </div>
          <small class="text-muted" style="font-size: 10px;">
            <i class="bx bx-volume-full me-1 text-primary"></i> This name will be announced aloud on speakers when your turn arrives.
          </small>
        </div>

        <!-- Phone Number -->
        <div class="mb-3">
          <label class="form-label small fw-bold">Mobile Phone (موبائل نمبر) <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text bg-white"><i class="bx bx-phone text-success"></i></span>
            <input type="text" name="customer_phone" class="form-control" placeholder="0300-1234567" required>
          </div>
        </div>

        <!-- Optional CNIC & Account -->
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small text-muted">CNIC (شناختی کارڈ نمبر)</label>
            <input type="text" name="customer_cnic" class="form-control form-control-sm" placeholder="42101-1234567-1">
          </div>
          <div class="col-6">
            <label class="form-label small text-muted">Bank Account # (اختیاری)</label>
            <input type="text" name="account_number" class="form-control form-control-sm" placeholder="PK00BANK0012345">
          </div>
        </div>

        <!-- Transaction Purpose & Amount -->
        <div class="row g-2">
          <div class="col-6">
            <label class="form-label small text-muted">Purpose / مقصد</label>
            <select name="transaction_purpose" class="form-select form-select-sm">
              <option value="Cash Deposit">Cash Deposit</option>
              <option value="Cash Withdrawal">Cash Withdrawal</option>
              <option value="Cheque Clearance">Cheque Clearance</option>
              <option value="Account Opening">Account Opening</option>
              <option value="Biometric Verification">Biometric KYC</option>
              <option value="Foreign Exchange">Foreign Remittance</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small text-muted">Amount (PKR رقم)</label>
            <input type="number" step="100" name="expected_amount" class="form-control form-control-sm" placeholder="e.g. 50,000">
          </div>
        </div>
      </div>

      <!-- Step 3: Priority Category (State Bank of Pakistan Guidelines) -->
      <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <label class="form-label fw-bold mb-0 text-dark">
            <span class="badge bg-primary rounded-circle me-1">3</span> Priority Category (SBP Guidelines):
          </label>
          <small class="urdu-font text-danger">ترجیحی کیٹگری</small>
        </div>

        <div class="row g-2">
          @foreach($priorities as $pri)
            <div class="col-12">
              <label class="w-100 cursor-pointer">
                <input type="radio" name="priority_category_id" value="{{ $pri->id }}" class="btn-check" {{ $pri->code === 'REGULAR' ? 'checked' : '' }}>
                <div class="btn btn-outline-{{ $pri->badge_color }} text-start w-100 p-2 rounded-3 d-flex justify-content-between align-items-center">
                  <div class="d-flex align-items-center">
                    <i class="bx {{ $pri->icon }} fs-4 me-2"></i>
                    <div>
                      <strong class="d-block small">{{ $pri->name }}</strong>
                      <span class="urdu-font" style="font-size: 11px;">{{ $pri->name_ur }}</span>
                    </div>
                  </div>
                  @if($pri->weight_multiplier > 1)
                    <span class="badge bg-{{ $pri->badge_color }}">Fast-Track</span>
                  @endif
                </div>
              </label>
            </div>
          @endforeach
        </div>
      </div>

      <!-- Submit Button -->
      <button type="submit" class="btn btn-success btn-lg w-100 py-3 fw-bold shadow-lg rounded-pill mb-3 fs-5">
        <i class="bx bx-check-circle me-1"></i> GENERATE QUEUE PASS / ٹوکن لیں
      </button>

      <div class="text-center small text-muted">
        <i class="bx bx-shield-quarter text-success me-1"></i> SBP Verified Banking Infrastructure &bull; Real-time Audio Dispatch
      </div>
    </form>
  </main>
</div>
@endsection

@push('styles')
<style>
  .btn-check:checked + .service-card {
    background-color: rgba(105, 108, 255, 0.08) !important;
    border-color: #696cff !important;
    box-shadow: 0 0 0 3px rgba(105, 108, 255, 0.25) !important;
  }
  .btn-check:checked + .btn-outline-secondary {
    background-color: #696cff !important;
    color: #fff !important;
    border-color: #696cff !important;
  }
</style>
@endpush
