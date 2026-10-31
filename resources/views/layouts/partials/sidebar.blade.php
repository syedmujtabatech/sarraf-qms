<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo py-3">
    <a href="{{ route('admin.dashboard') }}" class="app-brand-link d-flex align-items-center">
      <span class="app-brand-logo demo me-2">
        <span class="badge bg-primary p-2 rounded-3 text-white shadow-sm">
          <i class="bx bxs-bank fs-4"></i>
        </span>
      </span>
      <div class="d-flex flex-column">
        <span class="app-brand-text demo menu-text fw-bolder text-primary fs-5 lh-1">Sarraf-QMS</span>
        <small class="text-muted fw-bold" style="font-size: 9px; letter-spacing: 0.5px;">صراف &bull; CORE BANKING QMS</small>
      </div>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
      <i class="bx bx-chevron-left bx-sm align-middle"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    <!-- Operations & Monitoring -->
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Operations & Live Monitoring</span>
    </li>
    
    <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
      <a href="{{ route('admin.dashboard') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div>Operations Cockpit</div>
      </a>
    </li>

    <li class="menu-item {{ request()->routeIs('teller.index') ? 'active' : '' }}">
      <a href="{{ route('teller.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-desktop text-success"></i>
        <div>Teller Console</div>
        <div class="badge bg-label-success fs-tiny rounded-pill ms-auto">Desk</div>
      </a>
    </li>

    <li class="menu-item">
      <a href="{{ route('display.index') }}" target="_blank" class="menu-link">
        <i class="menu-icon tf-icons bx bx-tv text-info"></i>
        <div>Lobby TV Display</div>
        <span class="badge bg-label-info ms-auto" style="font-size: 9px;"><i class="bx bx-volume-full"></i> Audio</span>
      </a>
    </li>

    <li class="menu-item">
      <a href="{{ route('display.pole', 1) }}" target="_blank" class="menu-link">
        <i class="menu-icon tf-icons bx bx-broadcast text-warning"></i>
        <div>Counter Pole Display</div>
        <span class="badge bg-label-warning ms-auto" style="font-size: 9px;">LED</span>
      </a>
    </li>

    <li class="menu-item">
      <a href="{{ route('kiosk.index') }}" target="_blank" class="menu-link">
        <i class="menu-icon tf-icons bx bx-touch text-warning"></i>
        <div>Self-Service Kiosk</div>
        <span class="badge bg-label-warning ms-auto" style="font-size: 9px;">Touch</span>
      </a>
    </li>

    <!-- Public Portals -->
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Customer Portals</span>
    </li>

    <li class="menu-item">
      <a href="{{ route('mobile.join') }}" target="_blank" class="menu-link">
        <i class="menu-icon tf-icons bx bx-qr-scan text-success"></i>
        <div>Mobile QR Ticket (Scan)</div>
        <span class="badge bg-label-success ms-auto" style="font-size: 9px;">E-Pass</span>
      </a>
    </li>

    <li class="menu-item">
      <a href="{{ route('appointment.index') }}" target="_blank" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calendar-check text-primary"></i>
        <div>Online Appointments</div>
        <span class="badge bg-label-primary ms-auto" style="font-size: 9px;">Mobile</span>
      </a>
    </li>

    <li class="menu-item">
      <a href="{{ route('feedback.index') }}" target="_blank" class="menu-link">
        <i class="menu-icon tf-icons bx bx-star text-warning"></i>
        <div>Customer Feedback</div>
      </a>
    </li>

    <!-- Real Banking Infrastructure -->
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Branch Hierarchy & Staff</span>
    </li>

    <li class="menu-item {{ request()->routeIs('admin.regions.*') ? 'active' : '' }}">
      <a href="{{ route('admin.regions.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-map-pin text-primary"></i>
        <div>Regional Clusters</div>
      </a>
    </li>

    <li class="menu-item {{ request()->routeIs('admin.branches.*') ? 'active' : '' }}">
      <a href="{{ route('admin.branches.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-buildings"></i>
        <div>Branch Network</div>
      </a>
    </li>

    <li class="menu-item {{ request()->routeIs('admin.counters.*') ? 'active' : '' }}">
      <a href="{{ route('admin.counters.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-grid-alt"></i>
        <div>Counter Stations</div>
      </a>
    </li>

    <li class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
      <a href="{{ route('admin.users.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-user-pin text-success"></i>
        <div>Staff & Tellers</div>
      </a>
    </li>

    <li class="menu-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
      <a href="{{ route('admin.roles.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-key text-warning"></i>
        <div>Roles & Permissions</div>
        <span class="badge bg-label-warning ms-auto" style="font-size: 9px;">RBAC</span>
      </a>
    </li>

    <!-- Banking Services & Priority Policy -->
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Queuing Policy</span>
    </li>

    <li class="menu-item {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
      <a href="{{ route('admin.services.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-list-check"></i>
        <div>Service Categories</div>
      </a>
    </li>

    <li class="menu-item {{ request()->routeIs('admin.priorities.*') ? 'active' : '' }}">
      <a href="{{ route('admin.priorities.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-shield-quarter text-danger"></i>
        <div>Priority Tiers (SBP/SBI)</div>
        <span class="badge bg-label-danger ms-auto" style="font-size: 9px;">Policy</span>
      </a>
    </li>

    <!-- Analytics & Compliance -->
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Audit & Compliance</span>
    </li>

    <li class="menu-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
      <a href="{{ route('admin.reports.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
        <div>Compliance & SLA Reports</div>
      </a>
    </li>

    <li class="menu-item {{ request()->routeIs('admin.customers.history') ? 'active' : '' }}">
      <a href="{{ route('admin.customers.history') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-history"></i>
        <div>Customer Lifetime Audit</div>
      </a>
    </li>
  </ul>
</aside>
