<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">
        <i class="fas fa-chart-line mr-2"></i>Sales Dashboard
    </h1>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-gray-500 text-sm">Total Quotations</div>
                    <div class="text-3xl font-bold text-blue-600"><?php echo number_format($statistics['total_quotations']); ?></div>
                </div>
                <i class="fas fa-file-invoice text-4xl text-blue-200"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-gray-500 text-sm">Quotation Value</div>
                    <div class="text-2xl font-bold text-green-600">AED <?php echo number_format($statistics['quotation_value'], 0); ?></div>
                </div>
                <i class="fas fa-dollar-sign text-4xl text-green-200"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-gray-500 text-sm">Conversion Rate</div>
                    <div class="text-3xl font-bold text-purple-600"><?php echo number_format($statistics['conversion_rate'], 1); ?>%</div>
                </div>
                <i class="fas fa-percent text-4xl text-purple-200"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-gray-500 text-sm">Active Policies</div>
                    <div class="text-3xl font-bold text-indigo-600"><?php echo number_format($statistics['active_policies']); ?></div>
                </div>
                <i class="fas fa-file-contract text-4xl text-indigo-200"></i>
            </div>
        </div>
    </div>

    <!-- Stage Breakdown -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-gray-400">
            <div class="text-gray-600 text-sm">New Leads</div>
            <div class="text-2xl font-bold"><?php echo $statistics['stage_new'] ?? 0; ?></div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-400">
            <div class="text-blue-600 text-sm">Contacted</div>
            <div class="text-2xl font-bold"><?php echo $statistics['stage_contacted'] ?? 0; ?></div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-yellow-400">
            <div class="text-yellow-600 text-sm">Quoted</div>
            <div class="text-2xl font-bold"><?php echo $statistics['stage_quoted'] ?? 0; ?></div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-400">
            <div class="text-green-600 text-sm">Won</div>
            <div class="text-2xl font-bold"><?php echo $statistics['stage_won'] ?? 0; ?></div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-red-400">
            <div class="text-red-600 text-sm">Lost</div>
            <div class="text-2xl font-bold"><?php echo $statistics['stage_lost'] ?? 0; ?></div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <a href="<?php echo base_url('sales/quotation_form'); ?>" class="bg-gradient-to-r from-blue-500 to-blue-700 text-white p-6 rounded-lg shadow hover:shadow-lg transition">
            <div class="flex items-center">
                <i class="fas fa-plus-circle text-4xl mr-4"></i>
                <div>
                    <div class="text-lg font-bold">New Quotation</div>
                    <div class="text-sm opacity-90">Create a new insurance quotation</div>
                </div>
            </div>
        </a>

        <a href="<?php echo base_url('sales/pipeline'); ?>" class="bg-gradient-to-r from-purple-500 to-purple-700 text-white p-6 rounded-lg shadow hover:shadow-lg transition">
            <div class="flex items-center">
                <i class="fas fa-funnel-dollar text-4xl mr-4"></i>
                <div>
                    <div class="text-lg font-bold">Sales Pipeline</div>
                    <div class="text-sm opacity-90">View sales pipeline & stages</div>
                </div>
            </div>
        </a>

        <a href="<?php echo base_url('sales/commissions'); ?>" class="bg-gradient-to-r from-green-500 to-green-700 text-white p-6 rounded-lg shadow hover:shadow-lg transition">
            <div class="flex items-center">
                <i class="fas fa-hand-holding-usd text-4xl mr-4"></i>
                <div>
                    <div class="text-lg font-bold">Commissions</div>
                    <div class="text-sm opacity-90">View agent commissions</div>
                </div>
            </div>
        </a>
    </div>

    <!-- Recent Quotations -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Recent Quotations</h2>
            <a href="<?php echo base_url('sales/quotation_list'); ?>" class="text-blue-600 hover:underline">View All</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quote #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Premium</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if(isset($recent_quotations) && count($recent_quotations) > 0): ?>
                        <?php foreach($recent_quotations as $quote): ?>
                        <tr>
                            <td class="px-4 py-3 font-medium"><?php echo $quote->quotation_number; ?></td>
                            <td class="px-4 py-3"><?php echo $quote->customer_name; ?></td>
                            <td class="px-4 py-3"><?php echo ucfirst($quote->insurance_type); ?></td>
                            <td class="px-4 py-3">AED <?php echo number_format($quote->premium_amount, 2); ?></td>
                            <td class="px-4 py-3">
                                <?php
                                $statusColors = [
                                    'draft' => 'bg-gray-100 text-gray-800',
                                    'sent' => 'bg-blue-100 text-blue-800',
                                    'accepted' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800'
                                ];
                                $colorClass = $statusColors[$quote->status] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="px-2 py-1 <?php echo $colorClass; ?> rounded text-xs"><?php echo ucfirst($quote->status); ?></span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="<?php echo base_url('sales/quotation_view/'.$quote->quotation_id); ?>" class="text-blue-600 hover:text-blue-800">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">No recent quotations</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
