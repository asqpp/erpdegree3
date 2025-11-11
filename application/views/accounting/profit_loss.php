<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-chart-line mr-2"></i>Profit & Loss Statement
        </h1>
        <div class="flex gap-2">
            <a href="<?php echo base_url('accounting/export_profit_loss'); ?>" class="bg-green-600 text-white px-4 py-2 rounded">
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
                <input type="date" name="date_from" value="<?php echo $filters['date_from'] ?? date('Y-01-01'); ?>" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                <input type="date" name="date_to" value="<?php echo $filters['date_to'] ?? date('Y-m-d'); ?>" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Quick Select</label>
                <select name="period" class="w-full border rounded px-3 py-2" onchange="this.form.submit()">
                    <option value="">Custom</option>
                    <option value="this_month">This Month</option>
                    <option value="last_month">Last Month</option>
                    <option value="this_quarter">This Quarter</option>
                    <option value="this_year">This Year</option>
                    <option value="last_year">Last Year</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded">Generate</button>
            </div>
        </form>
    </div>

    <!-- Profit & Loss Report -->
    <div class="bg-white rounded-lg shadow p-8">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold"><?php echo $company_settings->company_name ?? 'Insurance Company Ltd'; ?></h2>
            <h3 class="text-xl font-semibold mt-2">Profit & Loss Statement</h3>
            <p class="text-gray-600 mt-2">
                For the period from <?php echo date('d/m/Y', strtotime($filters['date_from'])); ?>
                to <?php echo date('d/m/Y', strtotime($filters['date_to'])); ?>
            </p>
        </div>

        <!-- Revenue Section -->
        <div class="mb-8">
            <div class="bg-green-50 border-l-4 border-green-600 px-4 py-2 mb-4">
                <h3 class="text-lg font-bold text-green-800">REVENUE</h3>
            </div>
            <table class="w-full">
                <?php
                $total_revenue = 0;
                if(isset($revenue_accounts)):
                    foreach($revenue_accounts as $account):
                        $total_revenue += $account->balance;
                ?>
                <tr class="border-b">
                    <td class="py-2 pl-8"><?php echo $account->account_name; ?></td>
                    <td class="py-2 text-right pr-8">AED <?php echo number_format($account->balance, 2); ?></td>
                </tr>
                <?php
                    endforeach;
                endif;
                ?>
                <tr class="font-bold bg-green-50">
                    <td class="py-3 pl-8">TOTAL REVENUE</td>
                    <td class="py-3 text-right pr-8">AED <?php echo number_format($total_revenue, 2); ?></td>
                </tr>
            </table>
        </div>

        <!-- Expenses Section -->
        <div class="mb-8">
            <div class="bg-red-50 border-l-4 border-red-600 px-4 py-2 mb-4">
                <h3 class="text-lg font-bold text-red-800">EXPENSES</h3>
            </div>
            <table class="w-full">
                <?php
                $total_expenses = 0;
                $expense_groups = [];

                // Group expenses by subgroup
                if(isset($expense_accounts)):
                    foreach($expense_accounts as $account):
                        $subgroup = $account->account_subgroup ?: 'Other Expenses';
                        if(!isset($expense_groups[$subgroup])) {
                            $expense_groups[$subgroup] = [];
                        }
                        $expense_groups[$subgroup][] = $account;
                        $total_expenses += $account->balance;
                    endforeach;

                    // Display grouped expenses
                    foreach($expense_groups as $subgroup => $accounts):
                        $subgroup_total = array_sum(array_column($accounts, 'balance'));
                ?>
                <tr>
                    <td colspan="2" class="py-2 pl-4 font-semibold text-gray-700"><?php echo $subgroup; ?></td>
                </tr>
                <?php foreach($accounts as $account): ?>
                <tr class="border-b">
                    <td class="py-2 pl-12"><?php echo $account->account_name; ?></td>
                    <td class="py-2 text-right pr-8">AED <?php echo number_format($account->balance, 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="bg-gray-50">
                    <td class="py-2 pl-8 font-semibold">Subtotal - <?php echo $subgroup; ?></td>
                    <td class="py-2 text-right pr-8 font-semibold">AED <?php echo number_format($subgroup_total, 2); ?></td>
                </tr>
                <?php
                    endforeach;
                endif;
                ?>
                <tr class="font-bold bg-red-50">
                    <td class="py-3 pl-8">TOTAL EXPENSES</td>
                    <td class="py-3 text-right pr-8">AED <?php echo number_format($total_expenses, 2); ?></td>
                </tr>
            </table>
        </div>

        <!-- Net Profit/Loss -->
        <?php $net_profit = $total_revenue - $total_expenses; ?>
        <div class="border-t-4 border-gray-800 pt-6">
            <div class="flex justify-between items-center text-2xl font-bold">
                <span><?php echo $net_profit >= 0 ? 'NET PROFIT' : 'NET LOSS'; ?></span>
                <span class="<?php echo $net_profit >= 0 ? 'text-green-600' : 'text-red-600'; ?>">
                    AED <?php echo number_format(abs($net_profit), 2); ?>
                </span>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="mt-8 pt-8 border-t grid grid-cols-3 gap-4 text-sm">
            <div class="text-center">
                <div class="text-gray-600">Gross Profit Margin</div>
                <div class="text-xl font-bold text-blue-600">
                    <?php echo $total_revenue > 0 ? number_format(($total_revenue - $total_expenses) / $total_revenue * 100, 1) : 0; ?>%
                </div>
            </div>
            <div class="text-center">
                <div class="text-gray-600">Expense Ratio</div>
                <div class="text-xl font-bold text-orange-600">
                    <?php echo $total_revenue > 0 ? number_format($total_expenses / $total_revenue * 100, 1) : 0; ?>%
                </div>
            </div>
            <div class="text-center">
                <div class="text-gray-600">Net Profit Margin</div>
                <div class="text-xl font-bold text-green-600">
                    <?php echo $total_revenue > 0 ? number_format($net_profit / $total_revenue * 100, 1) : 0; ?>%
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 pt-4 border-t text-center text-sm text-gray-500">
            Generated on <?php echo date('d/m/Y H:i:s'); ?>
        </div>
    </div>
</div>
