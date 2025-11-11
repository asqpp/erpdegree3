<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-money-check-alt mr-2"></i>Receipt Vouchers
        </h1>
        <a href="<?php echo base_url('receipts/add'); ?>" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>New Receipt
        </a>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Total Receipts</div>
            <div class="text-3xl font-bold text-blue-600"><?php echo number_format($statistics['total_receipts']); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Total Amount</div>
            <div class="text-3xl font-bold text-green-600">AED <?php echo number_format($statistics['total_amount'], 2); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Today</div>
            <div class="text-3xl font-bold text-purple-600"><?php echo number_format($statistics['today_receipts']); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">This Month</div>
            <div class="text-3xl font-bold text-orange-600"><?php echo number_format($statistics['month_receipts']); ?></div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form method="get" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" placeholder="Search..." class="border rounded px-3 py-2" value="<?php echo $filters['search']; ?>">
            <select name="payment_method" class="border rounded px-3 py-2">
                <option value="">All Methods</option>
                <option value="cash">Cash</option>
                <option value="cheque">Cheque</option>
                <option value="bank_transfer">Bank Transfer</option>
            </select>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
            <a href="<?php echo base_url('receipts/export'); ?>" class="bg-green-600 text-white px-4 py-2 rounded text-center">Export CSV</a>
        </form>
    </div>

    <!-- Receipts Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Voucher #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Party</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach($receipts as $receipt): ?>
                <tr>
                    <td class="px-6 py-4"><?php echo $receipt->voucher_number; ?></td>
                    <td class="px-6 py-4"><?php echo date('d/m/Y', strtotime($receipt->voucher_date)); ?></td>
                    <td class="px-6 py-4"><?php echo $receipt->party_name; ?></td>
                    <td class="px-6 py-4"><span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs"><?php echo ucfirst($receipt->payment_method); ?></span></td>
                    <td class="px-6 py-4 font-semibold">AED <?php echo number_format($receipt->total_amount, 2); ?></td>
                    <td class="px-6 py-4">
                        <a href="<?php echo base_url('receipts/view/'.$receipt->receipt_id); ?>" class="text-blue-600 hover:text-blue-800 mr-3">View</a>
                        <a href="<?php echo base_url('receipts/print_voucher/'.$receipt->receipt_id); ?>" class="text-green-600 hover:text-green-800">Print</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
