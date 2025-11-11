<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-users-cog mr-2"></i>Role Permissions
        </h1>
        <a href="<?php echo base_url('user_permissions/list'); ?>" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to Users
        </a>
    </div>

    <!-- Roles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php
        $roles = [
            ['name' => 'admin', 'label' => 'Administrator', 'color' => 'red', 'icon' => 'fa-user-shield', 'description' => 'Full system access'],
            ['name' => 'manager', 'label' => 'Manager', 'color' => 'purple', 'icon' => 'fa-user-tie', 'description' => 'Management access'],
            ['name' => 'accountant', 'label' => 'Accountant', 'color' => 'green', 'icon' => 'fa-calculator', 'description' => 'Financial access'],
            ['name' => 'employee', 'label' => 'Employee', 'color' => 'blue', 'icon' => 'fa-user', 'description' => 'Basic access']
        ];

        foreach($roles as $role):
            // Count users with this role
            $user_count = 0;
            if(isset($role_users[$role['name']])) {
                $user_count = $role_users[$role['name']];
            }
        ?>
        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
            <div class="bg-<?php echo $role['color']; ?>-600 text-white p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <i class="fas <?php echo $role['icon']; ?> text-4xl mb-2"></i>
                        <h3 class="text-xl font-bold"><?php echo $role['label']; ?></h3>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold"><?php echo $user_count; ?></div>
                        <div class="text-sm opacity-90">Users</div>
                    </div>
                </div>
                <p class="text-sm mt-2 opacity-90"><?php echo $role['description']; ?></p>
            </div>

            <div class="p-6">
                <?php if(isset($role_permissions[$role['name']])): ?>
                    <?php
                    $perms = $role_permissions[$role['name']];
                    $modules_with_access = count($perms);
                    ?>
                    <div class="mb-4">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600">Module Access</span>
                            <span class="font-semibold"><?php echo $modules_with_access; ?>/14</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-<?php echo $role['color']; ?>-600 h-2 rounded-full" style="width: <?php echo ($modules_with_access / 14) * 100; ?>%"></div>
                        </div>
                    </div>
                <?php endif; ?>

                <a href="<?php echo base_url('user_permissions/manage_role/'.$role['name']); ?>"
                   class="block w-full text-center bg-<?php echo $role['color']; ?>-600 text-white px-4 py-2 rounded hover:bg-<?php echo $role['color']; ?>-700 transition">
                    <i class="fas fa-edit mr-2"></i>Manage Permissions
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Quick Overview -->
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Permission Overview</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Module</th>
                        <th class="px-4 py-3 text-center font-semibold text-red-700">Admin</th>
                        <th class="px-4 py-3 text-center font-semibold text-purple-700">Manager</th>
                        <th class="px-4 py-3 text-center font-semibold text-green-700">Accountant</th>
                        <th class="px-4 py-3 text-center font-semibold text-blue-700">Employee</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php
                    $modules = ['Customers', 'Policies', 'Claims', 'Sales', 'Receipts', 'Payments',
                                'Debit Notes', 'Credit Notes', 'Accounting', 'Reports', 'HR', 'Settings', 'Users', 'Backup'];

                    foreach($modules as $module):
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium"><?php echo $module; ?></td>
                        <?php foreach(['admin', 'manager', 'accountant', 'employee'] as $role): ?>
                        <td class="px-4 py-3 text-center">
                            <?php
                            $has_access = false;
                            if(isset($role_permissions[$role])) {
                                foreach($role_permissions[$role] as $perm) {
                                    if($perm->module_name == $module && $perm->can_view) {
                                        $has_access = true;
                                        break;
                                    }
                                }
                            }
                            ?>
                            <?php if($has_access): ?>
                                <i class="fas fa-check-circle text-green-600"></i>
                            <?php else: ?>
                                <i class="fas fa-times-circle text-gray-300"></i>
                            <?php endif; ?>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
