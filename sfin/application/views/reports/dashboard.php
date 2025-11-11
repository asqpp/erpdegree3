<?php $this->load->view('components/ui_components'); ?>

<!-- Reports Dashboard -->
<div class="mb-6" data-aos="fade-down">
    <h1 class="page-title">Reports & Analytics</h1>
    <p class="text-gray-600 mt-1">Comprehensive reporting system with 60+ reports</p>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="stat-card bg-gradient-primary" data-aos="fade-up">
        <div class="flex items-start justify-between">
            <div>
                <p class="stat-label">Total Reports</p>
                <h3 class="stat-value">60+</h3>
                <p class="text-sm mt-1">Across 6 categories</p>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                <i class="fas fa-chart-bar text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="stat-card bg-gradient-success" data-aos="fade-up" data-aos-delay="100">
        <div class="flex items-start justify-between">
            <div>
                <p class="stat-label">Financial Reports</p>
                <h3 class="stat-value">15+</h3>
                <p class="text-sm mt-1">P&L, Balance Sheet, VAT</p>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                <i class="fas fa-dollar-sign text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="stat-card bg-gradient-warning" data-aos="fade-up" data-aos-delay="200">
        <div class="flex items-start justify-between">
            <div>
                <p class="stat-label">Insurance Reports</p>
                <h3 class="stat-value">20+</h3>
                <p class="text-sm mt-1">Policies, Claims, Premium</p>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                <i class="fas fa-shield-alt text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="stat-card bg-gradient-info" data-aos="fade-up" data-aos-delay="300">
        <div class="flex items-start justify-between">
            <div>
                <p class="stat-label">Compliance Reports</p>
                <h3 class="stat-value">15+</h3>
                <p class="text-sm mt-1">VAT, IA, Audit</p>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                <i class="fas fa-balance-scale text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Report Categories -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- 1. Financial Reports -->
    <?php card_start('💰 Financial Reports', '', true); ?>
        <div class="space-y-3">
            <?php
            $financial_reports = [
                ['name' => 'Profit & Loss Statement', 'url' => 'accounting/profit_loss', 'icon' => 'chart-line', 'desc' => 'Income vs Expenses'],
                ['name' => 'Balance Sheet', 'url' => 'accounting/balance_sheet', 'icon' => 'balance-scale', 'desc' => 'Assets, Liabilities, Equity'],
                ['name' => 'Cash Flow Statement', 'url' => 'reports/cash_flow', 'icon' => 'money-bill-wave', 'desc' => 'Cash inflows and outflows'],
                ['name' => 'Trial Balance', 'url' => 'reports/trial_balance', 'icon' => 'calculator', 'desc' => 'Ledger balance summary'],
                ['name' => 'General Ledger', 'url' => 'reports/general_ledger', 'icon' => 'book', 'desc' => 'Complete transaction history'],
                ['name' => 'Revenue Report', 'url' => 'reports/financial?type=revenue', 'icon' => 'chart-pie', 'desc' => 'Revenue breakdown by source'],
                ['name' => 'Expense Report', 'url' => 'reports/financial?type=expense', 'icon' => 'receipt', 'desc' => 'Expense analysis'],
                ['name' => 'Premium Collection Report', 'url' => 'reports/premium_collection', 'icon' => 'coins', 'desc' => 'Premium received vs pending'],
                ['name' => 'Commission Report', 'url' => 'sales/commissions', 'icon' => 'percentage', 'desc' => 'Agent/broker commissions'],
                ['name' => 'Outstanding AR Report', 'url' => 'accounting/accounts_receivable', 'icon' => 'file-invoice-dollar', 'desc' => 'Pending receivables'],
                ['name' => 'Outstanding AP Report', 'url' => 'accounting/accounts_payable', 'icon' => 'file-invoice', 'desc' => 'Pending payables'],
                ['name' => 'Bank Reconciliation', 'url' => 'reports/bank_reconciliation', 'icon' => 'university', 'desc' => 'Bank vs book balance'],
                ['name' => 'Financial Ratios', 'url' => 'reports/financial_ratios', 'icon' => 'chart-area', 'desc' => 'Key financial metrics'],
                ['name' => 'Budget vs Actual', 'url' => 'reports/budget_actual', 'icon' => 'tasks', 'desc' => 'Budget performance'],
                ['name' => 'Departmental P&L', 'url' => 'reports/dept_pl', 'icon' => 'building', 'desc' => 'Profit by department']
            ];

            foreach ($financial_reports as $report): ?>
                <a href="<?php echo base_url($report['url']); ?>" class="flex items-center p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition group">
                    <div class="w-10 h-10 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition">
                        <i class="fas fa-<?php echo $report['icon']; ?>"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900"><?php echo $report['name']; ?></h4>
                        <p class="text-sm text-gray-600"><?php echo $report['desc']; ?></p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-primary-600"></i>
                </a>
            <?php endforeach; ?>
        </div>
    <?php card_end(); ?>

    <!-- 2. Insurance Reports -->
    <?php card_start('🛡️ Insurance Reports', '', true); ?>
        <div class="space-y-3">
            <?php
            $insurance_reports = [
                ['name' => 'Policy Register', 'url' => 'reports/insurance?type=policy_register', 'icon' => 'file-contract', 'desc' => 'All policies issued'],
                ['name' => 'Policy Issuance Report', 'url' => 'reports/insurance?type=issuance', 'icon' => 'file-signature', 'desc' => 'New policies by period'],
                ['name' => 'Policy Renewal Report', 'url' => 'reports/insurance?type=renewals', 'icon' => 'redo', 'desc' => 'Renewed policies'],
                ['name' => 'Policy Expiry Report', 'url' => 'reports/policy_expiry', 'icon' => 'calendar-times', 'desc' => 'Expiring policies'],
                ['name' => 'Policy Cancellation Report', 'url' => 'reports/policy_cancellations', 'icon' => 'ban', 'desc' => 'Cancelled policies'],
                ['name' => 'Policy Endorsement Report', 'url' => 'reports/endorsements', 'icon' => 'edit', 'desc' => 'Policy modifications'],
                ['name' => 'Claims Register', 'url' => 'reports/insurance?type=claims_register', 'icon' => 'clipboard-list', 'desc' => 'All claims'],
                ['name' => 'Claims Paid Report', 'url' => 'reports/claims_paid', 'icon' => 'check-circle', 'desc' => 'Settled claims'],
                ['name' => 'Claims Outstanding Report', 'url' => 'reports/claims_outstanding', 'icon' => 'hourglass-half', 'desc' => 'Pending claims'],
                ['name' => 'Claims Rejection Report', 'url' => 'reports/claims_rejected', 'icon' => 'times-circle', 'desc' => 'Rejected claims'],
                ['name' => 'Loss Ratio Report', 'url' => 'reports/loss_ratio', 'icon' => 'percentage', 'desc' => 'Claims vs Premium'],
                ['name' => 'Premium Analysis', 'url' => 'reports/premium_analysis', 'icon' => 'chart-bar', 'desc' => 'Premium breakdown'],
                ['name' => 'Sum Insured Report', 'url' => 'reports/sum_insured', 'icon' => 'money-check', 'desc' => 'Total coverage'],
                ['name' => 'Policy Type Analysis', 'url' => 'reports/policy_type_analysis', 'icon' => 'chart-pie', 'desc' => 'By insurance type'],
                ['name' => 'Reinsurance Report', 'url' => 'reports/reinsurance', 'icon' => 'handshake', 'desc' => 'Reinsurance ceded'],
                ['name' => 'Underwriting Report', 'url' => 'reports/underwriting', 'icon' => 'file-medical', 'desc' => 'Risk assessment'],
                ['name' => 'Motor Insurance Report', 'url' => 'reports/motor_insurance', 'icon' => 'car', 'desc' => 'Vehicle policies'],
                ['name' => 'Medical Insurance Report', 'url' => 'reports/medical_insurance', 'icon' => 'heartbeat', 'desc' => 'Health policies'],
                ['name' => 'Life Insurance Report', 'url' => 'reports/life_insurance', 'icon' => 'hand-holding-heart', 'desc' => 'Life policies'],
                ['name' => 'Property Insurance Report', 'url' => 'reports/property_insurance', 'icon' => 'home', 'desc' => 'Property policies']
            ];

            foreach ($insurance_reports as $report): ?>
                <a href="<?php echo base_url($report['url']); ?>" class="flex items-center p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition group">
                    <div class="w-10 h-10 bg-success-100 text-success-600 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition">
                        <i class="fas fa-<?php echo $report['icon']; ?>"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900"><?php echo $report['name']; ?></h4>
                        <p class="text-sm text-gray-600"><?php echo $report['desc']; ?></p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-success-600"></i>
                </a>
            <?php endforeach; ?>
        </div>
    <?php card_end(); ?>

    <!-- 3. Sales Reports -->
    <?php card_start('📊 Sales Reports', '', true); ?>
        <div class="space-y-3">
            <?php
            $sales_reports = [
                ['name' => 'Sales Dashboard', 'url' => 'reports/sales', 'icon' => 'chart-line', 'desc' => 'Sales overview'],
                ['name' => 'Quotation Report', 'url' => 'reports/quotations', 'icon' => 'file-alt', 'desc' => 'All quotations'],
                ['name' => 'Quotation Conversion Report', 'url' => 'reports/quote_conversion', 'icon' => 'exchange-alt', 'desc' => 'Quote to policy ratio'],
                ['name' => 'Sales Pipeline Report', 'url' => 'sales/pipeline', 'icon' => 'funnel-dollar', 'desc' => 'Sales stages'],
                ['name' => 'Agent Performance Report', 'url' => 'reports/agent_performance', 'icon' => 'user-tie', 'desc' => 'Top agents'],
                ['name' => 'Broker Performance Report', 'url' => 'reports/broker_performance', 'icon' => 'users', 'desc' => 'Broker analysis'],
                ['name' => 'Sales by Product Report', 'url' => 'reports/sales_by_product', 'icon' => 'boxes', 'desc' => 'Product-wise sales'],
                ['name' => 'Sales by Region Report', 'url' => 'reports/sales_by_region', 'icon' => 'map-marked-alt', 'desc' => 'Geographic sales'],
                ['name' => 'Sales Target vs Achievement', 'url' => 'reports/sales_targets', 'icon' => 'bullseye', 'desc' => 'Target performance'],
                ['name' => 'Customer Acquisition Report', 'url' => 'reports/customer_acquisition', 'icon' => 'user-plus', 'desc' => 'New customers']
            ];

            foreach ($sales_reports as $report): ?>
                <a href="<?php echo base_url($report['url']); ?>" class="flex items-center p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition group">
                    <div class="w-10 h-10 bg-warning-100 text-warning-600 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition">
                        <i class="fas fa-<?php echo $report['icon']; ?>"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900"><?php echo $report['name']; ?></h4>
                        <p class="text-sm text-gray-600"><?php echo $report['desc']; ?></p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-warning-600"></i>
                </a>
            <?php endforeach; ?>
        </div>
    <?php card_end(); ?>

    <!-- 4. Customer Reports -->
    <?php card_start('👥 Customer Reports', '', true); ?>
        <div class="space-y-3">
            <?php
            $customer_reports = [
                ['name' => 'Customer Register', 'url' => 'reports/customers', 'icon' => 'address-book', 'desc' => 'All customers'],
                ['name' => 'Customer Demographics', 'url' => 'reports/customer_demographics', 'icon' => 'users', 'desc' => 'Customer segmentation'],
                ['name' => 'Top Customers Report', 'url' => 'reports/top_customers', 'icon' => 'star', 'desc' => 'By premium volume'],
                ['name' => 'Customer Retention Report', 'url' => 'reports/customer_retention', 'icon' => 'user-check', 'desc' => 'Retention rate'],
                ['name' => 'Customer Churn Report', 'url' => 'reports/customer_churn', 'icon' => 'user-times', 'desc' => 'Lost customers'],
                ['name' => 'Customer Lifetime Value', 'url' => 'reports/customer_ltv', 'icon' => 'coins', 'desc' => 'Customer value'],
                ['name' => 'KYC Compliance Report', 'url' => 'reports/kyc_compliance', 'icon' => 'id-card', 'desc' => 'KYC status'],
                ['name' => 'Customer Portal Usage', 'url' => 'reports/portal_usage', 'icon' => 'desktop', 'desc' => 'Portal activity']
            ];

            foreach ($customer_reports as $report): ?>
                <a href="<?php echo base_url($report['url']); ?>" class="flex items-center p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition group">
                    <div class="w-10 h-10 bg-info-100 text-info-600 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition">
                        <i class="fas fa-<?php echo $report['icon']; ?>"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900"><?php echo $report['name']; ?></h4>
                        <p class="text-sm text-gray-600"><?php echo $report['desc']; ?></p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-info-600"></i>
                </a>
            <?php endforeach; ?>
        </div>
    <?php card_end(); ?>

    <!-- 5. Compliance Reports -->
    <?php card_start('⚖️ Compliance Reports', '', true); ?>
        <div class="space-y-3">
            <?php
            $compliance_reports = [
                ['name' => 'VAT Return (UAE)', 'url' => 'accounting/vat_reports', 'icon' => 'file-invoice-dollar', 'desc' => 'UAE VAT filing'],
                ['name' => 'VAT 201 Form', 'url' => 'reports/vat_201', 'icon' => 'file-alt', 'desc' => 'UAE VAT form'],
                ['name' => 'Input VAT Report', 'url' => 'reports/input_vat', 'icon' => 'download', 'desc' => 'Recoverable VAT'],
                ['name' => 'Output VAT Report', 'url' => 'reports/output_vat', 'icon' => 'upload', 'desc' => 'VAT on sales'],
                ['name' => 'Insurance Authority Return', 'url' => 'reports/ia_return', 'icon' => 'landmark', 'desc' => 'IA compliance'],
                ['name' => 'Quarterly IA Report', 'url' => 'reports/compliance?type=quarterly', 'icon' => 'calendar-check', 'desc' => 'Quarterly filing'],
                ['name' => 'Annual IA Report', 'url' => 'reports/compliance?type=annual', 'icon' => 'calendar', 'desc' => 'Annual filing'],
                ['name' => 'Audit Trail Report', 'url' => 'reports/audit_trail', 'icon' => 'history', 'desc' => 'System audit log'],
                ['name' => 'Regulatory Compliance Dashboard', 'url' => 'reports/compliance_dashboard', 'icon' => 'tasks', 'desc' => 'Compliance status'],
                ['name' => 'AML/KYC Report', 'url' => 'reports/aml_kyc', 'icon' => 'shield-alt', 'desc' => 'Anti-money laundering'],
                ['name' => 'Premium Tax Report', 'url' => 'reports/premium_tax', 'icon' => 'percent', 'desc' => 'Premium taxation'],
                ['name' => 'Commission Tax Report', 'url' => 'reports/commission_tax', 'icon' => 'coins', 'desc' => 'Commission tax'],
                ['name' => 'Withholding Tax Report', 'url' => 'reports/withholding_tax', 'icon' => 'hand-holding-usd', 'desc' => 'WHT compliance'],
                ['name' => 'GDPR Compliance Report', 'url' => 'reports/gdpr', 'icon' => 'user-shield', 'desc' => 'Data privacy'],
                ['name' => 'Zakat Calculation', 'url' => 'reports/zakat', 'icon' => 'mosque', 'desc' => 'Islamic obligation']
            ];

            foreach ($compliance_reports as $report): ?>
                <a href="<?php echo base_url($report['url']); ?>" class="flex items-center p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition group">
                    <div class="w-10 h-10 bg-danger-100 text-danger-600 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition">
                        <i class="fas fa-<?php echo $report['icon']; ?>"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900"><?php echo $report['name']; ?></h4>
                        <p class="text-sm text-gray-600"><?php echo $report['desc']; ?></p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-danger-600"></i>
                </a>
            <?php endforeach; ?>
        </div>
    <?php card_end(); ?>

    <!-- 6. HR Reports -->
    <?php card_start('👨‍💼 HR Reports', '', true); ?>
        <div class="space-y-3">
            <?php
            $hr_reports = [
                ['name' => 'Employee Directory', 'url' => 'hr/employees', 'icon' => 'id-badge', 'desc' => 'All employees'],
                ['name' => 'Payroll Report', 'url' => 'hr/payroll', 'icon' => 'money-bill-wave', 'desc' => 'Salary disbursements'],
                ['name' => 'Leave Summary Report', 'url' => 'reports/leave_summary', 'icon' => 'calendar-alt', 'desc' => 'Leave taken'],
                ['name' => 'Attendance Report', 'url' => 'reports/attendance', 'icon' => 'clock', 'desc' => 'Employee attendance'],
                ['name' => 'Departmental Headcount', 'url' => 'reports/headcount', 'icon' => 'users', 'desc' => 'Staff by department'],
                ['name' => 'Employee Performance', 'url' => 'reports/employee_performance', 'icon' => 'chart-line', 'desc' => 'Performance metrics']
            ];

            foreach ($hr_reports as $report): ?>
                <a href="<?php echo base_url($report['url']); ?>" class="flex items-center p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition group">
                    <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition">
                        <i class="fas fa-<?php echo $report['icon']; ?>"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900"><?php echo $report['name']; ?></h4>
                        <p class="text-sm text-gray-600"><?php echo $report['desc']; ?></p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-purple-600"></i>
                </a>
            <?php endforeach; ?>
        </div>
    <?php card_end(); ?>

</div>

<!-- Export Options -->
<div class="mt-8 p-6 bg-gradient-to-r from-primary-50 to-purple-50 rounded-xl" data-aos="fade-up">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Export Reports</h3>
            <p class="text-gray-600">All reports can be exported to CSV, Excel, or PDF formats</p>
        </div>
        <div class="flex gap-3">
            <button class="btn btn-outline">
                <i class="fas fa-file-csv"></i>
                Export CSV
            </button>
            <button class="btn btn-outline">
                <i class="fas fa-file-excel"></i>
                Export Excel
            </button>
            <button class="btn btn-primary">
                <i class="fas fa-file-pdf"></i>
                Export PDF
            </button>
        </div>
    </div>
</div>

<script>
// Add smooth scrolling
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});
</script>
