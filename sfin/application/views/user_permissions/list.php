<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-user-shield mr-2"></i>User Permissions
        </h1>
        <a href="<?php echo base_url('user_permissions/roles'); ?>" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700">
            <i class="fas fa-users-cog mr-2"></i>Manage Roles
        </a>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Total Users</div>
            <div class="text-3xl font-bold text-blue-600"><?php echo number_format($statistics['total_users'] ?? 0); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Active Users</div>
            <div class="text-3xl font-bold text-green-600"><?php echo number_format($statistics['active_users'] ?? 0); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Roles</div>
            <div class="text-3xl font-bold text-purple-600"><?php echo number_format($statistics['total_roles'] ?? 0); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Modules</div>
            <div class="text-3xl font-bold text-orange-600"><?php echo number_format($statistics['total_modules'] ?? 14); ?></div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Login</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if(isset($users) && count($users) > 0): ?>
                    <?php foreach($users as $user): ?>
                    <tr>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">
                                        <?php echo strtoupper(substr($user->full_name, 0, 2)); ?>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="font-semibold text-gray-900"><?php echo $user->full_name; ?></div>
                                    <div class="text-sm text-gray-500"><?php echo $user->email; ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 <?php
                            $roleColors = [
                                'admin' => 'bg-red-100 text-red-800',
                                'manager' => 'bg-purple-100 text-purple-800',
                                'employee' => 'bg-blue-100 text-blue-800',
                                'accountant' => 'bg-green-100 text-green-800'
                            ];
                            echo $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800';
                            ?> rounded text-xs font-semibold">
                                <?php echo ucfirst($user->role); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4"><?php echo $user->department_name ?? '-'; ?></td>
                        <td class="px-6 py-4 text-sm">
                            <?php echo $user->last_login ? date('d/m/Y H:i', strtotime($user->last_login)) : 'Never'; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if($user->is_active): ?>
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Active</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <a href="<?php echo base_url('user_permissions/manage/'.$user->user_id); ?>" class="text-blue-600 hover:text-blue-800 mr-3">
                                <i class="fas fa-lock mr-1"></i>Permissions
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">No users found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
