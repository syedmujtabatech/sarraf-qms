@extends('layouts.blank')

@section('title', 'Sign In | Sarraf Banking QMS')

@section('content')
<div class="min-vh-100 d-flex flex-column flex-lg-row bg-white">
  <!-- Left Side: Modern Banking Graphic & Showcase -->
  <div class="d-none d-lg-flex col-lg-6 col-xl-7 flex-column justify-content-between p-5 text-white position-relative" style="background: linear-gradient(135deg, #0b1329 0%, #172554 50%, #1e1b4b 100%);">
    <!-- Top Branding -->
    <div class="d-flex align-items-center mb-4 z-1">
      <div class="p-3 bg-primary rounded-4 text-white shadow-lg me-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
        <i class="bx bxs-bank fs-2"></i>
      </div>
      <div>
        <h4 class="fw-bolder mb-0 text-white tracking-wide">SARRAF BANKING</h4>
        <small class="text-white-50 text-uppercase letter-spacing-1" style="font-size: 11px;">National Queue & Operations Architecture</small>
      </div>
    </div>

    <!-- Center Hero Illustration & Features -->
    <div class="my-auto py-4 z-1" style="max-width: 620px;">
      <span class="badge bg-primary bg-opacity-25 text-info border border-info border-opacity-25 px-3 py-2 rounded-pill mb-3">
        <i class="bx bx-check-shield me-1"></i> State Bank Regulatory Compliant &bull; Real-time Branch Flow
      </span>
      <h1 class="display-6 fw-bold text-white mb-3" style="line-height: 1.25;">
        Intelligent Branch Queuing & Mobile QR Ticketing
      </h1>
      <p class="text-white-50 fs-6 mb-4">
        Eliminate customer wait lines, enable instant smartphone QR tickets across all branches, and empower branch managers with real-time counter telemetry.
      </p>

      <!-- 3 Key Features -->
      <div class="row g-3">
        <div class="col-12">
          <div class="p-3 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-10 d-flex align-items-center backdrop-blur">
            <div class="avatar avatar-sm rounded-circle bg-primary text-white me-3 d-flex align-items-center justify-content-center">
              <i class="bx bx-qr-scan fs-4"></i>
            </div>
            <div>
              <div class="fw-bold text-white small">Dedicated Branch QR Code Passes</div>
              <small class="text-white-50" style="font-size: 11px;">Customers scan the branch QR code from phone camera to instantly generate live digital tickets.</small>
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="p-3 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-10 d-flex align-items-center backdrop-blur">
            <div class="avatar avatar-sm rounded-circle bg-success text-white me-3 d-flex align-items-center justify-content-center">
              <i class="bx bx-buildings fs-4"></i>
            </div>
            <div>
              <div class="fw-bold text-white small">Role-Based Banking Hierarchy</div>
              <small class="text-white-50" style="font-size: 11px;">Branch managers govern local branch operations; Regional managers oversee multi-branch clusters.</small>
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="p-3 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-10 d-flex align-items-center backdrop-blur">
            <div class="avatar avatar-sm rounded-circle bg-warning text-dark me-3 d-flex align-items-center justify-content-center">
              <i class="bx bx-time-five fs-4"></i>
            </div>
            <div>
              <div class="fw-bold text-white small">Live Counter Routing & TV Chime Display</div>
              <small class="text-white-50" style="font-size: 11px;">Split-screen lobby displays with synchronized audio announcements and teller workbench dispatch.</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Status -->
    <div class="d-flex justify-content-between align-items-center pt-3 border-top border-white border-opacity-10 z-1 small text-white-50">
      <div class="d-flex align-items-center">
        <span class="badge bg-success rounded-circle p-1 me-2" style="width: 8px; height: 8px;"></span>
        Core Banking Telemetry Engine Active
      </div>
      <div>v2.5.0 Enterprise</div>
    </div>
  </div>

  <!-- Right Side: Clean Login Form & Fast Role Switcher -->
  <div class="col-12 col-lg-6 col-xl-5 d-flex flex-column justify-content-center p-4 p-md-5">
    <div class="mx-auto" style="max-width: 460px; width: 100%;">
      
      <!-- Mobile Logo for small screens -->
      <div class="d-lg-none text-center mb-4">
        <div class="d-inline-flex p-3 rounded-circle bg-primary text-white shadow-sm mb-2">
          <i class="bx bxs-bank fs-1"></i>
        </div>
        <h3 class="fw-bolder mb-1 text-primary">Sarraf Banking QMS</h3>
        <p class="text-muted small">National Queue & Branch Operations System</p>
      </div>

      <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1">Banking Workstation Sign In</h3>
        <p class="text-muted small mb-0">Enter your official banking credentials or use the 1-click role switcher below.</p>
      </div>

      @if($errors->any())
        <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3">
          <i class="bx bx-error-circle me-1"></i> {{ $errors->first() }}
        </div>
      @endif

      <!-- Standard Login Form -->
      <form id="formAuthentication" action="{{ route('login.post') }}" method="POST" class="mb-4">
        @csrf
        <div class="mb-3">
          <label for="email" class="form-label fw-semibold text-dark small">Official Banking Email</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="bx bx-envelope"></i></span>
            <input
              type="email"
              class="form-control"
              id="email"
              name="email"
              value="{{ old('email', 'admin@sarraf.com') }}"
              placeholder="name@sarraf.com"
              autofocus
              required />
          </div>
        </div>

        <div class="mb-3">
          <div class="d-flex justify-content-between align-items-center">
            <label class="form-label fw-semibold text-dark small mb-0" for="password">Password</label>
          </div>
          <div class="input-group input-group-merge mt-1">
            <span class="input-group-text"><i class="bx bx-lock-alt"></i></span>
            <input
              type="password"
              id="password"
              class="form-control"
              name="password"
              value="admin123"
              placeholder="Enter password"
              required />
            <span class="input-group-text cursor-pointer" onclick="togglePasswordVisibility()"><i class="bx bx-hide" id="pwdToggleIcon"></i></span>
          </div>
        </div>

        <div class="mb-3 d-flex justify-content-between align-items-center">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="remember-me" name="remember" checked />
            <label class="form-check-label small" for="remember-me"> Keep workstation active </label>
          </div>
        </div>

        <button class="btn btn-primary d-grid w-100 py-2 fw-semibold fs-6 shadow-sm" type="submit">
          <i class="bx bx-log-in me-1"></i> Sign In to Workstation
        </button>
      </form>

      <!-- 1-Click Fast Role Switcher -->
      <div class="divider my-4">
        <div class="divider-text text-uppercase small text-muted fw-bold" style="font-size: 11px;">
          Fast Role Switcher (1-Click Access)
        </div>
      </div>

      <div class="row g-2 mb-4">
        @foreach($users as $u)
          <div class="col-6">
            <a href="{{ route('login.quick', $u->id) }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center w-100 text-start p-2 rounded-3 border">
              <div class="avatar avatar-xs me-2">
                <span class="avatar-initial rounded-circle bg-label-{{ $u->roleModel->badge_color ?? 'primary' }}">
                  <i class="bx {{ $u->role === 'super_admin' ? 'bx-crown' : ($u->role === 'branch_manager' ? 'bx-buildings' : ($u->role === 'regional_manager' ? 'bx-globe' : ($u->role === 'chief_teller' ? 'bx-vault' : ($u->role === 'teller' ? 'bx-desktop' : 'bx-user')))) }}"></i>
                </span>
              </div>
              <div class="overflow-hidden">
                <div class="fw-bold text-truncate" style="font-size: 11px;">{{ $u->name }}</div>
                <div class="text-primary text-uppercase" style="font-size: 9px; letter-spacing: 0.5px;">{{ $u->role_title }}</div>
              </div>
            </a>
          </div>
        @endforeach
      </div>

      <!-- Public Branch Terminals -->
      <div class="p-3 bg-light rounded-3 border text-center">
        <small class="text-muted fw-bold text-uppercase d-block mb-2" style="font-size: 10px; letter-spacing: 0.5px;">
          Live Public Customer Interfaces
        </small>
        <div class="d-flex justify-content-center gap-2">
          <a href="{{ route('mobile.join') }}" target="_blank" class="btn btn-sm btn-primary">
            <i class="bx bx-qr-scan me-1"></i> Mobile QR Pass
          </a>
          <a href="{{ route('kiosk.index') }}" target="_blank" class="btn btn-sm btn-warning">
            <i class="bx bx-touch me-1"></i> Branch Kiosk
          </a>
          <a href="{{ route('display.index') }}" target="_blank" class="btn btn-sm btn-info">
            <i class="bx bx-tv me-1"></i> TV Screen
          </a>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function togglePasswordVisibility() {
    const pwdInput = document.getElementById('password');
    const icon = document.getElementById('pwdToggleIcon');
    if (pwdInput.type === 'password') {
      pwdInput.type = 'text';
      icon.classList.remove('bx-hide');
      icon.classList.add('bx-show');
    } else {
      pwdInput.type = 'password';
      icon.classList.remove('bx-show');
      icon.classList.add('bx-hide');
    }
  }
</script>
@endpush
