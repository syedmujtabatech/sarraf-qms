@extends('layouts.blank')

@section('title', 'Appointment Confirmed: ' . $appointment->appointment_code)

@section('content')
<div class="min-vh-100 bg-light py-5 d-flex align-items-center justify-content-center">
  <div class="container" style="max-width: 540px;">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
      <!-- Success Top Banner -->
      <div class="bg-success text-white p-4 text-center">
        <div class="avatar avatar-xl rounded-circle bg-white text-success mx-auto mb-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
          <i class="bx bx-check fs-1"></i>
        </div>
        <h3 class="fw-bolder text-white mb-1">Appointment Confirmed!</h3>
        <p class="mb-0 text-white-50 small">Your banking visit has been scheduled in the queue core.</p>
      </div>

      <div class="card-body p-4 text-center">
        <!-- Appointment Code Box -->
        <div class="p-3 bg-light rounded-3 border border-dashed mb-4">
          <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 11px;">Appointment Reference Code</small>
          <h2 class="fw-bolder text-primary mb-0 font-monospace tracking-wide">{{ $appointment->appointment_code }}</h2>
          <small class="text-success"><i class="bx bx-check-circle me-1"></i> Verified & Active in Core QMS</small>
        </div>

        <!-- Appointment Details List -->
        <div class="text-start border rounded-3 p-3 mb-4 bg-white">
          <div class="d-flex justify-content-between py-1 border-bottom">
            <span class="text-muted small">Customer Name:</span>
            <strong>{{ $appointment->customer_name }}</strong>
          </div>
          <div class="d-flex justify-content-between py-1 border-bottom">
            <span class="text-muted small">Branch:</span>
            <strong>{{ $appointment->branch->name }}</strong>
          </div>
          <div class="d-flex justify-content-between py-1 border-bottom">
            <span class="text-muted small">Service:</span>
            <span class="badge bg-label-primary">{{ $appointment->serviceCategory->name }}</span>
          </div>
          <div class="d-flex justify-content-between py-1 border-bottom">
            <span class="text-muted small">Date & Slot:</span>
            <strong>{{ $appointment->appointment_date->format('M d, Y') }} ({{ $appointment->time_slot }})</strong>
          </div>
          <div class="d-flex justify-content-between py-1">
            <span class="text-muted small">Priority:</span>
            <span class="badge bg-{{ $appointment->priorityCategory->badge_color ?? 'primary' }}">
              {{ $appointment->priorityCategory->name }}
            </span>
          </div>
        </div>

        <!-- Mobile QR Code -->
        @php
          $currentHost = request()->getHttpHost();
          $isLocal = in_array(request()->getHost(), ['127.0.0.1', 'localhost']);
          if ($isLocal && !empty(config('app.url')) && !str_contains(config('app.url'), 'localhost') && !str_contains(config('app.url'), '127.0.0.1')) {
              $baseUrl = rtrim(config('app.url'), '/');
          } else {
              $scheme = (request()->isSecure() || request()->header('x-forwarded-proto') === 'https') ? 'https' : request()->getScheme();
              $baseUrl = $scheme . '://' . $currentHost;
          }
          $apptQrUrl = $baseUrl . '/appointment/confirmation/' . $appointment->id;
        @endphp
        <div class="mb-4">
          <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data={{ urlencode($apptQrUrl) }}" alt="QR" class="img-fluid border p-2 rounded shadow-xs" width="130" />
          <div class="small text-muted mt-2">Scan with your mobile camera to view your digital pass on phone.</div>
          <div class="font-monospace text-muted small" style="font-size: 10px;">{{ $apptQrUrl }}</div>
        </div>

        <!-- Check-in Now Button -->
        <form action="{{ route('appointment.checkIn', $appointment->id) }}" method="POST" class="mb-3">
          @csrf
          <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold shadow">
            <i class="bx bx-log-in-circle me-1 fs-5"></i> Check-in Now & Generate Active Queue Token
          </button>
        </form>

        <a href="{{ route('kiosk.index') }}" class="btn btn-outline-secondary w-100">
          <i class="bx bx-home-alt me-1"></i> Return to Kiosk
        </a>
      </div>
    </div>
  </div>
</div>
@endsection
