@extends('layouts.blank')

@section('title', 'Thank You for Your Feedback')

@section('content')
<div class="min-vh-100 bg-light py-5 d-flex align-items-center justify-content-center">
  <div class="container" style="max-width: 500px;">
    <div class="card shadow-lg border-0 rounded-4 text-center p-5">
      <div class="avatar avatar-xl rounded-circle bg-label-success mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
        <i class="bx bx-heart fs-1 text-success"></i>
      </div>
      <h3 class="fw-bold mb-1 text-dark">Thank You!</h3>
      <p class="text-muted mb-4">Your rating of <strong>{{ $feedback->rating }}/5 Stars</strong> has been logged into the Bank Management Dashboard.</p>
      
      <div class="d-flex justify-content-center gap-2">
        <a href="{{ route('kiosk.index') }}" class="btn btn-primary">
          <i class="bx bx-touch me-1"></i> Return to Kiosk
        </a>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
          <i class="bx bx-home-alt me-1"></i> Dashboard
        </a>
      </div>
    </div>
  </div>
</div>
@endsection
