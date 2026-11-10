@extends('layouts.blank')

@section('title', 'Get Mobile Ticket | ' . ($currentBranch->name ?? 'Sarraf-QMS'))

@section('body-class', 'bg-light')

@section('content')
<div class="mobile-join-container min-vh-100 d-flex flex-column" style="max-width: 460px; margin: 0 auto; background: #ffffff;">
  <!-- Header -->
  <header class="bg-primary text-white p-3 shadow-sm sticky-top">
    <div class="d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center">
        <div class="p-2 bg-white text-primary rounded-3 me-2 shadow-xs">
          <i class="bx bxs-bank fs-4"></i>
        </div>
        <div>
          <h6 class="mb-0 fw-bold text-white">Sarraf Banking QMS</h6>
          <small class="text-white-50" style="font-size: 11px;">
            <i class="bx bx-map-pin me-1"></i>{{ $currentBranch->name ?? 'Main Branch' }}
          </small>
        </div>
      </div>
      <span class="badge bg-white text-primary fw-bold px-2 py-1">
        Code: {{ $currentBranch->code ?? 'SRF-001' }}
      </span>
    </div>
  </header>

  <!-- Banner -->
  <div class="bg-label-primary p-3 border-bottom text-center">
    <h5 class="fw-bold mb-1 text-primary">Mobile Queue Pass</h5>
    <p class="text-muted small mb-0">Select your service and enter your details to generate your digital token instantly.</p>
  </div>

  <main class="flex-grow-1 p-3">
    @if($errors->any())
      <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3">
        {{ $errors->first() }}
      </div>
    @endif

    <form action="{{ route('mobile.join.post') }}" method="POST" id="joinForm">
      @csrf
      <input type="hidden" name="branch_id" value="{{ $currentBranch->id }}">

      <!-- Step 1: Select Service -->
      <div class="mb-3">
        <label class="form-label fw-bold text-dark d-flex align-items-center mb-2">
          <span class="badge bg-primary rounded-circle me-2">1</span> Select Service Required:
        </label>

        <div class="row g-2">
          @foreach($services as $idx => $s)
            <div class="col-6">
              <label class="w-100 cursor-pointer mb-0">
                <input type="radio" name="service_category_id" value="{{ $s->id }}" class="btn-check" {{ $idx == 0 ? 'checked' : '' }} required>
                <div class="btn btn-outline-primary text-start w-100 p-3 h-100 rounded-3 d-flex flex-column justify-content-between service-card" style="border-width: 2px;">
                  <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-{{ $s->color ?? 'primary' }} me-1 font-monospace">{{ $s->prefix }}</span>
                    <i class="bx {{ $s->icon ?? 'bx-credit-card' }} text-{{ $s->color ?? 'primary' }} fs-4 ms-auto"></i>
                  </div>
                  <strong class="d-block text-dark small" style="line-height: 1.2;">{{ $s->name }}</strong>
                  <span class="text-muted" style="font-size: 10px;">Avg: {{ $s->avg_service_time_mins ?? 5 }} mins</span>
                </div>
              </label>
            </div>
          @endforeach
        </div>
      </div>

      <!-- Step 2: Customer Personal Details -->
      <div class="mb-3 p-3 bg-light rounded-3 border">
        <label class="form-label fw-bold text-dark d-flex align-items-center mb-3">
          <span class="badge bg-primary rounded-circle me-2">2</span> Customer Information:
        </label>

        <!-- Full Name -->
        <div class="mb-3">
          <label class="form-label small fw-bold text-dark">Full Name <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text bg-white"><i class="bx bx-user text-primary"></i></span>
            <input type="text" name="customer_name" class="form-control" placeholder="e.g. Muhammad Ali" required autofocus>
          </div>
        </div>

        <!-- Phone Number -->
        <div class="mb-2">
          <label class="form-label small fw-bold text-dark">Mobile Number <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text bg-white"><i class="bx bx-phone text-success"></i></span>
            <input type="tel" name="customer_phone" class="form-control" placeholder="e.g. 0300-1234567" required>
          </div>
          <small class="text-muted" style="font-size: 11px;">You will receive your ticket number on this device.</small>
        </div>

        <!-- Priority Option (Simple Toggle) -->
        @php
          $seniorPriority = $priorities->firstWhere('code', 'SENIOR_CITIZEN') ?? $priorities->where('weight_multiplier', '>', 1)->first();
          $regularPriority = $priorities->firstWhere('code', 'REGULAR') ?? $priorities->first();
        @endphp
        @if($seniorPriority)
          <div class="mt-3 pt-2 border-top">
            <div class="form-check form-switch mb-0">
              <input class="form-check-input" type="checkbox" id="isPriorityCheck" name="priority_category_id" value="{{ $seniorPriority->id }}">
              <label class="form-check-label small fw-semibold text-dark" for="isPriorityCheck">
                <i class="bx bx-heart text-danger me-1"></i> Senior Citizen / Priority Fast-Track
              </label>
            </div>
          </div>
        @endif
      </div>

      <!-- Submit Button -->
      <button type="submit" class="btn btn-success btn-lg w-100 py-3 fw-bold shadow rounded-pill mb-3 fs-5">
        <i class="bx bx-check-circle me-1"></i> Get Queue Ticket
      </button>

      <div class="text-center small text-muted">
        <i class="bx bx-shield-quarter text-success me-1"></i> Official Banking Queue System &bull; Live Counter Alerts
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
</style>
@endpush
