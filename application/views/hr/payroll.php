<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-money-check-alt mr-2"></i>Payroll Management
        </h1>
        <a href="<?php echo base_url('hr/process_payroll'); ?>" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-calculator mr-2"></i>Process Payroll
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Total Payroll (This Month)</div>
            <div class="text-3xl font-bold text-blue-600">AED <?php echo number_format($summary['total_payroll'] ?? 0, 2); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Basic Salaries</div>
            <div class="text-3xl font-bold text-green-600">AED <?php echo number_format($summary['basic_salaries'] ?? 0, 2); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Allowances</div>
            <div class="text-3xl font-bold text-purple-600">AED <?php echo number_format($summary['allowances'] ?? 0, 2); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Deductions</div>
            <div class="text-3xl font-bold text-red-600">AED <?php echo number_format($summary['deductions'] ?? 0, 2); ?></div>
        </div>
    </div>

    <!-- Period Selector -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form method="get" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                <select name="month" class="w-full border rounded px-3 py-2">
                    <?php for($m = 1; $m <= 12; $m++): ?>
                    <option value="<?php echo $m; ?>" <?php echo (isset($filters['month']) && $filters['month'] == $m) || (!isset($filters['month']) && $m == date('n')) ? 'selected' : ''; ?>>
                        <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                    </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                <select name="year" class="w-full border rounded px-3 py-2">
                    <?php for($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                    <option value="<?php echo $y; ?>" <?php echo (isset($filters['year']) && $filters['year'] == $y) || (!isset($filters['year']) && $y == date('Y')) ? 'selected' : ''; ?>>
                        <?php echo $y; ?>
                    </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded">Generate</button>
            </div>

            <div class="flex items-end">
                <a href="<?php echo base_url('hr/export_payroll'); ?>" class="w-full bg-green-600 text-white px-4 py-2 rounded text-center">Export CSV</a>
            </div>
        </form>
    </div>

    <!-- Payroll Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Basic Salary</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Allowances</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Gross</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Deductions</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Net Salary</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if(isset($payroll_records) && count($payroll_records) > 0): ?>
                    <?php foreach($payroll_records as $payroll): ?>
                    <?php
                    $allowances = ($payroll->housing_allowance ?? 0) + ($payroll->transport_allowance ?? 0);
                    $gross_salary = $payroll->basic_salary + $allowances;
                    $deductions = $payroll->deductions ?? 0;
                    $net_salary = $gross_salary - $deductions;
                    ?>
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-semibold"><?php echo $payroll->full_name; ?></div>
                            <div class="text-xs text-gray-500"><?php echo $payroll->employee_code; ?></div>
                        </td>
                        <td class="px-6 py-4"><?php echo $payroll->department_name; ?></td>
                        <td class="px-6 py-4 text-right">AED <?php echo number_format($payroll->basic_salary, 2); ?></td>
                        <td class="px-6 py-4 text-right text-purple-600">AED <?php echo number_format($allowances, 2); ?></td>
                        <td class="px-6 py-4 text-right font-semibold">AED <?php echo number_format($gross_salary, 2); ?></td>
                        <td class="px-6 py-4 text-right text-red-600">AED <?php echo number_format($deductions, 2); ?></td>
                        <td class="px-6 py-4 text-right text-xl font-bold text-green-600">AED <?php echo number_format($net_salary, 2); ?></td>
                        <td class="px-6 py-4">
                            <?php if(isset($payroll->payroll_status)): ?>
                                <?php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'processed' => 'bg-blue-100 text-blue-800',
                                    'paid' => 'bg-green-100 text-green-800'
                                ];
                                $statusColor = $statusColors[$payroll->payroll_status] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="px-2 py-1 <?php echo $statusColor; ?> rounded text-xs"><?php echo ucfirst($payroll->payroll_status); ?></span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Not Processed</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if(isset($payroll->payroll_id)): ?>
                                <a href="<?php echo base_url('hr/view_payslip/'.$payroll->payroll_id); ?>" class="text-blue-600 hover:text-blue-800 mr-3">View</a>
                                <a href="<?php echo base_url('hr/print_payslip/'.$payroll->payroll_id); ?>" class="text-green-600 hover:text-green-800">Print</a>
                            <?php else: ?>
                                <span class="text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <!-- Total Row -->
                    <tr class="bg-gray-800 text-white font-bold">
                        <td class="px-6 py-4" colspan="2">TOTAL</td>
                        <td class="px-6 py-4 text-right">AED <?php echo number_format(array_sum(array_column($payroll_records, 'basic_salary')), 2); ?></td>
                        <td class="px-6 py-4 text-right">AED <?php echo number_format($summary['allowances'] ?? 0, 2); ?></td>
                        <td class="px-6 py-4 text-right">AED <?php echo number_format(($summary['basic_salaries'] ?? 0) + ($summary['allowances'] ?? 0), 2); ?></td>
                        <td class="px-6 py-4 text-right">AED <?php echo number_format($summary['deductions'] ?? 0, 2); ?></td>
                        <td class="px-6 py-4 text-right text-2xl">AED <?php echo number_format($summary['total_payroll'] ?? 0, 2); ?></td>
                        <td class="px-6 py-4" colspan="2"></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center text-gray-500">No payroll records found for this period</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Payroll History -->
    <?php if(isset($payroll_history) && count($payroll_history) > 0): ?>
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Payroll History</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Employees</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total Amount</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Processed Date</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    <?php foreach($payroll_history as $history): ?>
                    <tr>
                        <td class="px-4 py-2 font-semibold"><?php echo date('F Y', mktime(0, 0, 0, $history->month, 1, $history->year)); ?></td>
                        <td class="px-4 py-2 text-right"><?php echo $history->employee_count; ?></td>
                        <td class="px-4 py-2 text-right font-bold text-green-600">AED <?php echo number_format($history->total_amount, 2); ?></td>
                        <td class="px-4 py-2"><?php echo date('d/m/Y', strtotime($history->processed_date)); ?></td>
                        <td class="px-4 py-2">
                            <a href="<?php echo base_url('hr/payroll?month='.$history->month.'&year='.$history->year); ?>" class="text-blue-600 hover:text-blue-800">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
