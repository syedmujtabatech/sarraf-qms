# Sarraf-QMS (صراف) — National Banking Queue & Operations System
### Designed in Compliance with State Bank of Pakistan (SBP) & State Bank of India (SBI) Standards

**Sarraf-QMS (صراف کیو ایم ایس)** is a comprehensive, enterprise-grade Electronic Queue Management and Branch Operations System designed specifically for commercial and central banks. Built on **Laravel 12 (PHP 8.2)** with a fully sliced **Bootstrap 5 (Sneat Admin Dashboard)**, it mirrors the exact real-world organizational hierarchy, teller desk workflows, and regulatory compliance requirements of actual banks.

---

## 🏛 Real Banking Organizational Structure (RBAC)

In real banking operations, responsibilities are strictly separated between headquarters, regional clusters, and branch personnel. **Sarraf-QMS** introduces a complete **Role-Based Access Control (RBAC)** matrix:

1. **Group Head of Banking Operations (`super_admin`)**: Complete national oversight, system parameters, regulatory audit policies, and security delegation.
2. **Regional Operations Manager - ROM (`regional_manager`)**: Manages multi-branch regional clusters (e.g., South Region - Sindh, Central Region - Punjab, North Region - Islamabad/KPK), tracks inter-branch SLA benchmarks.
3. **Branch Operations Manager - BOM / BM (`branch_manager`)**: Oversees branch floor operations, customer flow escalations, staff reassignments, and emergency priority overrides.
4. **Head Cashier / Chief Teller (`chief_teller`)**: Manages cash vault drops, cash drawer reconciliations, teller breaks, and counter reallocations.
5. **Universal Teller / Cashier (`teller`)**: Counter teller handling cash deposits, cheques, over-the-counter encashments, and fast token processing.
6. **Customer Services Officer - CSO (`cso`)**: Desk officer handling account openings, biometric re-verification, and complaints.
7. **SBP Compliance & Inspection Officer (`sbp_auditor`)**: Regulatory auditor tracking SBP Circular 01/2018 (Pensioners & PWDs priority fulfilment) and inspection audit trails.
8. **Floor Greeter / Accessibility Host (`greeter`)**: Front-desk greeter assisting senior citizens and persons with disabilities at the self-service ticket dispenser kiosk.

---

## 🏦 Real Banking Operational Capabilities Added

* **Regional Operational Clusters (`/admin/regions`):** Group branches into geographic regions with dedicated regional heads, contact coordinates, and aggregate SLA tracking.
* **Roles & Granular Permissions Matrix (`/admin/roles`):** Modular permission management across 5 modules (*Dashboard & Monitoring*, *Teller & Counter Operations*, *Branch Infrastructure*, *Compliance & Audit*, *Security & Role Management*).
* **Staff Directory & Counter Binding (`/admin/users`):** Bind tellers to physical counter hardware stations, assign roles and branch locations.
* **Cash Drawer & Vault Balancing:** Tellers can track physical cash in their counter drawer with instant reconciliation after vault drops.
* **Structured Teller Breaks:** Tellers can log structured pauses (*Lunch Break, Prayer Break, Cash Reconciliation / Vault Drop, Tea Break*) with visual pause indicators on the dashboard and waiting hall screens.
* **Manager Priority Override & Emergency Token Injection:** Branch Managers, Regional Heads, and Chief Tellers can inject VIP or emergency tokens with maximum precedence (Score: 999) to immediately bypass regular queues.
* **Real-time Mobile E-Pass with Live Alerts (`/m/{token_number}`):** Customers scan the slip QR code from their mobile phone (via Wi-Fi `http://192.168.100.9:8080/m/...`) to track real-time queue position with auto-vibration and sound alert when summoned to a counter.
* **Waiting Hall Split TV Screen (`/display`):** With bilingual English & Urdu voice announcements and airport chime.

---

## 🚀 Live Access URLs (Port 8080)

- **Authentication & Fast Role Switcher:** [http://127.0.0.1:8080/login](http://127.0.0.1:8080/login)
- **Central Operations Cockpit:** [http://127.0.0.1:8080/admin/dashboard](http://127.0.0.1:8080/admin/dashboard)
- **Teller Operator Workbench:** [http://127.0.0.1:8080/teller](http://127.0.0.1:8080/teller)
- **Roles & Permissions (RBAC):** [http://127.0.0.1:8080/admin/roles](http://127.0.0.1:8080/admin/roles)
- **Staff Directory & Counter Setup:** [http://127.0.0.1:8080/admin/users](http://127.0.0.1:8080/admin/users)
- **Regional Clusters:** [http://127.0.0.1:8080/admin/regions](http://127.0.0.1:8080/admin/regions)
- **Self-Service Touchscreen Kiosk:** [http://127.0.0.1:8080/kiosk](http://127.0.0.1:8080/kiosk)
- **Lobby TV Display & Audio Screen:** [http://127.0.0.1:8080/display](http://127.0.0.1:8080/display)
- **Online / Mobile Appointment Booking:** [http://127.0.0.1:8080/appointment](http://127.0.0.1:8080/appointment)
- **Customer Feedback Rating Terminal:** [http://127.0.0.1:8080/feedback](http://127.0.0.1:8080/feedback)
- **Compliance & SLA Reports:** [http://127.0.0.1:8080/admin/reports](http://127.0.0.1:8080/admin/reports)
- **Customer Lifetime History Audit:** [http://127.0.0.1:8080/admin/customers/history](http://127.0.0.1:8080/admin/customers/history)

---

## 🔑 Demo Staff Accounts (1-Click Switch on Login Page)

| Role | Name | Email | Password | Quick Link |
|---|---|---|---|---|
| **Group Head of Banking Ops** | Syed Zafar Iqbal | `admin@sarraf.com` | `admin123` | [1-Click Head Access](http://127.0.0.1:8080/login/quick/1) |
| **Regional Operations Manager** | Kamran Haider | `rom@sarraf.com` | `rom123` | [1-Click ROM Access](http://127.0.0.1:8080/login/quick/2) |
| **Branch Operations Manager** | Tariq Mansoor | `bm@sarraf.com` | `bm123` | [1-Click BM Access](http://127.0.0.1:8080/login/quick/3) |
| **Head Cashier / Chief Teller** | Shafqat Mehmood | `chiefteller@sarraf.com` | `chief123` | [1-Click Chief Teller](http://127.0.0.1:8080/login/quick/4) |
| **Cashier (Counter 02 - Cash)** | Farhan Qureshi | `cashier@sarraf.com` | `cashier123` | [1-Click Cashier](http://127.0.0.1:8080/login/quick/5) |
| **Customer Services Officer** | Zainab Bibi | `cso@sarraf.com` | `cso123` | [1-Click CSO](http://127.0.0.1:8080/login/quick/6) |
| **SBP Compliance Auditor** | M. Arshad | `auditor@sarraf.com` | `auditor123` | [1-Click SBP Auditor](http://127.0.0.1:8080/login/quick/7) |
| **Floor Greeter / Host** | Bilal Ahmed | `greeter@sarraf.com` | `greeter123` | [1-Click Greeter](http://127.0.0.1:8080/login/quick/8) |
