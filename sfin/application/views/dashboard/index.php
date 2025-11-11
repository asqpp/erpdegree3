<!-- Dashboard Header -->
<div class="page-header" data-aos="fade-down">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Welcome back! Here's what's happening today.</p>
    </div>
    <div class="flex gap-2">
        <button class="btn btn-outline btn-sm">
            <i class="fas fa-download"></i>
            Export Report
        </button>
        <button class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i>
            New Policy
        </button>
    </div>
</div>

<!-- Stats Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Total Policies -->
    <div class="stat-card bg-gradient-primary" data-aos="fade-up" data-aos-delay="100">
        <div class="flex items-start justify-between">
            <div>
                <p class="stat-label">Total Policies</p>
                <h3 class="stat-value">1,234</h3>
                <p class="text-sm mt-2">
                    <i class="fas fa-arrow-up"></i>
                    <span>12% from last month</span>
                </p>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                <i class="fas fa-file-contract text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Active Claims -->
    <div class="stat-card bg-gradient-warning" data-aos="fade-up" data-aos-delay="200">
        <div class="flex items-start justify-between">
            <div>
                <p class="stat-label">Active Claims</p>
                <h3 class="stat-value">45</h3>
                <p class="text-sm mt-2">
                    <i class="fas fa-arrow-down"></i>
                    <span>8% from last month</span>
                </p>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                <i class="fas fa-clipboard-check text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Premium Collected -->
    <div class="stat-card bg-gradient-success" data-aos="fade-up" data-aos-delay="300">
        <div class="flex items-start justify-between">
            <div>
                <p class="stat-label">Premium Collected</p>
                <h3 class="stat-value">2.5M</h3>
                <p class="text-sm mt-2">AED this month</p>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                <i class="fas fa-coins text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Customers -->
    <div class="stat-card bg-gradient-danger" data-aos="fade-up" data-aos-delay="400">
        <div class="flex items-start justify-between">
            <div>
                <p class="stat-label">Total Customers</p>
                <h3 class="stat-value">567</h3>
                <p class="text-sm mt-2">
                    <i class="fas fa-arrow-up"></i>
                    <span>25 new this week</span>
                </p>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                <i class="fas fa-users text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Premium Collection Trend -->
    <div class="card" data-aos="fade-up">
        <div class="card-header">
            <h3 class="card-title">Premium Collection Trend</h3>
            <div class="flex gap-2">
                <button class="text-sm text-gray-600 hover:text-primary-600">Monthly</button>
                <button class="text-sm text-gray-600 hover:text-primary-600">Yearly</button>
            </div>
        </div>
        <div class="card-body">
            <canvas id="premiumChart" height="250"></canvas>
        </div>
    </div>

    <!-- Claims by Status -->
    <div class="card" data-aos="fade-up" data-aos-delay="100">
        <div class="card-header">
            <h3 class="card-title">Claims by Status</h3>
        </div>
        <div class="card-body">
            <canvas id="claimsChart" height="250"></canvas>
        </div>
    </div>
</div>

