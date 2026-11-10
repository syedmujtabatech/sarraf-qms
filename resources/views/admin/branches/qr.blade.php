@extends('layouts.admin')

@section('title', 'Branch QR Standee: ' . $branch->name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
      <h4 class="fw-bold py-1 mb-0">Branch Mobile QR Pass</h4>
      <p class="text-muted small mb-0">Customer touchless entrance QR standee for {{ $branch->name }}</p>
    </div>
    <div>
      <button onclick="window.print()" class="btn btn-primary me-2">
        <i class="bx bx-printer me-1"></i> Print Standee Poster
      </button>
      <a href="{{ route('admin.branches.index') }}" class="btn btn-outline-secondary">
        <i class="bx bx-arrow-back me-1"></i> Back to Branches
      </a>
    </div>
  </div>

  @php
    $currentHost = request()->getHttpHost();
    $isLocal = in_array(request()->getHost(), ['127.0.0.1', 'localhost']);
    if ($isLocal && !empty(config('app.url')) && !str_contains(config('app.url'), 'localhost') && !str_contains(config('app.url'), '127.0.0.1')) {
        $baseUrl = rtrim(config('app.url'), '/');
    } else {
        $scheme = (request()->isSecure() || request()->header('x-forwarded-proto') === 'https') ? 'https' : request()->getScheme();
        $baseUrl = $scheme . '://' . $currentHost;
    }
    $branchQrUrl = $baseUrl . '/join?branch=' . $branch->id;
  @endphp

  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <!-- Printable Standee Card -->
      <div class="card shadow-lg border-2 border-primary rounded-4 text-center p-4 p-md-5 bg-white qr-standee-card">
        <!-- Logo Header -->
        <div class="d-inline-flex p-3 rounded-circle bg-primary text-white shadow-sm mb-3 mx-auto">
          <i class="bx bxs-bank fs-1"></i>
        </div>
        <h3 class="fw-bolder mb-1 text-primary">SARRAF BANKING</h3>
        <div class="badge bg-label-primary px-3 py-1 mb-3 text-uppercase font-monospace fs-6">
          {{ $branch->name }}
        </div>
        <div class="text-muted small mb-4">Branch Code: <strong>{{ $branch->code }}</strong> &bull; {{ $branch->city }}</div>

        <!-- Big QR Code Box -->
        <div class="p-3 bg-light rounded-4 border border-2 border-dashed border-primary d-inline-block mx-auto mb-4 shadow-sm">
          <img src="https://api.qrserver.com/v1/create-qr-code/?size=260x260&data={{ urlencode($branchQrUrl) }}" 
               alt="Branch QR Code" width="220" height="220" class="img-fluid rounded" />
        </div>

        <h4 class="fw-bold text-dark mb-2">Scan for Mobile Queue Pass</h4>
        <p class="text-muted fs-6 mb-3">
          Open your phone camera, scan this QR code, enter your name & phone, and receive your digital ticket instantly.
        </p>

        <div class="p-2 bg-light rounded-3 font-monospace small text-primary mb-3 text-truncate">
          {{ $branchQrUrl }}
        </div>

        <div class="d-flex justify-content-center gap-2 d-print-none">
          <a href="{{ $branchQrUrl }}" target="_blank" class="btn btn-outline-primary btn-sm">
            <i class="bx bx-mobile-alt me-1"></i> Test on Browser
          </a>
          <button onclick="navigator.clipboard.writeText('{{ $branchQrUrl }}'); alert('Copied to clipboard!');" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-copy me-1"></i> Copy Link
          </button>
        </div>

        <div class="mt-4 pt-3 border-top text-muted small" style="font-size: 11px;">
          State Bank Regulatory Compliant Queue Management &bull; Instant Counter Alerts
        </div>
      </div>
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
    .layout-navbar, .layout-menu, .content-footer, .d-print-none {
      display: none !important;
    }
    .content-wrapper {
      padding: 0 !important;
      margin: 0 !important;
    }
    .qr-standee-card {
      border: 3px solid #000 !important;
      box-shadow: none !important;
      max-width: 100% !important;
      margin-top: 20px !important;
    }
  }
</style>
@endpush
