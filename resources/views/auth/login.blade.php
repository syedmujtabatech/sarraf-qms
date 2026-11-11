@extends('layouts.blank')

@section('title', 'Sign In | Sarraf Banking QMS')

@section('content')
<style>
  html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background-color: #f8fafc;
  }

  .login-split-wrapper {
    min-height: 100vh;
    display: flex;
    width: 100%;
    overflow-x: hidden;
  }

  /* Left Showcase Panel */
  .login-showcase-panel {
    flex: 1 1 54%;
    position: relative;
    background: 
      linear-gradient(135deg, rgba(11, 19, 41, 0.90) 0%, rgba(15, 23, 42, 0.86) 50%, rgba(30, 27, 75, 0.92) 100%),
      url("{{ asset('assets/img/backgrounds/bank-branch.jpg') }}") center/cover no-repeat;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 3rem 3.5rem;
    color: #ffffff !important;
    overflow: hidden;
  }

  .login-showcase-panel::before {
    content: '';
    position: absolute;
    top: -15%;
    left: -15%;
    width: 60%;
    height: 60%;
    background: radial-gradient(circle, rgba(99, 102, 241, 0.35) 0%, transparent 70%);
    filter: blur(50px);
    pointer-events: none;
  }

  .bank-logo-box {
    width: 52px;
    height: 52px;
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.5);
    color: #ffffff;
    font-size: 1.75rem;
  }

  .glass-card-feature {
    background: rgba(255, 255, 255, 0.08) !important;
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 14px;
    padding: 1rem 1.25rem;
    margin-bottom: 0.9rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.25s ease;
  }

  .glass-card-feature:hover {
    background: rgba(255, 255, 255, 0.13) !important;
    border-color: rgba(99, 102, 241, 0.6) !important;
    transform: translateX(4px);
  }

  .glass-card-feature .feature-title {
    color: #ffffff !important;
    font-weight: 700;
    font-size: 0.92rem;
    margin-bottom: 2px;
  }

  .glass-card-feature .feature-desc {
    color: #cbd5e1 !important;
    font-size: 0.8rem;
    line-height: 1.35;
    margin: 0;
  }

  .feature-icon-circle {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
  }

  /* Right Form Panel */
  .login-form-panel {
    flex: 1 1 46%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2.5rem 2rem;
    background-color: #f8fafc;
  }

  .login-card-inner {
    width: 100%;
    max-width: 480px;
    background: #ffffff;
    border-radius: 20px;
    padding: 2.5rem;
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.06), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
  }

  .btn-login-submit {
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    border: none;
    color: #ffffff;
    font-weight: 600;
    padding: 0.75rem 1.25rem;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
    transition: all 0.2s ease;
  }

  .btn-login-submit:hover {
    background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
    color: #ffffff;
    box-shadow: 0 6px 16px rgba(99, 102, 241, 0.45);
    transform: translateY(-1px);
  }

  .role-btn-tile {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 8px 10px;
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.2s ease;
  }

  .role-btn-tile:hover {
    background: #f1f5f9;
    border-color: #6366f1;
    transform: translateY(-1px);
  }

  .role-btn-tile .user-name {
    font-size: 11px;
    font-weight: 700;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .role-btn-tile .role-label {
    font-size: 9px;
    font-weight: 700;
    color: #6366f1;
    text-transform: uppercase;
    letter-spacing: 0.3px;
  }
</style>

<div class="login-split-wrapper">
  <!-- Left Side: Modern Banking Graphic & Showcase -->
  <div class="login-showcase-panel d-none d-lg-flex">
    <!-- Top Branding -->
    <div class="d-flex align-items-center z-1">
      <div class="bank-logo-box me-3">
        <i class="bx bxs-bank"></i>
      </div>
      <div>
        <h3 class="fw-bolder mb-0 text-white tracking-wide">SARRAF BANKING</h3>
        <span class="text-info text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 1.5px;">
          National Queue &amp; Operations Architecture
        </span>
      </div>
    </div>

    <!-- Center Hero Content -->
    <div class="my-auto py-4 z-1" style="max-width: 580px;">
      <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3" style="background: rgba(99, 102, 241, 0.2); border: 1px solid rgba(129, 140, 248, 0.35);">
        <i class="bx bx-check-shield text-info"></i>
        <span class="text-white small fw-semibold">State Bank Regulatory Compliant &bull; Real-time Branch Flow</span>
      </div>

      <h1 class="display-6 fw-bold text-white mb-3" style="line-height: 1.25;">
        Intelligent Branch Queuing &amp; Mobile QR Ticketing
      </h1>
      <p class="fs-6 mb-4" style="color: #cbd5e1; line-height: 1.6;">
        Eliminate customer wait lines, enable instant smartphone QR tickets across all branches, and empower branch managers with real-time counter telemetry.
      </p>

      <!-- 3 Glassmorphism Feature Cards -->
      <div class="glass-card-feature">
        <div class="feature-icon-circle bg-primary text-white">
          <i class="bx bx-qr-scan"></i>
        </div>
        <div>
          <div class="feature-title">Dedicated Branch QR Code Passes</div>
          <p class="feature-desc">Customers scan the branch QR code with their mobile camera to instantly generate live digital tickets with wait time estimation.</p>
        </div>
      </div>

      <div class="glass-card-feature">
        <div class="feature-icon-circle bg-success text-white">
          <i class="bx bx-buildings"></i>
        </div>
        <div>
          <div class="feature-title">Role-Based Banking Hierarchy</div>
          <p class="feature-desc">Branch managers govern local branch counters and tickets; Regional managers oversee multi-branch SLA clusters.</p>
        </div>
      </div>

      <div class="glass-card-feature">
        <div class="feature-icon-circle bg-warning text-dark">
          <i class="bx bx-time-five"></i>
        </div>
        <div>
          <div class="feature-title">Live Counter Routing &amp; TV Chime Display</div>
          <p class="feature-desc">Split-screen lobby displays with synchronized audio announcements, counter status routing, and teller workbench dispatch.</p>
        </div>
      </div>

      <!-- Live Metrics Bar -->
      <div class="row g-2 mt-4 pt-3 border-top border-white border-opacity-10 text-center">
        <div class="col-4">
          <div class="fw-bolder fs-4 text-white">100%</div>
          <small class="text-white-50" style="font-size: 11px;">SBP Compliant</small>
        </div>
        <div class="col-4">
          <div class="fw-bolder fs-4 text-success">&lt; 5 Mins</div>
          <small class="text-white-50" style="font-size: 11px;">Avg Customer Wait</small>
        </div>
        <div class="col-4">
          <div class="fw-bolder fs-4 text-info">Live QR</div>
          <small class="text-white-50" style="font-size: 11px;">Mobile Touchless</small>
        </div>
      </div>
    </div>

    <!-- Bottom Status -->
    <div class="d-flex justify-content-between align-items-center pt-3 border-top border-white border-opacity-10 z-1 small" style="color: #94a3b8;">
      <div class="d-flex align-items-center">
        <span class="badge bg-success rounded-circle p-1 me-2" style="width: 8px; height: 8px;"></span>
        Core Banking Telemetry Engine Active
      </div>
      <div>v2.5.0 Enterprise &bull; Sarraf-QMS</div>
    </div>
  </div>

  <!-- Right Side: Clean Login Form & Fast Role Switcher -->
  <div class="login-form-panel">
    <div class="login-card-inner">
      
      <!-- Mobile Logo for small screens -->
      <div class="d-lg-none text-center mb-4">
        <div class="d-inline-flex p-3 rounded-circle bg-primary text-white shadow-sm mb-2">
          <i class="bx bxs-bank fs-1"></i>
        </div>
        <h4 class="fw-bolder mb-1 text-primary">Sarraf Banking QMS</h4>
        <p class="text-muted small">National Queue &amp; Branch Operations System</p>
      </div>

      <div class="mb-4">
        <div class="d-inline-flex align-items-center gap-1 badge bg-label-primary px-3 py-1 mb-2 fw-semibold">
          <i class="bx bx-shield-quarter"></i> Workstation Authentication
        </div>
        <h3 class="fw-bold text-dark mb-1">Sign In</h3>
        <p class="text-muted small mb-0">Enter your banking email or click a role shortcut below.</p>
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
          <label for="email" class="form-label fw-semibold text-dark small mb-1">Official Banking Email</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text bg-white"><i class="bx bx-envelope text-primary"></i></span>
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
          <div class="d-flex justify-content-between align-items-center mb-1">
            <label class="form-label fw-semibold text-dark small mb-0" for="password">Password</label>
          </div>
          <div class="input-group input-group-merge">
            <span class="input-group-text bg-white"><i class="bx bx-lock-alt text-primary"></i></span>
            <input
              type="password"
              id="password"
              class="form-control"
              name="password"
              value="sarraf123"
              placeholder="sarraf123"
              required />
            <span class="input-group-text bg-white cursor-pointer" onclick="togglePasswordVisibility()"><i class="bx bx-hide" id="pwdToggleIcon"></i></span>
          </div>
        </div>

        <div class="mb-3 d-flex justify-content-between align-items-center">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="remember-me" name="remember" checked />
            <label class="form-check-label small text-muted" for="remember-me"> Keep workstation active </label>
          </div>
        </div>

        <button class="btn btn-login-submit d-flex align-items-center justify-content-center w-100 fs-6" type="submit">
          <i class="bx bx-log-in me-2"></i> Sign In to Workstation
        </button>
      </form>

      <!-- 1-Click Fast Role Switcher -->
      <div class="divider my-3">
        <div class="divider-text text-uppercase small text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">
          Fast Role Switcher (1-Click Access)
        </div>
      </div>

      <div class="row g-2 mb-3">
        @foreach($users as $u)
          <div class="col-6">
            <a href="{{ route('login.quick', $u->id) }}" class="role-btn-tile">
              <div class="avatar avatar-xs flex-shrink-0">
                <span class="avatar-initial rounded-circle bg-label-{{ $u->roleModel->badge_color ?? 'primary' }}">
                  <i class="bx {{ $u->role === 'super_admin' ? 'bx-crown' : ($u->role === 'branch_manager' ? 'bx-buildings' : ($u->role === 'regional_manager' ? 'bx-globe' : ($u->role === 'chief_teller' ? 'bx-vault' : ($u->role === 'teller' ? 'bx-desktop' : 'bx-user')))) }}"></i>
                </span>
              </div>
              <div class="overflow-hidden">
                <div class="user-name">{{ $u->name }}</div>
                <div class="role-label">{{ $u->role_title }}</div>
              </div>
            </a>
          </div>
        @endforeach
      </div>

      <!-- Public Branch Terminals -->
      <div class="p-2 bg-light rounded-3 border text-center">
        <small class="text-muted fw-bold text-uppercase d-block mb-2" style="font-size: 9px; letter-spacing: 0.5px;">
          Public Customer Terminals
        </small>
        <div class="d-flex justify-content-center gap-2">
          <a href="{{ route('mobile.join') }}" target="_blank" class="btn btn-xs btn-outline-primary py-1 px-2">
            <i class="bx bx-qr-scan me-1"></i> Mobile QR
          </a>
          <a href="{{ route('kiosk.index') }}" target="_blank" class="btn btn-xs btn-outline-warning py-1 px-2">
            <i class="bx bx-touch me-1"></i> Self Kiosk
          </a>
          <a href="{{ route('display.index') }}" target="_blank" class="btn btn-xs btn-outline-info py-1 px-2">
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
