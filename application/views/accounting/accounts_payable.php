<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">
        <i class="fas fa-file-invoice-dollar mr-2"></i>Accounts Payable
    </h1>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Total Payable</div>
            <div class="text-3xl font-bold text-red-600">AED <?php echo number_format($summary['total_payable'] ?? 0, 2); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Current (0-30 days)</div>
            <div class="text-3xl font-bold text-green-600">AED <?php echo number_format($summary['current'] ?? 0, 2); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Overdue (31-60 days)</div>
            <div class="text-3xl font-bold text-yellow-600">AED <?php echo number_format($summary['overdue_30'] ?? 0, 2); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Overdue (60+ days)</div>
            <div class="text-3xl font-bold text-red-600">AED <?php echo number_format($summary['overdue_60'] ?? 0, 2); ?></div>
        </div>
    </div>

    <!-- Aging Analysis -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">Aging Analysis by Supplier</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Current</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">1-30 Days</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">31-60 Days</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">61-90 Days</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">90+ Days</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if(isset($aging_report) && count($aging_report) > 0): ?>
                        <?php foreach($aging_report as $supplier): ?>
                        <tr>
                            <td class="px-6 py-4 font-semibold"><?php echo $supplier->supplier_name; ?></td>
                            <td class="px-6 py-4 text-right">AED <?php echo number_format($supplier->current ?? 0, 2); ?></td>
                            <td class="px-6 py-4 text-right">AED <?php echo number_format($supplier->days_1_30 ?? 0, 2); ?></td>
                            <td class="px-6 py-4 text-right text-yellow-600">AED <?php echo number_format($supplier->days_31_60 ?? 0, 2); ?></td>
                            <td class="px-6 py-4 text-right text-orange-600">AED <?php echo number_format($supplier->days_61_90 ?? 0, 2); ?></td>
                            <td class="px-6 py-4 text-right text-red-600">AED <?php echo number_format($supplier->days_90_plus ?? 0, 2); ?></td>
                            <td class="px-6 py-4 text-right font-bold">AED <?php echo number_format($supplier->total ?? 0, 2); ?></td>
                            <td class="px-6 py-4">
                                <a href="<?php echo base_url('suppliers/view/'.$supplier->supplier_id); ?>" class="text-blue-600 hover:text-blue-800">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">No payables found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Outstanding Bills -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Outstanding Bills</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bill #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Paid</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Days Overdue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if(isset($outstanding_bills) && count($outstanding_bills) > 0): ?>
                        <?php foreach($outstanding_bills as $bill): ?>
                        <?php
                        $due_date = new DateTime($bill->due_date);
                        $today = new DateTime();
                        $days_overdue = $today > $due_date ? $today->diff($due_date)->days : 0;
                        $status_color = $days_overdue == 0 ? 'text-green-600' : ($days_overdue < 30 ? 'text-yellow-600' : 'text-red-600');
                        ?>
                        <tr>
                            <td class="px-6 py-4 font-medium"><?php echo $bill->bill_number; ?></td>
                            <td class="px-6 py-4"><?php echo $bill->supplier_name; ?></td>
                            <td class="px-6 py-4"><?php echo date('d/m/Y', strtotime($bill->bill_date)); ?></td>
                            <td class="px-6 py-4"><?php echo date('d/m/Y', strtotime($bill->due_date)); ?></td>
                            <td class="px-6 py-4 text-right">AED <?php echo number_format($bill->total_amount, 2); ?></td>
                            <td class="px-6 py-4 text-right">AED <?php echo number_format($bill->paid_amount ?? 0, 2); ?></td>
                            <td class="px-6 py-4 text-right font-bold">AED <?php echo number_format($bill->balance, 2); ?></td>
                            <td class="px-6 py-4 <?php echo $status_color; ?> font-semibold">
                                <?php echo $days_overdue > 0 ? $days_overdue . ' days' : 'Current'; ?>
                            </td>
                            <td class="px-6 py-4">
                                <a href="<?php echo base_url('bills/view/'.$bill->bill_id); ?>" class="text-blue-600 hover:text-blue-800 mr-3">View</a>
                                <a href="<?php echo base_url('receipts/add_payment?bill_id='.$bill->bill_id); ?>" class="text-green-600 hover:text-green-800">Pay</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500">No outstanding bills</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