<!-- Policies & Recent Activities Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Policy Distribution -->
    <div class="card lg:col-span-1" data-aos="fade-up">
        <div class="card-header">
            <h3 class="card-title">Policy Distribution</h3>
        </div>
        <div class="card-body">
            <canvas id="policyDistChart" height="200"></canvas>
        </div>
        <div class="mt-4 space-y-2">
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-primary-500 rounded"></div>
                    <span class="text-gray-700">Motor</span>
                </div>
                <span class="font-semibold">45%</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-success-500 rounded"></div>
                    <span class="text-gray-700">Health</span>
                </div>
                <span class="font-semibold">25%</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-warning-500 rounded"></div>
                    <span class="text-gray-700">Life</span>
                </div>
                <span class="font-semibold">20%</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-danger-500 rounded"></div>
                    <span class="text-gray-700">Others</span>
                </div>
                <span class="font-semibold">10%</span>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="card lg:col-span-2" data-aos="fade-up" data-aos-delay="100">
        <div class="card-header">
            <h3 class="card-title">Recent Activities</h3>
            <a href="#" class="text-sm text-primary-600 hover:text-primary-700">View All</a>
        </div>
        <div class="card-body">
            <div class="space-y-4">
                <!-- Activity Item -->
                <div class="flex items-start gap-4 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                    <div class="w-10 h-10 rounded-full bg-success-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check text-success-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">New policy issued</p>
                        <p class="text-xs text-gray-500 mt-1">Policy #MTR-2025-001 issued to Ahmed Al Maktoum</p>
                        <p class="text-xs text-gray-400 mt-1">
                            <i class="far fa-clock"></i> 5 minutes ago
                        </p>
                    </div>
                    <span class="badge badge-success">Policy</span>
                </div>

                <!-- Activity Item -->
                <div class="flex items-start gap-4 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                    <div class="w-10 h-10 rounded-full bg-warning-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-exclamation text-warning-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Claim approved</p>
                        <p class="text-xs text-gray-500 mt-1">Claim #CLM-001 approved for AED 15,000</p>
                        <p class="text-xs text-gray-400 mt-1">
                            <i class="far fa-clock"></i> 1 hour ago
                        </p>
                    </div>
                    <span class="badge badge-warning">Claim</span>
                </div>

                <!-- Activity Item -->
                <div class="flex items-start gap-4 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                    <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-money-bill text-primary-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Payment received</p>
                        <p class="text-xs text-gray-500 mt-1">Premium payment AED 5,000 from Fatima Hassan</p>
                        <p class="text-xs text-gray-400 mt-1">
                            <i class="far fa-clock"></i> 3 hours ago
                        </p>
                    </div>
                    <span class="badge badge-primary">Payment</span>
                </div>

                <!-- Activity Item -->
                <div class="flex items-start gap-4 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                    <div class="w-10 h-10 rounded-full bg-info-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user-plus text-info-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">New customer registered</p>
                        <p class="text-xs text-gray-500 mt-1">Mohammed Abdullah added to the system</p>
                        <p class="text-xs text-gray-400 mt-1">
                            <i class="far fa-clock"></i> 5 hours ago
                        </p>
                    </div>
                    <span class="badge badge-info">Customer</span>
                </div>

                <!-- Activity Item -->
                <div class="flex items-start gap-4 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                    <div class="w-10 h-10 rounded-full bg-danger-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-bell text-danger-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Policy expiring soon</p>
                        <p class="text-xs text-gray-500 mt-1">Policy #HLT-2024-523 expires in 7 days</p>
                        <p class="text-xs text-gray-400 mt-1">
                            <i class="far fa-clock"></i> 6 hours ago
                        </p>
                    </div>
                    <span class="badge badge-danger">Alert</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Policies Expiring Soon & Top Agents -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Policies Expiring Soon -->
    <div class="card" data-aos="fade-up">
        <div class="card-header">
            <h3 class="card-title">Policies Expiring Soon</h3>
            <span class="badge badge-warning">5 expiring</span>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Policy No</th>
                            <th>Customer</th>
                            <th>Type</th>
                            <th>Expiry Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-medium">#MTR-2024-001</td>
                            <td>Ahmed Al Maktoum</td>
                            <td><span class="badge badge-primary">Motor</span></td>
                            <td>15 Jan 2025</td>
                            <td>
                                <button class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                    Renew
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-medium">#HLT-2024-045</td>
                            <td>Fatima Hassan</td>
                            <td><span class="badge badge-success">Health</span></td>
                            <td>18 Jan 2025</td>
                            <td>
                                <button class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                    Renew
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-medium">#LIF-2024-089</td>
                            <td>Mohammed Abdullah</td>
                            <td><span class="badge badge-warning">Life</span></td>
                            <td>20 Jan 2025</td>
                            <td>
                                <button class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                    Renew
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Performing Agents -->
    <div class="card" data-aos="fade-up" data-aos-delay="100">
        <div class="card-header">
            <h3 class="card-title">Top Performing Agents</h3>
            <span class="text-sm text-gray-600">This Month</span>
        </div>
        <div class="card-body">
            <div class="space-y-4">
                <!-- Agent Item -->
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                        1
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Ali Mohammed</p>
                        <p class="text-xs text-gray-500">45 policies sold</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-success-600">AED 125K</p>
                        <p class="text-xs text-gray-500">Premium</p>
                    </div>
                </div>

                <!-- Agent Item -->
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-300 to-gray-500 flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                        2
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Sarah Ahmed</p>
                        <p class="text-xs text-gray-500">38 policies sold</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-success-600">AED 98K</p>
                        <p class="text-xs text-gray-500">Premium</p>
                    </div>
                </div>

                <!-- Agent Item -->
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                        3
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Omar Hassan</p>
                        <p class="text-xs text-gray-500">32 policies sold</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-success-600">AED 87K</p>
                        <p class="text-xs text-gray-500">Premium</p>
                    </div>
                </div>

                <!-- Agent Item -->
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-200 to-gray-400 flex items-center justify-center text-gray-700 font-bold text-lg flex-shrink-0">
                        4
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Noor Abdullah</p>
                        <p class="text-xs text-gray-500">28 policies sold</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-success-600">AED 75K</p>
                        <p class="text-xs text-gray-500">Premium</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Initialization Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Premium Collection Trend Chart
        const premiumCtx = document.getElementById('premiumChart');
        if (premiumCtx) {
            ChartHelpers.createLineChart(premiumCtx, [
                'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
            ], [{
                label: 'Premium Collected (AED)',
                data: [65000, 75000, 85000, 95000, 105000, 115000, 125000, 135000, 145000, 155000, 165000, 175000],
                borderColor: 'rgb(14, 165, 233)',
                backgroundColor: 'rgba(14, 165, 233, 0.1)',
                tension: 0.4,
                fill: true
            }]);
        }

        // Claims by Status Chart
        const claimsCtx = document.getElementById('claimsChart');
        if (claimsCtx) {
            ChartHelpers.createBarChart(claimsCtx, [
                'Registered', 'Investigating', 'Approved', 'Settled', 'Rejected'
            ], [{
                label: 'Number of Claims',
                data: [12, 8, 15, 20, 5],
                backgroundColor: [
                    'rgba(14, 165, 233, 0.8)',
                    'rgba(251, 191, 36, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(168, 85, 247, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ]
            }]);
        }

        // Policy Distribution Chart
        const policyDistCtx = document.getElementById('policyDistChart');
        if (policyDistCtx) {
            ChartHelpers.createDonutChart(policyDistCtx, [
                'Motor', 'Health', 'Life', 'Others'
            ], [45, 25, 20, 10]);
        }
    });
</script>
