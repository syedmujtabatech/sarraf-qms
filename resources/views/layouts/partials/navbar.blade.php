<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
      <i class="bx bx-menu bx-sm"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <!-- Live System Status & Branch Indicator -->
    <div class="navbar-nav align-items-center">
      <div class="nav-item d-flex align-items-center">
        <span class="badge bg-label-success me-2 py-2 px-3 d-flex align-items-center">
          <span class="spinner-grow spinner-grow-sm text-success me-2" role="status" style="width: 8px; height: 8px;"></span>
          <span class="fw-semibold">Sarraf Core: ONLINE</span>
        </span>
        <span class="text-muted d-none d-lg-inline" style="font-size: 13px;">
          <i class="bx bx-shield-check text-primary me-1"></i> National Bank SBP / SBI Compliant System
        </span>
      </div>
    </div>

    <ul class="navbar-nav flex-row align-items-center ms-auto">
      <!-- Quick Device Links -->
      <li class="nav-item me-2 d-none d-sm-block">
        <a href="{{ route('teller.index') }}" class="btn btn-sm btn-outline-success">
          <i class="bx bx-desktop me-1"></i> Teller Desk
        </a>
      </li>
      <li class="nav-item me-2 d-none d-sm-block">
        <a href="{{ route('display.index') }}" target="_blank" class="btn btn-sm btn-outline-info">
          <i class="bx bx-tv me-1"></i> TV Screen
        </a>
      </li>
      <li class="nav-item me-3 d-none d-sm-block">
        <a href="{{ route('kiosk.index') }}" target="_blank" class="btn btn-sm btn-outline-warning">
          <i class="bx bx-touch me-1"></i> Kiosk
        </a>
      </li>

      <!-- User Dropdown -->
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar avatar-online">
            <span class="avatar-initial rounded-circle bg-label-primary fw-bold">
              {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
            </span>
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow">
          <li>
            <a class="dropdown-item" href="#">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar avatar-online">
                    <span class="avatar-initial rounded-circle bg-label-primary fw-bold">
                      {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                    </span>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <span class="fw-semibold d-block text-truncate" style="max-width: 170px;">{{ auth()->user()->name ?? 'Administrator' }}</span>
                  <small class="badge bg-label-primary text-uppercase" style="font-size: 10px;">{{ auth()->user()->role_title ?? 'Officer' }}</small>
                </div>
              </div>
            </a>
          </li>
          <li>
            <div class="dropdown-divider"></div>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
              <i class="bx bx-home-circle me-2"></i>
              <span class="align-middle">Dashboard</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('teller.index') }}">
              <i class="bx bx-desktop me-2"></i>
              <span class="align-middle">Teller Console</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('admin.roles.index') }}">
              <i class="bx bx-key me-2"></i>
              <span class="align-middle">Roles & Permissions</span>
            </a>
          </li>
          <li>
            <div class="dropdown-divider"></div>
          </li>
          <li>
            <form method="POST" action="{{ route('logout') }}" id="logout-form">
              @csrf
              <button type="submit" class="dropdown-item text-danger">
                <i class="bx bx-power-off me-2"></i>
                <span class="align-middle">Sign Out</span>
              </button>
            </form>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</nav>
