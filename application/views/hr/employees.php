<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-users mr-2"></i>Employees
        </h1>
        <a href="<?php echo base_url('hr/add_employee'); ?>" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-user-plus mr-2"></i>New Employee
        </a>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Total Employees</div>
            <div class="text-3xl font-bold text-blue-600"><?php echo number_format($statistics['total_employees'] ?? 0); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Active</div>
            <div class="text-3xl font-bold text-green-600"><?php echo number_format($statistics['active_employees'] ?? 0); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">On Leave</div>
            <div class="text-3xl font-bold text-yellow-600"><?php echo number_format($statistics['on_leave'] ?? 0); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Departments</div>
            <div class="text-3xl font-bold text-purple-600"><?php echo number_format($statistics['departments'] ?? 0); ?></div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form method="get" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <input type="text" name="search" placeholder="Search name, email, ID..." class="border rounded px-3 py-2" value="<?php echo $filters['search'] ?? ''; ?>">

            <select name="department_id" class="border rounded px-3 py-2">
                <option value="">All Departments</option>
                <?php if(isset($departments)): foreach($departments as $dept): ?>
                <option value="<?php echo $dept->department_id; ?>" <?php echo (isset($filters['department_id']) && $filters['department_id'] == $dept->department_id) ? 'selected' : ''; ?>>
                    <?php echo $dept->department_name; ?>
                </option>
                <?php endforeach; endif; ?>
            </select>

            <select name="status" class="border rounded px-3 py-2">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="on_leave">On Leave</option>
                <option value="inactive">Inactive</option>
            </select>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
            <a href="<?php echo base_url('hr/export_employees'); ?>" class="bg-green-600 text-white px-4 py-2 rounded text-center">Export CSV</a>
        </form>
    </div>

    <!-- Employees Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Position</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Join Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Salary</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if(isset($employees) && count($employees) > 0): ?>
                    <?php foreach($employees as $employee): ?>
                    <tr>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">
                                        <?php echo strtoupper(substr($employee->full_name, 0, 2)); ?>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="font-semibold text-gray-900"><?php echo $employee->full_name; ?></div>
                                    <div class="text-sm text-gray-500"><?php echo $employee->email; ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-mono text-sm"><?php echo $employee->employee_code; ?></td>
                        <td class="px-6 py-4"><?php echo $employee->department_name; ?></td>
                        <td class="px-6 py-4"><?php echo $employee->position; ?></td>
                        <td class="px-6 py-4"><?php echo date('d/m/Y', strtotime($employee->join_date)); ?></td>
                        <td class="px-6 py-4 font-semibold">AED <?php echo number_format($employee->basic_salary, 0); ?></td>
                        <td class="px-6 py-4">
                            <?php
                            $statusColors = [
                                'active' => 'bg-green-100 text-green-800',
                                'on_leave' => 'bg-yellow-100 text-yellow-800',
                                'inactive' => 'bg-gray-100 text-gray-800'
                            ];
                            $colorClass = $statusColors[$employee->status] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="px-2 py-1 <?php echo $colorClass; ?> rounded text-xs"><?php echo ucfirst(str_replace('_', ' ', $employee->status)); ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="<?php echo base_url('hr/view_employee/'.$employee->employee_id); ?>" class="text-blue-600 hover:text-blue-800 mr-3">View</a>
                            <a href="<?php echo base_url('hr/edit_employee/'.$employee->employee_id); ?>" class="text-green-600 hover:text-green-800">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">No employees found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
