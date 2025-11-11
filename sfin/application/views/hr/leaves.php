<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-calendar-alt mr-2"></i>Leave Management
        </h1>
        <a href="<?php echo base_url('hr/apply_leave'); ?>" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>Apply Leave
        </a>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Pending Requests</div>
            <div class="text-3xl font-bold text-yellow-600"><?php echo number_format($statistics['pending'] ?? 0); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Approved</div>
            <div class="text-3xl font-bold text-green-600"><?php echo number_format($statistics['approved'] ?? 0); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Rejected</div>
            <div class="text-3xl font-bold text-red-600"><?php echo number_format($statistics['rejected'] ?? 0); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Employees on Leave</div>
            <div class="text-3xl font-bold text-blue-600"><?php echo number_format($statistics['on_leave_now'] ?? 0); ?></div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form method="get" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <select name="employee_id" class="border rounded px-3 py-2">
                <option value="">All Employees</option>
                <?php if(isset($employees)): foreach($employees as $emp): ?>
                <option value="<?php echo $emp->employee_id; ?>" <?php echo (isset($filters['employee_id']) && $filters['employee_id'] == $emp->employee_id) ? 'selected' : ''; ?>>
                    <?php echo $emp->full_name; ?>
                </option>
                <?php endforeach; endif; ?>
            </select>

            <select name="leave_type" class="border rounded px-3 py-2">
                <option value="">All Leave Types</option>
                <option value="annual">Annual Leave</option>
                <option value="sick">Sick Leave</option>
                <option value="emergency">Emergency Leave</option>
                <option value="unpaid">Unpaid Leave</option>
            </select>

            <select name="status" class="border rounded px-3 py-2">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
            <a href="<?php echo base_url('hr/export_leaves'); ?>" class="bg-green-600 text-white px-4 py-2 rounded text-center">Export CSV</a>
        </form>
    </div>

    <!-- Leave Requests Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Leave Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Days</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if(isset($leave_requests) && count($leave_requests) > 0): ?>
                    <?php foreach($leave_requests as $leave): ?>
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-semibold"><?php echo $leave->employee_name; ?></div>
                            <div class="text-xs text-gray-500"><?php echo $leave->department_name; ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <?php
                            $leaveTypeColors = [
                                'annual' => 'bg-blue-100 text-blue-800',
                                'sick' => 'bg-red-100 text-red-800',
                                'emergency' => 'bg-orange-100 text-orange-800',
                                'unpaid' => 'bg-gray-100 text-gray-800'
                            ];
                            $typeColor = $leaveTypeColors[$leave->leave_type] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="px-2 py-1 <?php echo $typeColor; ?> rounded text-xs font-semibold">
                                <?php echo ucfirst($leave->leave_type); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4"><?php echo date('d/m/Y', strtotime($leave->start_date)); ?></td>
                        <td class="px-6 py-4"><?php echo date('d/m/Y', strtotime($leave->end_date)); ?></td>
                        <td class="px-6 py-4 font-semibold"><?php echo $leave->total_days; ?> days</td>
                        <td class="px-6 py-4 text-sm"><?php echo substr($leave->reason, 0, 50) . (strlen($leave->reason) > 50 ? '...' : ''); ?></td>
                        <td class="px-6 py-4">
                            <?php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'approved' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-800'
                            ];
                            $statusColor = $statusColors[$leave->status] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="px-2 py-1 <?php echo $statusColor; ?> rounded text-xs">
                                <?php echo ucfirst($leave->status); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <?php if($leave->status == 'pending'): ?>
                                <a href="<?php echo base_url('hr/approve_leave/'.$leave->leave_id); ?>"
                                   class="text-green-600 hover:text-green-800 mr-3"
                                   onclick="return confirm('Approve this leave request?')">Approve</a>
                                <a href="<?php echo base_url('hr/reject_leave/'.$leave->leave_id); ?>"
                                   class="text-red-600 hover:text-red-800"
                                   onclick="return confirm('Reject this leave request?')">Reject</a>
                            <?php else: ?>
                                <a href="<?php echo base_url('hr/view_leave/'.$leave->leave_id); ?>" class="text-blue-600 hover:text-blue-800">View</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">No leave requests found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Leave Balance Summary -->
    <?php if(isset($leave_balances) && count($leave_balances) > 0): ?>
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Employee Leave Balances</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <?php foreach($leave_balances as $balance): ?>
            <div class="border rounded p-4">
                <div class="font-semibold text-sm mb-2"><?php echo $balance->employee_name; ?></div>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <div class="text-gray-500">Annual</div>
                        <div class="font-bold text-blue-600"><?php echo $balance->annual_balance; ?> days</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Sick</div>
                        <div class="font-bold text-red-600"><?php echo $balance->sick_balance; ?> days</div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
