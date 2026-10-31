<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Counter;
use App\Models\CustomerFeedback;
use App\Models\Permission;
use App\Models\PriorityCategory;
use App\Models\Region;
use App\Models\Role;
use App\Models\ServiceCategory;
use App\Models\Token;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Regional Banking Clusters
        $southRegion = Region::create([
            'name' => 'South Region (Sindh & Coastal)',
            'code' => 'REG-SOUTH',
            'city_headquarters' => 'Karachi',
            'regional_manager_name' => 'Kamran Haider (Regional Head)',
            'contact_email' => 'regional.south@sarraf-qms.com',
            'contact_phone' => '+92-21-3241-0001',
            'is_active' => true,
        ]);

        $centralRegion = Region::create([
            'name' => 'Central Region (Punjab)',
            'code' => 'REG-CENTRAL',
            'city_headquarters' => 'Lahore',
            'regional_manager_name' => 'Malik Jahangir (Regional Head)',
            'contact_email' => 'regional.central@sarraf-qms.com',
            'contact_phone' => '+92-42-3571-0002',
            'is_active' => true,
        ]);

        $northRegion = Region::create([
            'name' => 'North Region (Federal & KPK)',
            'code' => 'REG-NORTH',
            'city_headquarters' => 'Islamabad',
            'regional_manager_name' => 'Brig (R) Asim Bilal (Regional Head)',
            'contact_email' => 'regional.north@sarraf-qms.com',
            'contact_phone' => '+92-51-2821-0003',
            'is_active' => true,
        ]);

        // 2. Real Banking Roles
        $roleSuperAdmin = Role::create([
            'name' => 'super_admin',
            'display_name' => 'Group Head of Banking Operations',
            'badge_color' => 'danger',
            'description' => 'Full national access across all regions, system security, role delegations and SBP audit policies.',
            'is_system' => true,
        ]);

        $roleRegManager = Role::create([
            'name' => 'regional_manager',
            'display_name' => 'Regional Operations Manager (ROM)',
            'badge_color' => 'primary',
            'description' => 'Oversees cluster of bank branches, regional SLA performance, branch manager approvals.',
            'is_system' => true,
        ]);

        $roleBranchManager = Role::create([
            'name' => 'branch_manager',
            'display_name' => 'Branch Operations Manager (BOM)',
            'badge_color' => 'info',
            'description' => 'Direct branch in-charge, counter allocations, priority overrides and escalated queue resolution.',
            'is_system' => true,
        ]);

        $roleChiefTeller = Role::create([
            'name' => 'chief_teller',
            'display_name' => 'Head Cashier / Chief Teller',
            'badge_color' => 'warning',
            'description' => 'Supervises cash drawer reconciliations, vault drops, counter queues and cash overrides.',
            'is_system' => true,
        ]);

        $roleTeller = Role::create([
            'name' => 'teller',
            'display_name' => 'Universal Teller / Cashier',
            'badge_color' => 'success',
            'description' => 'Executes counter cash deposits, withdrawals, cheques and token completion.',
            'is_system' => true,
        ]);

        $roleCSO = Role::create([
            'name' => 'cso',
            'display_name' => 'Customer Services Officer (CSO)',
            'badge_color' => 'secondary',
            'description' => 'Manages account openings, biometric KYC, customer inquiries and grievances.',
            'is_system' => true,
        ]);

        $roleSBPAuditor = Role::create([
            'name' => 'sbp_auditor',
            'display_name' => 'SBP Compliance & Inspection Auditor',
            'badge_color' => 'dark',
            'description' => 'State Bank inspection officer monitoring PWD accessibility, pensioner SLAs and audit trails.',
            'is_system' => true,
        ]);

        $roleGreeter = Role::create([
            'name' => 'greeter',
            'display_name' => 'Floor Greeter / Kiosk Attendant',
            'badge_color' => 'secondary',
            'description' => 'Assists walk-in visitors, guides senior citizens & PWDs at self-service ticket dispenser.',
            'is_system' => true,
        ]);

        // 3. Modular Permissions
        $permissions = [
            // Dashboard
            ['name' => 'view_executive_dashboard', 'display_name' => 'View Executive Operations Cockpit', 'module' => 'Dashboard & Monitoring'],
            ['name' => 'view_regional_overview', 'display_name' => 'View Multi-Branch Regional Cluster Overview', 'module' => 'Dashboard & Monitoring'],
            ['name' => 'view_counter_wallboard', 'display_name' => 'View Real-time Counter Wallboard', 'module' => 'Dashboard & Monitoring'],

            // Queue Operations
            ['name' => 'teller_call_tokens', 'display_name' => 'Call & Dispatch Queue Tokens', 'module' => 'Teller & Counter Operations'],
            ['name' => 'teller_complete_tokens', 'display_name' => 'Complete Transactions & Log Amounts', 'module' => 'Teller & Counter Operations'],
            ['name' => 'teller_hold_transfer', 'display_name' => 'Put on Hold & Transfer Customers', 'module' => 'Teller & Counter Operations'],
            ['name' => 'teller_take_break', 'display_name' => 'Pause Counter & Log Structured Break', 'module' => 'Teller & Counter Operations'],
            ['name' => 'manager_override_priority', 'display_name' => 'Manager Override & Emergency Token Injection', 'module' => 'Teller & Counter Operations'],

            // Branch Setup
            ['name' => 'manage_regions', 'display_name' => 'Configure Bank Regions & Clusters', 'module' => 'Branch Infrastructure'],
            ['name' => 'manage_branches', 'display_name' => 'Create & Edit Bank Branches', 'module' => 'Branch Infrastructure'],
            ['name' => 'manage_counters', 'display_name' => 'Configure Teller Stations & Service Routing', 'module' => 'Branch Infrastructure'],
            ['name' => 'manage_services', 'display_name' => 'Manage Banking Services & SLA Timings', 'module' => 'Branch Infrastructure'],
            ['name' => 'manage_priorities', 'display_name' => 'Configure SBP & SBI Customer Priority Tiers', 'module' => 'Branch Infrastructure'],

            // Compliance & Audit
            ['name' => 'view_sbp_reports', 'display_name' => 'View SBP Compliance & SLA Audit Reports', 'module' => 'Compliance & Audit'],
            ['name' => 'export_audit_csv', 'display_name' => 'Export Full Data CSV & Audit Logs', 'module' => 'Compliance & Audit'],
            ['name' => 'view_customer_history', 'display_name' => 'Search & Audit Customer Lifetime Service History', 'module' => 'Compliance & Audit'],
            ['name' => 'view_teller_productivity', 'display_name' => 'View Staff & Teller Efficiency Analytics', 'module' => 'Compliance & Audit'],

            // RBAC & Security
            ['name' => 'manage_users', 'display_name' => 'Manage Banking Staff Accounts & Counter Binding', 'module' => 'Security & Role Management'],
            ['name' => 'manage_roles_permissions', 'display_name' => 'Manage Roles & Delegate Granular Permissions', 'module' => 'Security & Role Management'],
        ];

        $permModels = [];
        foreach ($permissions as $p) {
            $permModels[$p['name']] = Permission::create($p);
        }

        // Attach permissions to roles
        // Super Admin gets all
        $roleSuperAdmin->permissions()->sync(array_values(array_map(fn($pm) => $pm->id, $permModels)));

        // Regional Manager
        $roleRegManager->permissions()->sync([
            $permModels['view_executive_dashboard']->id,
            $permModels['view_regional_overview']->id,
            $permModels['view_counter_wallboard']->id,
            $permModels['view_sbp_reports']->id,
            $permModels['export_audit_csv']->id,
            $permModels['view_customer_history']->id,
            $permModels['view_teller_productivity']->id,
        ]);

        // Branch Manager
        $roleBranchManager->permissions()->sync([
            $permModels['view_executive_dashboard']->id,
            $permModels['view_counter_wallboard']->id,
            $permModels['manager_override_priority']->id,
            $permModels['manage_counters']->id,
            $permModels['view_sbp_reports']->id,
            $permModels['export_audit_csv']->id,
            $permModels['view_customer_history']->id,
            $permModels['view_teller_productivity']->id,
            $permModels['manage_users']->id,
        ]);

        // Chief Teller
        $roleChiefTeller->permissions()->sync([
            $permModels['view_executive_dashboard']->id,
            $permModels['view_counter_wallboard']->id,
            $permModels['teller_call_tokens']->id,
            $permModels['teller_complete_tokens']->id,
            $permModels['teller_hold_transfer']->id,
            $permModels['teller_take_break']->id,
            $permModels['manager_override_priority']->id,
            $permModels['view_customer_history']->id,
        ]);

        // Teller
        $roleTeller->permissions()->sync([
            $permModels['teller_call_tokens']->id,
            $permModels['teller_complete_tokens']->id,
            $permModels['teller_hold_transfer']->id,
            $permModels['teller_take_break']->id,
        ]);

        // CSO
        $roleCSO->permissions()->sync([
            $permModels['teller_call_tokens']->id,
            $permModels['teller_complete_tokens']->id,
            $permModels['teller_hold_transfer']->id,
            $permModels['teller_take_break']->id,
            $permModels['view_customer_history']->id,
        ]);

        // SBP Auditor
        $roleSBPAuditor->permissions()->sync([
            $permModels['view_executive_dashboard']->id,
            $permModels['view_sbp_reports']->id,
            $permModels['export_audit_csv']->id,
            $permModels['view_customer_history']->id,
        ]);

        // Greeter
        $roleGreeter->permissions()->sync([
            $permModels['view_counter_wallboard']->id,
        ]);

        // 4. Branches linked to Regions
        $mainBranch = Branch::create([
            'region_id' => $southRegion->id,
            'name' => 'Sarraf Tower Main Flagship Branch',
            'code' => 'SRF-001',
            'city' => 'Karachi',
            'address' => 'I.I. Chundrigar Road, Banking District, Karachi',
            'phone' => '+92-21-111-727-111',
            'email' => 'operations.khi@sarraf-qms.com',
            'open_time' => '09:00:00',
            'close_time' => '17:00:00',
            'is_active' => true,
        ]);

        $lahoreBranch = Branch::create([
            'region_id' => $centralRegion->id,
            'name' => 'Mall Road Commercial Branch',
            'code' => 'SRF-002',
            'city' => 'Lahore',
            'address' => 'Mall Road, Commercial Zone, Lahore',
            'phone' => '+92-42-111-727-222',
            'email' => 'operations.lhr@sarraf-qms.com',
            'open_time' => '09:00:00',
            'close_time' => '17:00:00',
            'is_active' => true,
        ]);

        $isbBranch = Branch::create([
            'region_id' => $northRegion->id,
            'name' => 'Blue Area Corporate Branch',
            'code' => 'SRF-003',
            'city' => 'Islamabad',
            'address' => 'Jinnah Avenue, Blue Area, Islamabad',
            'phone' => '+92-51-111-727-333',
            'email' => 'operations.isb@sarraf-qms.com',
            'open_time' => '09:00:00',
            'close_time' => '17:00:00',
            'is_active' => true,
        ]);

        // 5. Priority Categories (SBP & SBI Compliance)
        $priorityPWD = PriorityCategory::create([
            'name' => 'Special Persons / PWD',
            'name_ur' => 'معذور اور خصوصی افراد',
            'code' => 'PWD',
            'weight_multiplier' => 5,
            'badge_color' => 'danger',
            'icon' => 'bx-accessibility',
            'regulatory_note' => 'SBP Policy for Inclusion of Persons with Disabilities 2021 (Fast-Track Precedence)',
            'is_active' => true,
        ]);

        $priorityPensioner = PriorityCategory::create([
            'name' => 'Pensioners & EOBI',
            'name_ur' => 'پنشنرز اور بزرگ پنشن ہولڈرز',
            'code' => 'PENSIONER',
            'weight_multiplier' => 5,
            'badge_color' => 'warning',
            'icon' => 'bx-shield-quarter',
            'regulatory_note' => 'SBP BC-CPD Circular Letter No. 01/2018 (Pensioner Dedicated Priority)',
            'is_active' => true,
        ]);

        $prioritySenior = PriorityCategory::create([
            'name' => 'Senior Citizens (60+)',
            'name_ur' => 'سینئر شہری (60 سال یا زائد)',
            'code' => 'SENIOR',
            'weight_multiplier' => 4,
            'badge_color' => 'info',
            'icon' => 'bx-user-voice',
            'regulatory_note' => 'SBP CPD Circular No. 2/2014 & SBI Queue Tender Priority Guidelines',
            'is_active' => true,
        ]);

        $priorityVIP = PriorityCategory::create([
            'name' => 'Bank Approved VIP / Priority Banking',
            'name_ur' => 'ترجیحی بینکنگ کسٹمر',
            'code' => 'VIP',
            'weight_multiplier' => 3,
            'badge_color' => 'primary',
            'icon' => 'bx-crown',
            'regulatory_note' => 'SBI Tender approved commercial priority criteria',
            'is_active' => true,
        ]);

        $priorityPregnant = PriorityCategory::create([
            'name' => 'Expectant & Nursing Mothers',
            'name_ur' => 'خواتین ترجیحی سروس',
            'code' => 'PREGNANT',
            'weight_multiplier' => 3,
            'badge_color' => 'secondary',
            'icon' => 'bx-heart',
            'regulatory_note' => 'Priority banking courtesy assistance',
            'is_active' => true,
        ]);

        $priorityRegular = PriorityCategory::create([
            'name' => 'Standard Walk-in Customer',
            'name_ur' => 'عام بینکاری کسٹمر',
            'code' => 'REGULAR',
            'weight_multiplier' => 1,
            'badge_color' => 'secondary',
            'icon' => 'bx-user',
            'regulatory_note' => 'Standard FIFO Queue Order',
            'is_active' => true,
        ]);

        // 6. Service Categories
        $srvCashDeposit = ServiceCategory::create([
            'name' => 'Cash Deposit & Cheques',
            'name_ur' => 'کیش جمع اور کلیئرنگ چیک',
            'prefix' => 'C',
            'description' => 'Cash deposits, pay orders, banker cheques, clearing deposits',
            'avg_service_time_mins' => 6,
            'icon' => 'bx-money-withdraw',
            'color' => 'success',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $srvCashWithdraw = ServiceCategory::create([
            'name' => 'Cash Withdrawal',
            'name_ur' => 'کیش نکلوانا',
            'prefix' => 'W',
            'description' => 'Over the counter cheque encashment and withdrawal slips',
            'avg_service_time_mins' => 5,
            'icon' => 'bx-wallet',
            'color' => 'primary',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $srvRemittance = ServiceCategory::create([
            'name' => 'Foreign Remittance & Forex',
            'name_ur' => 'فارن ترسیلات اور غیر ملکی کرنسی',
            'prefix' => 'R',
            'description' => 'Western Union, MoneyGram, Home Remittance PRI, Currency Exchange',
            'avg_service_time_mins' => 8,
            'icon' => 'bx-globe',
            'color' => 'info',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $srvAccountOpening = ServiceCategory::create([
            'name' => 'Account Opening & KYC / Biometric',
            'name_ur' => 'نیا اکاؤنٹ اور بائیو میٹرک ویریفکیشن',
            'prefix' => 'A',
            'description' => 'Current/Savings account, Asaan account, biometric re-verification',
            'avg_service_time_mins' => 15,
            'icon' => 'bx-user-plus',
            'color' => 'warning',
            'is_active' => true,
            'sort_order' => 4,
        ]);

        $srvPension = ServiceCategory::create([
            'name' => 'Pension & Social Welfare Disbursements',
            'name_ur' => 'پنشن اور سرکاری ادائیگیاں',
            'prefix' => 'P',
            'description' => 'Government pensioners, EOBI pensions, BISP disbursement vouchers',
            'avg_service_time_mins' => 7,
            'icon' => 'bx-donate-heart',
            'color' => 'danger',
            'is_active' => true,
            'sort_order' => 5,
        ]);

        $srvFinancing = ServiceCategory::create([
            'name' => 'Financing & Consumer Loans',
            'name_ur' => 'بینک لون اور فنانسنگ سروسز',
            'prefix' => 'L',
            'description' => 'Auto loan, home finance, SME loan facilities, credit cards',
            'avg_service_time_mins' => 20,
            'icon' => 'bx-line-chart',
            'color' => 'success',
            'is_active' => true,
            'sort_order' => 6,
        ]);

        $srvInquiries = ServiceCategory::create([
            'name' => 'Customer Support & Inquiries',
            'name_ur' => 'کسٹمر سپورٹ اور شکایات ڈیسک',
            'prefix' => 'S',
            'description' => 'Account statements, ATM card dispute, mobile banking setup, complaints',
            'avg_service_time_mins' => 8,
            'icon' => 'bx-headphone',
            'color' => 'secondary',
            'is_active' => true,
            'sort_order' => 7,
        ]);

        // 7. Counters with Real Cash Drawer Balances & Break status
        $cnt1 = Counter::create([
            'branch_id' => $mainBranch->id,
            'counter_number' => '01',
            'name' => 'Counter 01 - Priority & SBP Special Desk',
            'type' => 'priority_dedicated',
            'status' => 'online',
            'opening_cash_balance' => 500000.00,
            'current_cash_drawer' => 425500.00,
        ]);
        $cnt1->services()->attach([$srvPension->id, $srvCashWithdraw->id, $srvCashDeposit->id, $srvInquiries->id]);

        $cnt2 = Counter::create([
            'branch_id' => $mainBranch->id,
            'counter_number' => '02',
            'name' => 'Counter 02 - Cash Deposit & Cheques',
            'type' => 'cash',
            'status' => 'online',
            'opening_cash_balance' => 1000000.00,
            'current_cash_drawer' => 1250000.00,
        ]);
        $cnt2->services()->attach([$srvCashDeposit->id]);

        $cnt3 = Counter::create([
            'branch_id' => $mainBranch->id,
            'counter_number' => '03',
            'name' => 'Counter 03 - Cash Withdrawal & Remittances',
            'type' => 'cash',
            'status' => 'online',
            'opening_cash_balance' => 2000000.00,
            'current_cash_drawer' => 1650000.00,
        ]);
        $cnt3->services()->attach([$srvCashWithdraw->id, $srvRemittance->id]);

        $cnt4 = Counter::create([
            'branch_id' => $mainBranch->id,
            'counter_number' => '04',
            'name' => 'Counter 04 - Account Opening & KYC',
            'type' => 'general',
            'status' => 'online',
            'opening_cash_balance' => 0.00,
            'current_cash_drawer' => 0.00,
        ]);
        $cnt4->services()->attach([$srvAccountOpening->id]);

        $cnt5 = Counter::create([
            'branch_id' => $mainBranch->id,
            'counter_number' => '05',
            'name' => 'Counter 05 - Customer Support & Grievance Desk',
            'type' => 'inquiries',
            'status' => 'online',
            'opening_cash_balance' => 0.00,
            'current_cash_drawer' => 0.00,
        ]);
        $cnt5->services()->attach([$srvInquiries->id, $srvFinancing->id]);

        // 8. Staff Users with Real Roles & Regional Hierarchy
        $superAdmin = User::create([
            'name' => 'Syed Zafar Iqbal (Head of Banking Operations)',
            'email' => 'admin@sarraf.com',
            'password' => Hash::make('admin123'),
            'role' => 'super_admin',
            'role_id' => $roleSuperAdmin->id,
            'branch_id' => $mainBranch->id,
            'region_id' => $southRegion->id,
            'is_active' => true,
        ]);

        $regManager = User::create([
            'name' => 'Kamran Haider (Regional Operations Manager - South)',
            'email' => 'rom@sarraf.com',
            'password' => Hash::make('rom123'),
            'role' => 'regional_manager',
            'role_id' => $roleRegManager->id,
            'branch_id' => $mainBranch->id,
            'region_id' => $southRegion->id,
            'is_active' => true,
        ]);

        $branchManager = User::create([
            'name' => 'Tariq Mansoor (Branch Operations Manager)',
            'email' => 'bm@sarraf.com',
            'password' => Hash::make('bm123'),
            'role' => 'branch_manager',
            'role_id' => $roleBranchManager->id,
            'branch_id' => $mainBranch->id,
            'region_id' => $southRegion->id,
            'is_active' => true,
        ]);

        $chiefTeller = User::create([
            'name' => 'Shafqat Mehmood (Head Cashier / Chief Teller)',
            'email' => 'chiefteller@sarraf.com',
            'password' => Hash::make('chief123'),
            'role' => 'chief_teller',
            'role_id' => $roleChiefTeller->id,
            'branch_id' => $mainBranch->id,
            'assigned_counter_id' => $cnt1->id,
            'is_active' => true,
        ]);
        $cnt1->update(['current_user_id' => $chiefTeller->id]);

        $cashier = User::create([
            'name' => 'Farhan Qureshi (Cashier - Counter 2)',
            'email' => 'cashier@sarraf.com',
            'password' => Hash::make('cashier123'),
            'role' => 'teller',
            'role_id' => $roleTeller->id,
            'branch_id' => $mainBranch->id,
            'assigned_counter_id' => $cnt2->id,
            'is_active' => true,
        ]);
        $cnt2->update(['current_user_id' => $cashier->id]);

        $cso = User::create([
            'name' => 'Zainab Bibi (Customer Services Officer)',
            'email' => 'cso@sarraf.com',
            'password' => Hash::make('cso123'),
            'role' => 'cso',
            'role_id' => $roleCSO->id,
            'branch_id' => $mainBranch->id,
            'assigned_counter_id' => $cnt4->id,
            'is_active' => true,
        ]);
        $cnt4->update(['current_user_id' => $cso->id]);

        $sbpAuditor = User::create([
            'name' => 'M. Arshad (SBP Regulatory Inspection Officer)',
            'email' => 'auditor@sarraf.com',
            'password' => Hash::make('auditor123'),
            'role' => 'sbp_auditor',
            'role_id' => $roleSBPAuditor->id,
            'branch_id' => $mainBranch->id,
            'region_id' => $southRegion->id,
            'is_active' => true,
        ]);

        $greeter = User::create([
            'name' => 'Bilal Ahmed (Floor Greeter & Accessibility Host)',
            'email' => 'greeter@sarraf.com',
            'password' => Hash::make('greeter123'),
            'role' => 'greeter',
            'role_id' => $roleGreeter->id,
            'branch_id' => $mainBranch->id,
            'is_active' => true,
        ]);

        // 9. Initial Tokens
        $comp1 = Token::create([
            'token_number' => 'C-101',
            'sequence_number' => 101,
            'branch_id' => $mainBranch->id,
            'service_category_id' => $srvCashDeposit->id,
            'priority_category_id' => $priorityRegular->id,
            'counter_id' => $cnt2->id,
            'served_by_user_id' => $cashier->id,
            'customer_name' => 'Muhammad Rashid',
            'customer_phone' => '03001234567',
            'customer_cnic' => '42101-1234567-1',
            'channel' => 'kiosk',
            'status' => 'completed',
            'priority_score' => 100,
            'issued_at' => Carbon::now()->subMinutes(45),
            'called_at' => Carbon::now()->subMinutes(35),
            'serving_started_at' => Carbon::now()->subMinutes(35),
            'completed_at' => Carbon::now()->subMinutes(28),
            'waiting_time_seconds' => 600,
            'serving_time_seconds' => 420,
            'transaction_amount' => 150000.00,
            'transaction_reference' => 'TXN-DEP-88491',
            'notes' => 'Commercial cash deposit in business account.',
        ]);

        CustomerFeedback::create([
            'token_id' => $comp1->id,
            'branch_id' => $mainBranch->id,
            'counter_id' => $cnt2->id,
            'rating' => 5,
            'feedback_tags' => ['Quick Service', 'Courteous Staff', 'Accurate Handling'],
            'comments' => 'Very fast cash counting machine and polite teller.',
            'customer_phone' => '03001234567',
        ]);

        $comp2 = Token::create([
            'token_number' => 'P-101',
            'sequence_number' => 101,
            'branch_id' => $mainBranch->id,
            'service_category_id' => $srvPension->id,
            'priority_category_id' => $priorityPensioner->id,
            'counter_id' => $cnt1->id,
            'served_by_user_id' => $chiefTeller->id,
            'customer_name' => 'Col (Retd) Abdul Ghaffar',
            'customer_phone' => '03217654321',
            'customer_cnic' => '37405-9876543-1',
            'channel' => 'kiosk',
            'status' => 'completed',
            'priority_score' => 500,
            'issued_at' => Carbon::now()->subMinutes(30),
            'called_at' => Carbon::now()->subMinutes(25),
            'serving_started_at' => Carbon::now()->subMinutes(25),
            'completed_at' => Carbon::now()->subMinutes(18),
            'waiting_time_seconds' => 300,
            'serving_time_seconds' => 420,
            'transaction_amount' => 74500.00,
            'transaction_reference' => 'TXN-PEN-90123',
            'notes' => 'SBP Circular 01/2018 priority pension disbursal with biometric life certificate verified.',
        ]);

        // Tokens Serving
        $serv1 = Token::create([
            'token_number' => 'P-102',
            'sequence_number' => 102,
            'branch_id' => $mainBranch->id,
            'service_category_id' => $srvPension->id,
            'priority_category_id' => $priorityPWD->id,
            'counter_id' => $cnt1->id,
            'served_by_user_id' => $chiefTeller->id,
            'customer_name' => 'Mirza Asad (Wheelchair Assisted)',
            'customer_phone' => '03335551234',
            'customer_cnic' => '42201-5551234-9',
            'channel' => 'kiosk',
            'status' => 'serving',
            'priority_score' => 500,
            'issued_at' => Carbon::now()->subMinutes(12),
            'called_at' => Carbon::now()->subMinutes(6),
            'serving_started_at' => Carbon::now()->subMinutes(6),
            'waiting_time_seconds' => 360,
            'notes' => 'Wheelchair ramp assistance provided as per SBP PWD Financial Inclusion Policy.',
        ]);
        $cnt1->update(['current_token_id' => $serv1->id, 'status' => 'serving']);

        $serv2 = Token::create([
            'token_number' => 'C-102',
            'sequence_number' => 102,
            'branch_id' => $mainBranch->id,
            'service_category_id' => $srvCashDeposit->id,
            'priority_category_id' => $priorityRegular->id,
            'counter_id' => $cnt2->id,
            'served_by_user_id' => $cashier->id,
            'customer_name' => 'Hamza Tariq',
            'customer_phone' => '03124445566',
            'customer_cnic' => '42301-4445566-3',
            'channel' => 'kiosk',
            'status' => 'serving',
            'priority_score' => 100,
            'issued_at' => Carbon::now()->subMinutes(10),
            'called_at' => Carbon::now()->subMinutes(4),
            'serving_started_at' => Carbon::now()->subMinutes(4),
            'waiting_time_seconds' => 360,
        ]);
        $cnt2->update(['current_token_id' => $serv2->id, 'status' => 'serving']);

        // Waiting Tokens
        Token::create([
            'token_number' => 'A-101',
            'sequence_number' => 101,
            'branch_id' => $mainBranch->id,
            'service_category_id' => $srvAccountOpening->id,
            'priority_category_id' => $prioritySenior->id,
            'customer_name' => 'Haji Nisar Ahmed',
            'customer_phone' => '03019876543',
            'customer_cnic' => '42101-9876543-5',
            'channel' => 'kiosk',
            'status' => 'waiting',
            'priority_score' => 400,
            'issued_at' => Carbon::now()->subMinutes(8),
        ]);

        Token::create([
            'token_number' => 'W-101',
            'sequence_number' => 101,
            'branch_id' => $mainBranch->id,
            'service_category_id' => $srvCashWithdraw->id,
            'priority_category_id' => $priorityVIP->id,
            'customer_name' => 'Syed Kamran Shah (Corporate VIP)',
            'customer_phone' => '03009998877',
            'customer_cnic' => '42101-9998877-1',
            'channel' => 'appointment',
            'status' => 'waiting',
            'priority_score' => 300,
            'issued_at' => Carbon::now()->subMinutes(7),
        ]);
    }
}
