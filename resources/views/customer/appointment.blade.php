@extends('layouts.blank')

@section('title', 'Online Bank Appointment & Token Booking')

@section('content')
<div class="min-vh-100 bg-light py-5">
  <div class="container" style="max-width: 760px;">
    <!-- Back to Admin & Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bx bx-left-arrow-alt me-1"></i> Operations Dashboard
      </a>
      <span class="badge bg-label-primary px-3 py-2">
        <i class="bx bx-mobile-alt me-1"></i> Customer Self-Service Portal
      </span>
    </div>

    <div class="card shadow-lg border-0 rounded-4">
      <div class="card-header bg-primary text-white p-4 rounded-top-4">
        <div class="d-flex align-items-center">
          <div class="p-3 bg-white text-primary rounded-3 me-3">
            <i class="bx bx-calendar-check fs-2"></i>
          </div>
          <div>
            <h3 class="fw-bold mb-1 text-white">Online Banking Appointment</h3>
            <p class="mb-0 text-white-50 small">
              Pre-book your branch visit and skip the waiting line (SBI & SBP E-Channel Tender Spec).
            </p>
          </div>
        </div>
      </div>

      <div class="card-body p-4">
        <!-- SBP Accessibility Notice -->
        <div class="alert alert-warning d-flex align-items-center border-0 mb-4 py-2">
          <i class="bx bx-accessibility fs-3 me-2 text-danger"></i>
          <div class="small">
            <strong>SBP Priority Policy:</strong> Special Persons (PWDs), Senior Citizens (60+), and Pensioners receive dedicated priority queues at all our branches.
          </div>
        </div>

        @if($errors->any())
          <div class="alert alert-danger py-2 small">
            <ul class="mb-0 ps-3">
              @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form action="{{ route('appointment.store') }}" method="POST">
          @csrf

          <!-- Step 1: Branch Selection -->
          <div class="mb-4">
            <label class="form-label fw-bold text-dark">
              <i class="bx bx-buildings text-primary me-1"></i> 1. Select Preferred Bank Branch
            </label>
            <select name="branch_id" class="form-select form-select-lg" required>
              @foreach($branches as $b)
                <option value="{{ $b->id }}">
                  {{ $b->code }} - {{ $b->name }} ({{ $b->city }}) &bull; {{ $b->address }}
                </option>
              @endforeach
            </select>
          </div>

          <!-- Step 2: Service Category -->
          <div class="mb-4">
            <label class="form-label fw-bold text-dark">
              <i class="bx bx-list-check text-primary me-1"></i> 2. Banking Service Required
            </label>
            <div class="row g-2">
              @foreach($services as $srv)
                <div class="col-md-6">
                  <label class="w-100 cursor-pointer">
                    <input type="radio" name="service_category_id" value="{{ $srv->id }}" class="btn-check" {{ $loop->first ? 'checked' : '' }}>
                    <div class="btn btn-outline-primary w-100 p-3 text-start rounded-3 d-flex align-items-center">
                      <i class="bx {{ $srv->icon }} fs-3 me-2"></i>
                      <div>
                        <div class="fw-bold">{{ $srv->name }}</div>
                        <div class="urdu-font small" style="line-height: 1;">{{ $srv->name_ur }}</div>
                      </div>
                    </div>
                  </label>
                </div>
              @endforeach
            </div>
          </div>

          <!-- Step 3: Priority Category -->
          <div class="mb-4">
            <label class="form-label fw-bold text-dark">
              <i class="bx bx-shield-quarter text-danger me-1"></i> 3. Customer Classification (SBP Priority Guidelines)
            </label>
            <select name="priority_category_id" class="form-select" required>
              @foreach($priorities as $pri)
                <option value="{{ $pri->id }}" {{ $pri->code === 'REGULAR' ? 'selected' : '' }}>
                  {{ $pri->name }} - {{ $pri->name_ur }} {{ $pri->weight_multiplier > 1 ? '(Fast-Track Priority Precedence)' : '' }}
                </option>
              @endforeach
            </select>
          </div>

          <!-- Step 4: Date & Time Slot -->
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label fw-bold text-dark">
                <i class="bx bx-calendar text-primary me-1"></i> 4. Appointment Date
              </label>
              <input type="date" name="appointment_date" class="form-control" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold text-dark">
                <i class="bx bx-time text-primary me-1"></i> Preferred Time Slot
              </label>
              <select name="time_slot" class="form-select" required>
                @foreach($timeSlots as $slot)
                  <option value="{{ $slot }}">{{ $slot }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <!-- Step 5: Customer Details -->
          <div class="p-3 bg-light rounded-3 border mb-4">
            <h6 class="fw-bold text-dark mb-3"><i class="bx bx-user text-primary me-1"></i> 5. Your Contact Information</h6>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small">Full Name</label>
                <input type="text" name="customer_name" class="form-control" placeholder="e.g. Asad Ullah" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small">Mobile Phone (For SMS Token Confirmation)</label>
                <input type="text" name="customer_phone" class="form-control" placeholder="e.g. 0300-1234567" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small">CNIC Number</label>
                <input type="text" name="customer_cnic" class="form-control" placeholder="e.g. 42101-1234567-1">
              </div>
              <div class="col-md-6">
                <label class="form-label small">Email Address (Optional)</label>
                <input type="email" name="customer_email" class="form-control" placeholder="name@example.com">
              </div>
              <div class="col-12">
                <label class="form-label small">Special Notes or Requirements</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Wheelchair ramp needed, Large cash deposit, etc."></textarea>
              </div>
            </div>
          </div>

          <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold shadow">
            <i class="bx bx-check-double me-1 fs-5"></i> CONFIRM BOOKING & GENERATE E-TOKEN
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
