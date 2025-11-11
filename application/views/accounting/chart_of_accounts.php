<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-book mr-2"></i>Chart of Accounts
        </h1>
        <a href="<?php echo base_url('accounting/add_account'); ?>" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>New Account
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form method="get" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <input type="text" name="search" placeholder="Search account..." class="border rounded px-3 py-2" value="<?php echo $filters['search'] ?? ''; ?>">

            <select name="account_type" class="border rounded px-3 py-2">
                <option value="">All Types</option>
                <option value="asset">Asset</option>
                <option value="liability">Liability</option>
                <option value="equity">Equity</option>
                <option value="revenue">Revenue</option>
                <option value="expense">Expense</option>
            </select>

            <select name="account_subgroup" class="border rounded px-3 py-2">
                <option value="">All Subgroups</option>
                <option value="Current Assets">Current Assets</option>
                <option value="Fixed Assets">Fixed Assets</option>
                <option value="Current Liabilities">Current Liabilities</option>
                <option value="Long-term Liabilities">Long-term Liabilities</option>
            </select>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
            <a href="<?php echo base_url('accounting/export_accounts'); ?>" class="bg-green-600 text-white px-4 py-2 rounded text-center">Export CSV</a>
        </form>
    </div>

    <!-- Accounts by Type -->
    <?php
    $account_types = [
        'asset' => ['name' => 'Assets', 'color' => 'blue'],
        'liability' => ['name' => 'Liabilities', 'color' => 'red'],
        'equity' => ['name' => 'Equity', 'color' => 'purple'],
        'revenue' => ['name' => 'Revenue', 'color' => 'green'],
        'expense' => ['name' => 'Expenses', 'color' => 'orange']
    ];

    foreach($account_types as $type => $config):
        $type_accounts = array_filter($accounts, function($acc) use ($type) {
            return strtolower($acc->account_type) == $type;
        });
        if(count($type_accounts) == 0) continue;
    ?>
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="bg-<?php echo $config['color']; ?>-600 text-white px-6 py-3 rounded-t-lg">
            <h2 class="text-xl font-bold"><?php echo $config['name']; ?></h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subgroup</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Currency</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach($type_accounts as $account): ?>
                    <tr>
                        <td class="px-6 py-4 font-mono"><?php echo $account->account_code; ?></td>
                        <td class="px-6 py-4 font-semibold"><?php echo $account->account_name; ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?php echo $account->account_subgroup ?: '-'; ?></td>
                        <td class="px-6 py-4"><?php echo $account->currency; ?></td>
                        <td class="px-6 py-4 text-right font-semibold">
                            <?php echo $account->currency; ?> <?php echo number_format($account->current_balance, 2); ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if($account->is_active): ?>
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Active</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <a href="<?php echo base_url('accounting/account_ledger/'.$account->account_id); ?>" class="text-blue-600 hover:text-blue-800 mr-3">Ledger</a>
                            <a href="<?php echo base_url('accounting/edit_account/'.$account->account_id); ?>" class="text-green-600 hover:text-green-800">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endforeach; ?>
</div>
