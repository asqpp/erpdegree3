<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">
        <i class="fas fa-funnel-dollar mr-2"></i>Sales Pipeline
    </h1>

    <!-- Pipeline Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow text-center">
            <div class="text-3xl font-bold text-gray-600"><?php echo $pipeline_stats['new_count'] ?? 0; ?></div>
            <div class="text-sm text-gray-500">New Leads</div>
            <div class="text-lg font-semibold mt-2">AED <?php echo number_format($pipeline_stats['new_value'] ?? 0, 0); ?></div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow text-center">
            <div class="text-3xl font-bold text-blue-600"><?php echo $pipeline_stats['contacted_count'] ?? 0; ?></div>
            <div class="text-sm text-gray-500">Contacted</div>
            <div class="text-lg font-semibold mt-2">AED <?php echo number_format($pipeline_stats['contacted_value'] ?? 0, 0); ?></div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow text-center">
            <div class="text-3xl font-bold text-yellow-600"><?php echo $pipeline_stats['quoted_count'] ?? 0; ?></div>
            <div class="text-sm text-gray-500">Quoted</div>
            <div class="text-lg font-semibold mt-2">AED <?php echo number_format($pipeline_stats['quoted_value'] ?? 0, 0); ?></div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow text-center">
            <div class="text-3xl font-bold text-green-600"><?php echo $pipeline_stats['won_count'] ?? 0; ?></div>
            <div class="text-sm text-gray-500">Won</div>
            <div class="text-lg font-semibold mt-2">AED <?php echo number_format($pipeline_stats['won_value'] ?? 0, 0); ?></div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow text-center">
            <div class="text-3xl font-bold text-red-600"><?php echo $pipeline_stats['lost_count'] ?? 0; ?></div>
            <div class="text-sm text-gray-500">Lost</div>
            <div class="text-lg font-semibold mt-2">AED <?php echo number_format($pipeline_stats['lost_value'] ?? 0, 0); ?></div>
        </div>
    </div>

    <!-- Pipeline Stages -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <!-- New Leads -->
        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="font-bold text-gray-700 mb-4 flex items-center">
                <span class="bg-gray-400 w-3 h-3 rounded-full mr-2"></span>
                New Leads
            </h3>
            <div class="space-y-3">
                <?php if(isset($pipeline['new']) && count($pipeline['new']) > 0): ?>
                    <?php foreach($pipeline['new'] as $item): ?>
                    <div class="bg-white p-3 rounded shadow-sm hover:shadow-md transition">
                        <div class="font-semibold text-sm"><?php echo $item->customer_name; ?></div>
                        <div class="text-xs text-gray-500"><?php echo ucfirst($item->insurance_type); ?></div>
                        <div class="text-green-600 font-bold mt-1">AED <?php echo number_format($item->premium_amount, 0); ?></div>
                        <a href="<?php echo base_url('sales/quotation_view/'.$item->quotation_id); ?>" class="text-xs text-blue-600 hover:underline">View</a>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-sm text-gray-400 text-center py-4">No items</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Contacted -->
        <div class="bg-blue-50 rounded-lg p-4">
            <h3 class="font-bold text-blue-700 mb-4 flex items-center">
                <span class="bg-blue-400 w-3 h-3 rounded-full mr-2"></span>
                Contacted
            </h3>
            <div class="space-y-3">
                <?php if(isset($pipeline['contacted']) && count($pipeline['contacted']) > 0): ?>
                    <?php foreach($pipeline['contacted'] as $item): ?>
                    <div class="bg-white p-3 rounded shadow-sm hover:shadow-md transition">
                        <div class="font-semibold text-sm"><?php echo $item->customer_name; ?></div>
                        <div class="text-xs text-gray-500"><?php echo ucfirst($item->insurance_type); ?></div>
                        <div class="text-green-600 font-bold mt-1">AED <?php echo number_format($item->premium_amount, 0); ?></div>
                        <a href="<?php echo base_url('sales/quotation_view/'.$item->quotation_id); ?>" class="text-xs text-blue-600 hover:underline">View</a>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-sm text-gray-400 text-center py-4">No items</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quoted -->
        <div class="bg-yellow-50 rounded-lg p-4">
            <h3 class="font-bold text-yellow-700 mb-4 flex items-center">
                <span class="bg-yellow-400 w-3 h-3 rounded-full mr-2"></span>
                Quoted
            </h3>
            <div class="space-y-3">
                <?php if(isset($pipeline['quoted']) && count($pipeline['quoted']) > 0): ?>
                    <?php foreach($pipeline['quoted'] as $item): ?>
                    <div class="bg-white p-3 rounded shadow-sm hover:shadow-md transition">
                        <div class="font-semibold text-sm"><?php echo $item->customer_name; ?></div>
                        <div class="text-xs text-gray-500"><?php echo ucfirst($item->insurance_type); ?></div>
                        <div class="text-green-600 font-bold mt-1">AED <?php echo number_format($item->premium_amount, 0); ?></div>
                        <a href="<?php echo base_url('sales/quotation_view/'.$item->quotation_id); ?>" class="text-xs text-blue-600 hover:underline">View</a>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-sm text-gray-400 text-center py-4">No items</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Won -->
        <div class="bg-green-50 rounded-lg p-4">
            <h3 class="font-bold text-green-700 mb-4 flex items-center">
                <span class="bg-green-400 w-3 h-3 rounded-full mr-2"></span>
                Won
            </h3>
            <div class="space-y-3">
                <?php if(isset($pipeline['won']) && count($pipeline['won']) > 0): ?>
                    <?php foreach($pipeline['won'] as $item): ?>
                    <div class="bg-white p-3 rounded shadow-sm hover:shadow-md transition">
                        <div class="font-semibold text-sm"><?php echo $item->customer_name; ?></div>
                        <div class="text-xs text-gray-500"><?php echo ucfirst($item->insurance_type); ?></div>
                        <div class="text-green-600 font-bold mt-1">AED <?php echo number_format($item->premium_amount, 0); ?></div>
                        <a href="<?php echo base_url('sales/quotation_view/'.$item->quotation_id); ?>" class="text-xs text-blue-600 hover:underline">View</a>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-sm text-gray-400 text-center py-4">No items</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Lost -->
        <div class="bg-red-50 rounded-lg p-4">
            <h3 class="font-bold text-red-700 mb-4 flex items-center">
                <span class="bg-red-400 w-3 h-3 rounded-full mr-2"></span>
                Lost
            </h3>
            <div class="space-y-3">
                <?php if(isset($pipeline['lost']) && count($pipeline['lost']) > 0): ?>
                    <?php foreach($pipeline['lost'] as $item): ?>
                    <div class="bg-white p-3 rounded shadow-sm hover:shadow-md transition">
                        <div class="font-semibold text-sm"><?php echo $item->customer_name; ?></div>
                        <div class="text-xs text-gray-500"><?php echo ucfirst($item->insurance_type); ?></div>
                        <div class="text-gray-600 font-bold mt-1">AED <?php echo number_format($item->premium_amount, 0); ?></div>
                        <a href="<?php echo base_url('sales/quotation_view/'.$item->quotation_id); ?>" class="text-xs text-blue-600 hover:underline">View</a>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-sm text-gray-400 text-center py-4">No items</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
