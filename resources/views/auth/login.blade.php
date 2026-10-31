@extends('layouts.blank')

@section('title', 'Sign In | Sarraf-QMS')

@section('content')
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y min-vh-100 d-flex align-items-center justify-content-center">
    <div class="authentication-inner" style="max-width: 560px; width: 100%;">
      <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-4 p-md-5">
          <!-- Logo & Header -->
          <div class="text-center mb-4">
            <div class="d-inline-flex p-3 rounded-circle bg-primary text-white shadow-sm mb-3">
              <i class="bx bxs-bank fs-1"></i>
            </div>
            <h2 class="fw-bolder mb-1 text-primary">Sarraf-QMS</h2>
            <div class="urdu-font fs-4 text-dark mb-1" style="line-height: 1;">صراف &bull; الیکٹرانک کیو مینجمنٹ سسٹم</div>
            <p class="text-muted mb-0 small">National Banking Queue & Treasury Flow Management System</p>
            <span class="badge bg-label-info mt-2">
              <i class="bx bx-shield-quarter me-1"></i> SBP Circular 01/2018 & SBI Tender Compliant
            </span>
          </div>

          @if($errors->any())
            <div class="alert alert-danger py-2 small">
              {{ $errors->first() }}
            </div>
          @endif

          <!-- Standard Login Form -->
          <form id="formAuthentication" class="mb-3" action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="email" class="form-label fw-semibold">Official Banking Email</label>
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

            <div class="mb-3 form-password-toggle">
              <div class="d-flex justify-content-between">
                <label class="form-label fw-semibold" for="password">Password</label>
              </div>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bx-lock-alt"></i></span>
                <input
                  type="password"
                  id="password"
                  class="form-control"
                  name="password"
                  value="admin123"
                  placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                  required />
              </div>
            </div>

            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember-me" name="remember" checked />
                <label class="form-check-label" for="remember-me"> Remember Session </label>
              </div>
            </div>

            <button class="btn btn-primary d-grid w-100 py-2 fw-semibold fs-6 shadow-sm" type="submit">
              <i class="bx bx-log-in me-1"></i> Authenticate & Enter System
            </button>
          </form>

          <!-- 1-Click Fast Role Switcher for Real Banking Roles -->
          <div class="divider my-4">
            <div class="divider-text text-uppercase small text-muted fw-bold">Real Banking Hierarchy - 1-Click Access</div>
          </div>

          <div class="row g-2">
            @foreach($users as $u)
              <div class="col-6">
                <a href="{{ route('login.quick', $u->id) }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center w-100 text-start p-2 rounded-3">
                  <div class="avatar avatar-xs me-2">
                    <span class="avatar-initial rounded-circle bg-label-{{ $u->roleModel->badge_color ?? 'primary' }}">
                      <i class="bx {{ $u->role === 'super_admin' ? 'bx-crown' : ($u->role === 'branch_manager' ? 'bx-buildings' : ($u->role === 'chief_teller' ? 'bx-vault' : ($u->role === 'teller' ? 'bx-desktop' : 'bx-user'))) }}"></i>
                    </span>
                  </div>
                  <div class="overflow-hidden">
                    <div class="fw-bold text-truncate" style="font-size: 11px;">{{ $u->name }}</div>
                    <div class="text-primary text-uppercase" style="font-size: 9px;">{{ $u->role_title }}</div>
                  </div>
                </a>
              </div>
            @endforeach
          </div>

          <div class="divider my-4">
            <div class="divider-text text-uppercase small text-muted fw-bold">Public Branch Terminals</div>
          </div>

          <div class="d-flex justify-content-center gap-2">
            <a href="{{ route('kiosk.index') }}" target="_blank" class="btn btn-sm btn-label-warning">
              <i class="bx bx-touch me-1"></i> Self Kiosk
            </a>
            <a href="{{ route('display.index') }}" target="_blank" class="btn btn-sm btn-label-info">
              <i class="bx bx-tv me-1"></i> TV Screen
            </a>
            <a href="{{ route('appointment.index') }}" target="_blank" class="btn btn-sm btn-label-primary">
              <i class="bx bx-calendar me-1"></i> E-Booking
            </a>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection
