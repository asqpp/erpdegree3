<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-file-invoice-dollar mr-2"></i>VAT Reports (UAE)
        </h1>
        <div class="flex gap-2">
            <a href="<?php echo base_url('accounting/export_vat'); ?>" class="bg-green-600 text-white px-4 py-2 rounded">
                <i class="fas fa-file-excel mr-2"></i>Export
            </a>
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded">
                <i class="fas fa-print mr-2"></i>Print
            </button>
        </div>
    </div>

    <!-- Period Selector -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form method="get" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                <input type="date" name="date_from" value="<?php echo $filters['date_from'] ?? date('Y-m-01'); ?>" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                <input type="date" name="date_to" value="<?php echo $filters['date_to'] ?? date('Y-m-t'); ?>" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">VAT Period</label>
                <select name="period" class="w-full border rounded px-3 py-2" onchange="this.form.submit()">
                    <option value="">Custom</option>
                    <option value="current_month">Current Month</option>
                    <option value="last_month">Last Month</option>
                    <option value="current_quarter">Current Quarter</option>
                    <option value="last_quarter">Last Quarter</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded">Generate</button>
            </div>
        </form>
    </div>

    <!-- VAT Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Total VAT Output</div>
            <div class="text-3xl font-bold text-green-600">AED <?php echo number_format($vat_summary['output_vat'] ?? 0, 2); ?></div>
            <div class="text-xs text-gray-500 mt-1">VAT on Sales</div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Total VAT Input</div>
            <div class="text-3xl font-bold text-blue-600">AED <?php echo number_format($vat_summary['input_vat'] ?? 0, 2); ?></div>
            <div class="text-xs text-gray-500 mt-1">VAT on Purchases</div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">VAT Payable / (Refundable)</div>
            <?php
            $vat_payable = ($vat_summary['output_vat'] ?? 0) - ($vat_summary['input_vat'] ?? 0);
            $color = $vat_payable >= 0 ? 'text-red-600' : 'text-green-600';
            ?>
            <div class="text-3xl font-bold <?php echo $color; ?>">AED <?php echo number_format($vat_payable, 2); ?></div>
            <div class="text-xs text-gray-500 mt-1"><?php echo $vat_payable >= 0 ? 'Amount to Pay' : 'Amount to Receive'; ?></div>
        </div>
    </div>

    <!-- UAE VAT Return (9-Box Format) -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">UAE VAT Return</h2>
        <p class="text-gray-600 mb-6">Period: <?php echo date('d/m/Y', strtotime($filters['date_from'])); ?> to <?php echo date('d/m/Y', strtotime($filters['date_to'])); ?></p>

        <div class="overflow-x-auto">
            <table class="min-w-full border">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left">Box #</th>
                        <th class="px-4 py-3 text-left">Description</th>
                        <th class="px-4 py-3 text-right">Amount (AED)</th>
                        <th class="px-4 py-3 text-right">VAT Amount (AED)</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <!-- Box 1: Standard Rated Sales -->
                    <tr class="border-b bg-green-50">
                        <td class="px-4 py-3 font-bold">1</td>
                        <td class="px-4 py-3 font-semibold">Standard Rated Sales (5%)</td>
                        <td class="px-4 py-3 text-right font-semibold">AED <?php echo number_format($vat_boxes['box1_amount'] ?? 0, 2); ?></td>
                        <td class="px-4 py-3 text-right font-semibold text-green-600">AED <?php echo number_format($vat_boxes['box1_vat'] ?? 0, 2); ?></td>
                    </tr>

                    <!-- Box 2: Zero Rated Sales -->
                    <tr class="border-b">
                        <td class="px-4 py-3 font-bold">2</td>
                        <td class="px-4 py-3">Zero Rated Sales (0%)</td>
                        <td class="px-4 py-3 text-right">AED <?php echo number_format($vat_boxes['box2_amount'] ?? 0, 2); ?></td>
                        <td class="px-4 py-3 text-right">AED 0.00</td>
                    </tr>

                    <!-- Box 3: Exempt Sales -->
                    <tr class="border-b">
                        <td class="px-4 py-3 font-bold">3</td>
                        <td class="px-4 py-3">Exempt Sales</td>
                        <td class="px-4 py-3 text-right">AED <?php echo number_format($vat_boxes['box3_amount'] ?? 0, 2); ?></td>
                        <td class="px-4 py-3 text-right">-</td>
                    </tr>

                    <!-- Box 4: Goods Imported into UAE -->
                    <tr class="border-b bg-blue-50">
                        <td class="px-4 py-3 font-bold">4</td>
                        <td class="px-4 py-3 font-semibold">Goods Imported into UAE</td>
                        <td class="px-4 py-3 text-right font-semibold">AED <?php echo number_format($vat_boxes['box4_amount'] ?? 0, 2); ?></td>
                        <td class="px-4 py-3 text-right font-semibold text-blue-600">AED <?php echo number_format($vat_boxes['box4_vat'] ?? 0, 2); ?></td>
                    </tr>

                    <!-- Box 5: Standard Rated Purchases -->
                    <tr class="border-b bg-blue-50">
                        <td class="px-4 py-3 font-bold">5</td>
                        <td class="px-4 py-3 font-semibold">Standard Rated Purchases (5%)</td>
                        <td class="px-4 py-3 text-right font-semibold">AED <?php echo number_format($vat_boxes['box5_amount'] ?? 0, 2); ?></td>
                        <td class="px-4 py-3 text-right font-semibold text-blue-600">AED <?php echo number_format($vat_boxes['box5_vat'] ?? 0, 2); ?></td>
                    </tr>

                    <!-- Box 6: Total Output VAT -->
                    <tr class="border-b bg-green-100">
                        <td class="px-4 py-3 font-bold">6</td>
                        <td class="px-4 py-3 font-bold">Total Output VAT (Box 1)</td>
                        <td class="px-4 py-3 text-right">-</td>
                        <td class="px-4 py-3 text-right font-bold text-green-700">AED <?php echo number_format($vat_boxes['box1_vat'] ?? 0, 2); ?></td>
                    </tr>

                    <!-- Box 7: Total Input VAT -->
                    <tr class="border-b bg-blue-100">
                        <td class="px-4 py-3 font-bold">7</td>
                        <td class="px-4 py-3 font-bold">Total Input VAT (Box 4 + Box 5)</td>
                        <td class="px-4 py-3 text-right">-</td>
                        <td class="px-4 py-3 text-right font-bold text-blue-700">AED <?php echo number_format(($vat_boxes['box4_vat'] ?? 0) + ($vat_boxes['box5_vat'] ?? 0), 2); ?></td>
                    </tr>

                    <!-- Box 8: Net VAT Due -->
                    <?php
                    $net_vat = ($vat_boxes['box1_vat'] ?? 0) - (($vat_boxes['box4_vat'] ?? 0) + ($vat_boxes['box5_vat'] ?? 0));
                    $net_vat_color = $net_vat >= 0 ? 'text-red-700' : 'text-green-700';
                    $net_vat_bg = $net_vat >= 0 ? 'bg-red-100' : 'bg-green-100';
                    ?>
                    <tr class="border-b <?php echo $net_vat_bg; ?>">
                        <td class="px-4 py-3 font-bold">8</td>
                        <td class="px-4 py-3 font-bold"><?php echo $net_vat >= 0 ? 'Net VAT Due (Box 6 - Box 7)' : 'VAT Refund Due (Box 7 - Box 6)'; ?></td>
                        <td class="px-4 py-3 text-right">-</td>
                        <td class="px-4 py-3 text-right font-bold text-xl <?php echo $net_vat_color; ?>">AED <?php echo number_format(abs($net_vat), 2); ?></td>
                    </tr>

                    <!-- Box 9: Corrections -->
                    <tr class="border-b">
                        <td class="px-4 py-3 font-bold">9</td>
                        <td class="px-4 py-3">Corrections from Previous Period</td>
                        <td class="px-4 py-3 text-right">-</td>
                        <td class="px-4 py-3 text-right">AED <?php echo number_format($vat_boxes['box9_corrections'] ?? 0, 2); ?></td>
                    </tr>

                    <!-- Final Net VAT -->
                    <?php $final_vat = $net_vat + ($vat_boxes['box9_corrections'] ?? 0); ?>
                    <tr class="bg-gray-800 text-white">
                        <td class="px-4 py-4 font-bold"></td>
                        <td class="px-4 py-4 font-bold text-lg">NET VAT PAYABLE / (REFUNDABLE)</td>
                        <td class="px-4 py-4 text-right">-</td>
                        <td class="px-4 py-4 text-right font-bold text-2xl">AED <?php echo number_format($final_vat, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- VAT Transactions Detail -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">VAT Transaction Details</h2>

        <!-- Output VAT (Sales) -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-green-700 mb-3">Output VAT (Sales)</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full border">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Invoice #</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Net Amount</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">VAT Amount</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        <?php if(isset($output_vat_transactions) && count($output_vat_transactions) > 0): ?>
                            <?php foreach($output_vat_transactions as $txn): ?>
                            <tr>
                                <td class="px-4 py-2"><?php echo date('d/m/Y', strtotime($txn->transaction_date)); ?></td>
                                <td class="px-4 py-2 font-medium"><?php echo $txn->invoice_number; ?></td>
                                <td class="px-4 py-2"><?php echo $txn->customer_name; ?></td>
                                <td class="px-4 py-2 text-right">AED <?php echo number_format($txn->net_amount, 2); ?></td>
                                <td class="px-4 py-2 text-right text-green-600 font-semibold">AED <?php echo number_format($txn->vat_amount, 2); ?></td>
                                <td class="px-4 py-2 text-right font-semibold">AED <?php echo number_format($txn->total_amount, 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-4 py-4 text-center text-gray-500">No output VAT transactions</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Input VAT (Purchases) -->
        <div>
            <h3 class="text-lg font-semibold text-blue-700 mb-3">Input VAT (Purchases)</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full border">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Bill #</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Net Amount</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">VAT Amount</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        <?php if(isset($input_vat_transactions) && count($input_vat_transactions) > 0): ?>
                            <?php foreach($input_vat_transactions as $txn): ?>
                            <tr>
                                <td class="px-4 py-2"><?php echo date('d/m/Y', strtotime($txn->transaction_date)); ?></td>
                                <td class="px-4 py-2 font-medium"><?php echo $txn->bill_number; ?></td>
                                <td class="px-4 py-2"><?php echo $txn->supplier_name; ?></td>
                                <td class="px-4 py-2 text-right">AED <?php echo number_format($txn->net_amount, 2); ?></td>
                                <td class="px-4 py-2 text-right text-blue-600 font-semibold">AED <?php echo number_format($txn->vat_amount, 2); ?></td>
                                <td class="px-4 py-2 text-right font-semibold">AED <?php echo number_format($txn->total_amount, 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-4 py-4 text-center text-gray-500">No input VAT transactions</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 pt-4 border-t text-center text-sm text-gray-500">
            Generated on <?php echo date('d/m/Y H:i:s'); ?> | This report is for informational purposes only
        </div>
    </div>
</div>
