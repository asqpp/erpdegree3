<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Receipt Voucher: <?php echo $receipt->voucher_number; ?></h1>
            <div>
                <a href="<?php echo base_url('receipts/print_voucher/'.$receipt->receipt_id); ?>" class="bg-green-600 text-white px-4 py-2 rounded mr-2">
                    <i class="fas fa-print mr-2"></i>Print
                </a>
                <a href="<?php echo base_url('receipts'); ?>" class="bg-gray-600 text-white px-4 py-2 rounded">Back</a>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div><strong>Date:</strong> <?php echo date('d/m/Y', strtotime($receipt->voucher_date)); ?></div>
            <div><strong>Party:</strong> <?php echo $receipt->party_name; ?></div>
            <div><strong>Payment Method:</strong> <?php echo ucfirst($receipt->payment_method); ?></div>
            <div><strong>Amount:</strong> <span class="text-green-600 font-bold">AED <?php echo number_format($receipt->total_amount, 2); ?></span></div>
        </div>
        
        <?php if($receipt->narration): ?>
        <div class="mb-6">
            <strong>Narration:</strong>
            <p class="text-gray-700 mt-2"><?php echo $receipt->narration; ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>
