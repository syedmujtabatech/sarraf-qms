@extends('layouts.blank')

@section('title', 'Customer Service Feedback')

@section('content')
<div class="min-vh-100 bg-light py-5 d-flex align-items-center justify-content-center">
  <div class="container" style="max-width: 600px;">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
      <!-- Header -->
      <div class="bg-primary text-white p-4 text-center">
        <div class="avatar avatar-xl rounded-circle bg-white text-primary mx-auto mb-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 65px; height: 65px;">
          <i class="bx bx-star fs-1 text-warning"></i>
        </div>
        <h3 class="fw-bolder text-white mb-1">Customer Service Feedback</h3>
        <p class="mb-0 text-white-50 small">Rate your banking experience with us today (SBI Tender Compliance).</p>
      </div>

      <div class="card-body p-4">
        <form action="{{ route('feedback.store') }}" method="POST">
          @csrf

          @if($token)
            <input type="hidden" name="token_id" value="{{ $token->id }}">
            <div class="p-3 bg-light rounded-3 border mb-4 text-center">
              <small class="text-muted text-uppercase d-block" style="font-size: 11px;">Serving Counter & Token</small>
              <h3 class="fw-bolder text-primary mb-0 font-monospace">{{ $token->token_number }}</h3>
              <div class="small fw-semibold text-dark">{{ $token->branch->name }} &bull; Counter #{{ $token->counter->counter_number ?? '-' }}</div>
            </div>
          @else
            <!-- Token Search if not passed in URL -->
            <div class="mb-4">
              <label class="form-label fw-bold">Select Token to Rate</label>
              <select name="token_id" class="form-select" required>
                <option value="">-- Choose your completed token --</option>
                @foreach(\App\Models\Token::where('status', 'completed')->latest()->take(10)->get() as $t)
                  <option value="{{ $t->id }}">
                    {{ $t->token_number }} - {{ $t->customer_name }} ({{ $t->branch->name }})
                  </option>
                @endforeach
              </select>
            </div>
          @endif

          <!-- Star Rating (1 to 5) -->
          <div class="mb-4 text-center">
            <label class="form-label fw-bold d-block mb-3 fs-5 text-dark">How would you rate your service?</label>
            <div class="star-rating d-inline-flex gap-2">
              @for($i = 5; $i >= 1; $i--)
                <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" class="d-none star-input" {{ $i === 5 ? 'checked' : '' }}>
                <label for="star{{ $i }}" class="star-label cursor-pointer text-warning fs-1" title="{{ $i }} Stars">
                  <i class="bx bxs-star"></i>
                </label>
              @endfor
            </div>
          </div>

          <!-- Quick Feedback Tags -->
          <div class="mb-4">
            <label class="form-label fw-bold small text-muted text-uppercase d-block">What did you like or think we can improve?</label>
            <div class="d-flex flex-wrap gap-2">
              @php
                $tags = ['Quick Service', 'Courteous Staff', 'Accurate Handling', 'Clear Audio Calls', 'Clean Waiting Hall', 'Long Delay', 'System Glitch'];
              @endphp
              @foreach($tags as $tag)
                <label class="cursor-pointer">
                  <input type="checkbox" name="feedback_tags[]" value="{{ $tag }}" class="btn-check">
                  <span class="btn btn-outline-secondary btn-sm rounded-pill">{{ $tag }}</span>
                </label>
              @endforeach
            </div>
          </div>

          <!-- Comments -->
          <div class="mb-4">
            <label class="form-label fw-bold small">Additional Comments or Suggestions</label>
            <textarea name="comments" class="form-control" rows="3" placeholder="Tell us about your experience with our branch counter operator..."></textarea>
          </div>

          <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold shadow">
            <i class="bx bx-send me-1"></i> SUBMIT CUSTOMER FEEDBACK
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .star-rating {
    flex-direction: row-reverse;
  }
  .star-label {
    transition: transform 0.2s;
  }
  .star-label:hover,
  .star-label:hover ~ .star-label {
    color: #ffab00 !important;
    transform: scale(1.2);
  }
  .star-input:checked ~ .star-label {
    color: #ffab00 !important;
  }
  .btn-check:checked + span {
    background-color: #696cff !important;
    color: white !important;
    border-color: #696cff !important;
  }
</style>
@endpush
