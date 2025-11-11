<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">
        <i class="fas fa-hand-holding-usd mr-2"></i>Agent Commissions
    </h1>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Total Commission</div>
            <div class="text-3xl font-bold text-green-600">AED <?php echo number_format($summary['total_commission'] ?? 0, 2); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Paid Commission</div>
            <div class="text-3xl font-bold text-blue-600">AED <?php echo number_format($summary['paid_commission'] ?? 0, 2); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Pending Commission</div>
            <div class="text-3xl font-bold text-orange-600">AED <?php echo number_format($summary['pending_commission'] ?? 0, 2); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Active Agents</div>
            <div class="text-3xl font-bold text-purple-600"><?php echo number_format($summary['active_agents'] ?? 0); ?></div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form method="get" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <select name="agent_id" class="border rounded px-3 py-2">
                <option value="">All Agents</option>
                <?php foreach($agents as $agent): ?>
                <option value="<?php echo $agent->employee_id; ?>" <?php echo (isset($filters['agent_id']) && $filters['agent_id'] == $agent->employee_id) ? 'selected' : ''; ?>>
                    <?php echo $agent->full_name; ?>
                </option>
                <?php endforeach; ?>
            </select>

            <select name="status" class="border rounded px-3 py-2">
                <option value="">All Status</option>
                <option value="pending" <?php echo (isset($filters['status']) && $filters['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="paid" <?php echo (isset($filters['status']) && $filters['status'] == 'paid') ? 'selected' : ''; ?>>Paid</option>
            </select>

            <input type="month" name="month" value="<?php echo $filters['month'] ?? date('Y-m'); ?>" class="border rounded px-3 py-2">

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
            <a href="<?php echo base_url('sales/export_commissions'); ?>" class="bg-green-600 text-white px-4 py-2 rounded text-center">Export CSV</a>
        </form>
    </div>

    <!-- Commissions Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Agent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Policy #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Premium</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rate</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commission</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if(isset($commissions) && count($commissions) > 0): ?>
                    <?php foreach($commissions as $commission): ?>
                    <tr>
                        <td class="px-6 py-4"><?php echo $commission->agent_name; ?></td>
                        <td class="px-6 py-4"><?php echo $commission->customer_name; ?></td>
                        <td class="px-6 py-4 font-medium"><?php echo $commission->policy_number; ?></td>
                        <td class="px-6 py-4">AED <?php echo number_format($commission->premium_amount, 2); ?></td>
                        <td class="px-6 py-4"><?php echo $commission->commission_rate; ?>%</td>
                        <td class="px-6 py-4 font-bold text-green-600">AED <?php echo number_format($commission->commission_amount, 2); ?></td>
                        <td class="px-6 py-4"><?php echo date('d/m/Y', strtotime($commission->commission_date)); ?></td>
                        <td class="px-6 py-4">
                            <?php if($commission->payment_status == 'paid'): ?>
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Paid</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded text-xs">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if($commission->payment_status == 'pending'): ?>
                                <a href="<?php echo base_url('sales/mark_commission_paid/'.$commission->commission_id); ?>"
                                   class="text-green-600 hover:text-green-800"
                                   onclick="return confirm('Mark this commission as paid?')">
                                    Mark Paid
                                </a>
                            <?php else: ?>
                                <span class="text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center text-gray-500">No commissions found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Agent Summary -->
    <?php if(isset($agent_summary) && count($agent_summary) > 0): ?>
    <div class="mt-6 bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Agent Summary</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php foreach($agent_summary as $agent): ?>
            <div class="border rounded p-4">
                <div class="font-semibold text-lg"><?php echo $agent->agent_name; ?></div>
                <div class="text-sm text-gray-500 mt-2">
                    <div>Policies Sold: <span class="font-semibold"><?php echo $agent->policy_count; ?></span></div>
                    <div>Total Premium: <span class="font-semibold">AED <?php echo number_format($agent->total_premium, 2); ?></span></div>
                    <div class="text-green-600 font-bold mt-1">Commission: AED <?php echo number_format($agent->total_commission, 2); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
