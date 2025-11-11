<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-balance-scale mr-2"></i>Balance Sheet
        </h1>
        <div class="flex gap-2">
            <a href="<?php echo base_url('accounting/export_balance_sheet'); ?>" class="bg-green-600 text-white px-4 py-2 rounded">
                <i class="fas fa-file-excel mr-2"></i>Export
            </a>
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded">
                <i class="fas fa-print mr-2"></i>Print
            </button>
        </div>
    </div>

    <!-- As of Date Selector -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form method="get" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">As of Date</label>
                <input type="date" name="as_of_date" value="<?php echo $as_of_date ?? date('Y-m-d'); ?>" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Quick Select</label>
                <select name="period" class="w-full border rounded px-3 py-2" onchange="this.form.submit()">
                    <option value="">Custom</option>
                    <option value="today">Today</option>
                    <option value="month_end">End of Month</option>
                    <option value="quarter_end">End of Quarter</option>
                    <option value="year_end">End of Year</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded">Generate</button>
            </div>
        </form>
    </div>

    <!-- Balance Sheet -->
    <div class="bg-white rounded-lg shadow p-8">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold"><?php echo $company_settings->company_name ?? 'Insurance Company Ltd'; ?></h2>
            <h3 class="text-xl font-semibold mt-2">Balance Sheet</h3>
            <p class="text-gray-600 mt-2">As of <?php echo date('d/m/Y', strtotime($as_of_date ?? date('Y-m-d'))); ?></p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Left Column: Assets -->
            <div>
                <div class="bg-blue-50 border-l-4 border-blue-600 px-4 py-2 mb-4">
                    <h3 class="text-lg font-bold text-blue-800">ASSETS</h3>
                </div>

                <!-- Current Assets -->
                <div class="mb-6">
                    <div class="font-semibold text-gray-700 mb-2">Current Assets</div>
                    <table class="w-full">
                        <?php
                        $current_assets_total = 0;
                        if(isset($current_assets)):
                            foreach($current_assets as $account):
                                $current_assets_total += $account->balance;
                        ?>
                        <tr class="border-b">
                            <td class="py-2 pl-4"><?php echo $account->account_name; ?></td>
                            <td class="py-2 text-right">AED <?php echo number_format($account->balance, 2); ?></td>
                        </tr>
                        <?php
                            endforeach;
                        endif;
                        ?>
                        <tr class="font-semibold bg-gray-50">
                            <td class="py-2 pl-4">Total Current Assets</td>
                            <td class="py-2 text-right">AED <?php echo number_format($current_assets_total, 2); ?></td>
                        </tr>
                    </table>
                </div>

                <!-- Fixed Assets -->
                <div class="mb-6">
                    <div class="font-semibold text-gray-700 mb-2">Fixed Assets</div>
                    <table class="w-full">
                        <?php
                        $fixed_assets_total = 0;
                        if(isset($fixed_assets)):
                            foreach($fixed_assets as $account):
                                $fixed_assets_total += $account->balance;
                        ?>
                        <tr class="border-b">
                            <td class="py-2 pl-4"><?php echo $account->account_name; ?></td>
                            <td class="py-2 text-right">AED <?php echo number_format($account->balance, 2); ?></td>
                        </tr>
                        <?php
                            endforeach;
                        endif;
                        ?>
                        <tr class="font-semibold bg-gray-50">
                            <td class="py-2 pl-4">Total Fixed Assets</td>
                            <td class="py-2 text-right">AED <?php echo number_format($fixed_assets_total, 2); ?></td>
                        </tr>
                    </table>
                </div>

                <!-- Total Assets -->
                <?php $total_assets = $current_assets_total + $fixed_assets_total; ?>
                <div class="border-t-2 border-blue-600 pt-4">
                    <table class="w-full">
                        <tr class="text-lg font-bold">
                            <td class="py-2">TOTAL ASSETS</td>
                            <td class="py-2 text-right text-blue-600">AED <?php echo number_format($total_assets, 2); ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Right Column: Liabilities & Equity -->
            <div>
                <!-- Liabilities -->
                <div class="bg-red-50 border-l-4 border-red-600 px-4 py-2 mb-4">
                    <h3 class="text-lg font-bold text-red-800">LIABILITIES</h3>
                </div>

                <!-- Current Liabilities -->
                <div class="mb-6">
                    <div class="font-semibold text-gray-700 mb-2">Current Liabilities</div>
                    <table class="w-full">
                        <?php
                        $current_liabilities_total = 0;
                        if(isset($current_liabilities)):
                            foreach($current_liabilities as $account):
                                $current_liabilities_total += $account->balance;
                        ?>
                        <tr class="border-b">
                            <td class="py-2 pl-4"><?php echo $account->account_name; ?></td>
                            <td class="py-2 text-right">AED <?php echo number_format($account->balance, 2); ?></td>
                        </tr>
                        <?php
                            endforeach;
                        endif;
                        ?>
                        <tr class="font-semibold bg-gray-50">
                            <td class="py-2 pl-4">Total Current Liabilities</td>
                            <td class="py-2 text-right">AED <?php echo number_format($current_liabilities_total, 2); ?></td>
                        </tr>
                    </table>
                </div>

                <!-- Long-term Liabilities -->
                <div class="mb-6">
                    <div class="font-semibold text-gray-700 mb-2">Long-term Liabilities</div>
                    <table class="w-full">
                        <?php
                        $longterm_liabilities_total = 0;
                        if(isset($longterm_liabilities)):
                            foreach($longterm_liabilities as $account):
                                $longterm_liabilities_total += $account->balance;
                        ?>
                        <tr class="border-b">
                            <td class="py-2 pl-4"><?php echo $account->account_name; ?></td>
                            <td class="py-2 text-right">AED <?php echo number_format($account->balance, 2); ?></td>
                        </tr>
                        <?php
                            endforeach;
                        endif;
                        ?>
                        <tr class="font-semibold bg-gray-50">
                            <td class="py-2 pl-4">Total Long-term Liabilities</td>
                            <td class="py-2 text-right">AED <?php echo number_format($longterm_liabilities_total, 2); ?></td>
                        </tr>
                    </table>
                </div>

                <?php $total_liabilities = $current_liabilities_total + $longterm_liabilities_total; ?>

                <!-- Equity -->
                <div class="bg-purple-50 border-l-4 border-purple-600 px-4 py-2 mb-4 mt-8">
                    <h3 class="text-lg font-bold text-purple-800">EQUITY</h3>
                </div>

                <div class="mb-6">
                    <table class="w-full">
                        <?php
                        $equity_total = 0;
                        if(isset($equity_accounts)):
                            foreach($equity_accounts as $account):
                                $equity_total += $account->balance;
                        ?>
                        <tr class="border-b">
                            <td class="py-2 pl-4"><?php echo $account->account_name; ?></td>
                            <td class="py-2 text-right">AED <?php echo number_format($account->balance, 2); ?></td>
                        </tr>
                        <?php
                            endforeach;
                        endif;
                        ?>
                        <tr class="font-semibold bg-gray-50">
                            <td class="py-2 pl-4">Total Equity</td>
                            <td class="py-2 text-right">AED <?php echo number_format($equity_total, 2); ?></td>
                        </tr>
                    </table>
                </div>

                <!-- Total Liabilities & Equity -->
                <?php $total_liabilities_equity = $total_liabilities + $equity_total; ?>
                <div class="border-t-2 border-purple-600 pt-4">
                    <table class="w-full">
                        <tr class="text-lg font-bold">
                            <td class="py-2">TOTAL LIABILITIES & EQUITY</td>
                            <td class="py-2 text-right text-purple-600">AED <?php echo number_format($total_liabilities_equity, 2); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Balance Check -->
        <?php $difference = abs($total_assets - $total_liabilities_equity); ?>
        <div class="mt-8 pt-6 border-t text-center">
            <?php if($difference < 0.01): ?>
                <div class="bg-green-50 border border-green-200 rounded p-4">
                    <i class="fas fa-check-circle text-green-600 text-2xl mb-2"></i>
                    <p class="text-green-800 font-semibold">Balance Sheet is in balance!</p>
                </div>
            <?php else: ?>
                <div class="bg-red-50 border border-red-200 rounded p-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl mb-2"></i>
                    <p class="text-red-800 font-semibold">Out of Balance: AED <?php echo number_format($difference, 2); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div class="mt-8 pt-4 border-t text-center text-sm text-gray-500">
            Generated on <?php echo date('d/m/Y H:i:s'); ?>
        </div>
    </div>
</div>
