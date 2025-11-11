<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold">Quotation: <?php echo $quotation->quotation_number; ?></h1>
                <?php
                $statusColors = [
                    'draft' => 'bg-gray-100 text-gray-800',
                    'sent' => 'bg-blue-100 text-blue-800',
                    'accepted' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800'
                ];
                $colorClass = $statusColors[$quotation->status] ?? 'bg-gray-100 text-gray-800';
                ?>
                <span class="inline-block mt-2 px-3 py-1 <?php echo $colorClass; ?> rounded text-sm"><?php echo ucfirst($quotation->status); ?></span>
            </div>
            <div>
                <?php if($quotation->status == 'sent'): ?>
                    <a href="<?php echo base_url('sales/quotation_accept/'.$quotation->quotation_id); ?>" class="bg-green-600 text-white px-4 py-2 rounded mr-2">
                        <i class="fas fa-check mr-2"></i>Accept
                    </a>
                    <a href="<?php echo base_url('sales/quotation_reject/'.$quotation->quotation_id); ?>" class="bg-red-600 text-white px-4 py-2 rounded mr-2">
                        <i class="fas fa-times mr-2"></i>Reject
                    </a>
                <?php endif; ?>
                <a href="<?php echo base_url('sales/quotation_print/'.$quotation->quotation_id); ?>" class="bg-blue-600 text-white px-4 py-2 rounded mr-2">
                    <i class="fas fa-print mr-2"></i>Print
                </a>
                <a href="<?php echo base_url('sales/quotation_list'); ?>" class="bg-gray-600 text-white px-4 py-2 rounded">Back</a>
            </div>
        </div>

        <!-- Quotation Details -->
        <div class="grid grid-cols-2 gap-6 mb-6 pb-6 border-b">
            <div>
                <p class="text-sm text-gray-500">Customer</p>
                <p class="font-semibold"><?php echo $quotation->customer_name; ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Insurance Type</p>
                <p class="font-semibold"><?php echo ucfirst($quotation->insurance_type); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Quotation Date</p>
                <p class="font-semibold"><?php echo date('d/m/Y', strtotime($quotation->quotation_date)); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Valid Until</p>
                <p class="font-semibold"><?php echo date('d/m/Y', strtotime($quotation->valid_until)); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Insurance Company</p>
                <p class="font-semibold"><?php echo $quotation->insurance_company_name; ?></p>
            </div>
            <?php if($quotation->agent_name): ?>
            <div>
                <p class="text-sm text-gray-500">Agent</p>
                <p class="font-semibold"><?php echo $quotation->agent_name; ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Coverage Details -->
        <div class="mb-6 pb-6 border-b">
            <h3 class="text-lg font-semibold mb-4">Coverage Information</h3>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Coverage Amount</p>
                    <p class="text-2xl font-bold text-blue-600">AED <?php echo number_format($quotation->coverage_amount, 2); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Premium Amount</p>
                    <p class="text-2xl font-bold text-green-600">AED <?php echo number_format($quotation->premium_amount, 2); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Policy Term</p>
                    <p class="font-semibold"><?php echo $quotation->policy_term_months; ?> months</p>
                </div>
                <?php if($quotation->commission_rate > 0): ?>
                <div>
                    <p class="text-sm text-gray-500">Commission</p>
                    <p class="font-semibold"><?php echo $quotation->commission_rate; ?>% (AED <?php echo number_format($quotation->premium_amount * $quotation->commission_rate / 100, 2); ?>)</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Coverage Details Text -->
        <?php if($quotation->coverage_details): ?>
        <div class="mb-6 pb-6 border-b">
            <h3 class="text-lg font-semibold mb-2">Coverage Details</h3>
            <div class="text-gray-700 whitespace-pre-line"><?php echo $quotation->coverage_details; ?></div>
        </div>
        <?php endif; ?>

        <!-- Terms & Conditions -->
        <?php if($quotation->terms_conditions): ?>
        <div class="mb-6 pb-6 border-b">
            <h3 class="text-lg font-semibold mb-2">Terms & Conditions</h3>
            <div class="text-gray-700 whitespace-pre-line"><?php echo $quotation->terms_conditions; ?></div>
        </div>
        <?php endif; ?>

        <!-- Notes -->
        <?php if($quotation->notes): ?>
        <div class="mb-6 pb-6 border-b">
            <h3 class="text-lg font-semibold mb-2">Internal Notes</h3>
            <div class="text-gray-700 whitespace-pre-line"><?php echo $quotation->notes; ?></div>
        </div>
        <?php endif; ?>

        <!-- Footer Info -->
        <div class="text-sm text-gray-500">
            <p>Created by: <?php echo $quotation->created_by_name; ?> on <?php echo date('d/m/Y H:i', strtotime($quotation->created_at)); ?></p>
            <?php if($quotation->sent_at): ?>
                <p>Sent on: <?php echo date('d/m/Y H:i', strtotime($quotation->sent_at)); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>
