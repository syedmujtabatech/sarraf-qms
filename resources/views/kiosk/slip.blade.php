@extends('layouts.blank')

@section('title', 'Token Slip: ' . $token->token_number)

@section('content')
<div class="min-vh-100 d-flex flex-column align-items-center justify-content-center bg-dark py-4">
  <!-- Actions bar outside print -->
  <div class="mb-3 d-print-none text-center">
    <button onclick="window.print()" class="btn btn-success btn-lg px-4 me-2 shadow">
      <i class="bx bx-printer me-1"></i> Print Ticket Slip
    </button>
    <a href="{{ route('kiosk.index', ['branch_id' => $token->branch_id]) }}" class="btn btn-outline-light btn-lg px-4">
      <i class="bx bx-home-alt me-1"></i> Next Customer / Kiosk Home
    </a>
  </div>

  <!-- Thermal Paper Receipt Container -->
  <div class="ticket-slip bg-white text-dark p-4 rounded-3 shadow-lg" style="width: 380px; max-width: 95vw; font-family: 'Public Sans', monospace;">
    <!-- Bank Header -->
    <div class="text-center border-bottom border-2 border-dark pb-3 mb-3">
      <div class="d-inline-flex p-2 rounded-circle bg-primary text-white mb-2">
        <i class="bx bxs-bank fs-2"></i>
      </div>
      <h4 class="fw-bolder mb-0 text-uppercase tracking-wide">SARRAF-QMS &bull; صراف کیو ایم ایس</h4>
      <div class="fw-semibold text-muted small">{{ $token->branch->name }}</div>
      <small class="text-muted d-block" style="font-size: 11px;">Branch Code: {{ $token->branch->code }} &bull; {{ $token->branch->city }}</small>
    </div>

    <!-- Big Token Number -->
    <div class="text-center my-3 py-2 bg-light rounded-3 border border-dashed border-dark">
      <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 11px;">Your Queue Token Number</small>
      <h1 class="display-3 fw-bolder text-primary mb-0 font-monospace" style="letter-spacing: 3px;">
        {{ $token->token_number }}
      </h1>
      <div class="urdu-font fs-5 text-dark mt-1" style="line-height: 1.2;">
        آپ کا ٹوکن نمبر
      </div>
    </div>

    <!-- Service & Priority Classification -->
    <div class="py-2 border-bottom border-dashed mb-3">
      <div class="d-flex justify-content-between mb-1">
        <span class="text-muted small">Banking Service:</span>
        <strong class="text-end">{{ $token->serviceCategory->name }}</strong>
      </div>
      <div class="text-end urdu-font text-primary small" style="line-height: 1;">
        {{ $token->serviceCategory->name_ur }}
      </div>

      <div class="d-flex justify-content-between align-items-center mt-2">
        <span class="text-muted small">Priority Tier:</span>
        <span class="badge bg-{{ $token->priorityCategory->badge_color ?? 'primary' }}">
          {{ $token->priorityCategory->name }}
        </span>
      </div>
      @if($token->priorityCategory->code !== 'REGULAR')
        <small class="d-block text-danger fw-bold text-end mt-1" style="font-size: 10px;">
          * SBP Circular 01/2018 Fast-Track Precedence *
        </small>
      @endif
    </div>

    <!-- Estimated Wait & Position -->
    <div class="row g-2 text-center py-2 border-bottom border-dashed mb-3">
      <div class="col-6 border-end">
        <small class="text-muted d-block" style="font-size: 11px;">Customers Ahead</small>
        <span class="fs-4 fw-bold text-dark">{{ $waitStats['tokens_ahead'] }}</span>
      </div>
      <div class="col-6">
        <small class="text-muted d-block" style="font-size: 11px;">Est. Wait Time</small>
        <span class="fs-4 fw-bold text-warning">~{{ $waitStats['estimated_minutes'] }} Mins</span>
      </div>
    </div>

    <!-- Customer Information -->
    <div class="small mb-3">
      <div class="d-flex justify-content-between mb-1">
        <span class="text-muted">Customer:</span>
        <strong class="text-end">{{ $token->salutation ? $token->salutation . ' ' : '' }}{{ $token->customer_name }}</strong>
      </div>
      @if($token->account_number)
        <div class="d-flex justify-content-between mb-1">
          <span class="text-muted">Account No:</span>
          <span class="font-monospace fw-bold">{{ $token->account_number }}</span>
        </div>
      @endif
      @if($token->transaction_purpose)
        <div class="d-flex justify-content-between mb-1">
          <span class="text-muted">Service / Purpose:</span>
          <span>{{ $token->transaction_purpose }} @if($token->expected_amount) (PKR {{ number_format($token->expected_amount, 0) }}) @endif</span>
        </div>
      @endif
      <div class="d-flex justify-content-between">
        <span class="text-muted">Issued Date:</span>
        <span>{{ $token->created_at->format('d/m/Y h:i:s A') }}</span>
      </div>
    </div>

    <!-- QR Code for Mobile Tracking & Live Pass -->
    @php
      $lanHost = (request()->getHost() === '127.0.0.1' || request()->getHost() === 'localhost') ? '192.168.100.9:8080' : request()->getHttpHost();
      $mobileTrackUrl = request()->getScheme() . '://' . $lanHost . '/m/' . $token->token_number;
    @endphp
    <div class="text-center pt-2 border-top border-2 border-dark">
      <div class="p-2 bg-white d-inline-block border rounded mb-2 shadow-xs">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data={{ urlencode($mobileTrackUrl) }}" 
             alt="Mobile QR Code" width="125" height="125" class="img-fluid" />
      </div>
      <div class="small fw-bolder text-dark" style="font-size: 12px;">
        <i class="bx bx-scan text-primary me-1"></i> Scan with Phone Camera for Live E-Pass
      </div>
      <div class="text-muted small mt-1 font-monospace" style="font-size: 10px;">
        {{ $mobileTrackUrl }}
      </div>
      <div class="urdu-font text-muted small mt-1" style="line-height: 1.2;">
        موبائل سے کیو آر کوڈ اسکین کریں اور لائیو اسٹیٹس دیکھیں
      </div>
      <small class="text-muted d-block mt-2" style="font-size: 9px;">
        Sarraf-QMS (صراف) &bull; National Bank Queue System &bull; State Bank Regulatory Compliant
      </small>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  @media print {
    body {
      background: white !important;
      padding: 0 !important;
    }
    .ticket-slip {
      box-shadow: none !important;
      border: none !important;
      width: 100% !important;
      max-width: 320px !important;
      margin: 0 auto !important;
      padding: 10px !important;
    }
    .d-print-none {
      display: none !important;
    }
  }
</style>
@endpush
