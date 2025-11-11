<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-users-cog mr-2"></i>Manage Role Permissions
            </h1>
            <p class="text-gray-600 mt-2">Role: <strong><?php echo ucfirst($role); ?></strong></p>
        </div>
        <a href="<?php echo base_url('user_permissions/roles'); ?>" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <?php echo form_open('user_permissions/save_role/'.$role); ?>

        <div class="mb-6 bg-purple-50 border-l-4 border-purple-600 p-4">
            <p class="text-sm text-purple-800">
                <i class="fas fa-info-circle mr-2"></i>
                Role permissions apply to all users with this role by default. Users can have custom permissions that override role permissions.
            </p>
        </div>

        <!-- Modules Permissions -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Module</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold">
                            <div><i class="fas fa-eye mb-1"></i></div>
                            <div class="text-xs">View</div>
                        </th>
                        <th class="px-6 py-3 text-center text-sm font-semibold">
                            <div><i class="fas fa-plus mb-1"></i></div>
                            <div class="text-xs">Create</div>
                        </th>
                        <th class="px-6 py-3 text-center text-sm font-semibold">
                            <div><i class="fas fa-edit mb-1"></i></div>
                            <div class="text-xs">Edit</div>
                        </th>
                        <th class="px-6 py-3 text-center text-sm font-semibold">
                            <div><i class="fas fa-trash mb-1"></i></div>
                            <div class="text-xs">Delete</div>
                        </th>
                        <th class="px-6 py-3 text-center text-sm font-semibold">
                            <div><i class="fas fa-check-circle mb-1"></i></div>
                            <div class="text-xs">Approve</div>
                        </th>
                        <th class="px-6 py-3 text-center text-sm font-semibold">
                            <div><i class="fas fa-file-export mb-1"></i></div>
                            <div class="text-xs">Export</div>
                        </th>
                        <th class="px-6 py-3 text-center text-sm font-semibold">
                            <div><i class="fas fa-check-double mb-1"></i></div>
                            <div class="text-xs">All</div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    $modules = [
                        ['name' => 'Customers', 'icon' => 'fa-users'],
                        ['name' => 'Policies', 'icon' => 'fa-file-contract'],
                        ['name' => 'Claims', 'icon' => 'fa-clipboard-list'],
                        ['name' => 'Sales', 'icon' => 'fa-chart-line'],
                        ['name' => 'Receipts', 'icon' => 'fa-money-check-alt'],
                        ['name' => 'Payments', 'icon' => 'fa-money-bill-wave'],
                        ['name' => 'Debit Notes', 'icon' => 'fa-file-invoice'],
                        ['name' => 'Credit Notes', 'icon' => 'fa-file-invoice-dollar'],
                        ['name' => 'Accounting', 'icon' => 'fa-balance-scale'],
                        ['name' => 'Reports', 'icon' => 'fa-chart-bar'],
                        ['name' => 'HR', 'icon' => 'fa-user-tie'],
                        ['name' => 'Settings', 'icon' => 'fa-cog'],
                        ['name' => 'Users', 'icon' => 'fa-user-shield'],
                        ['name' => 'Backup', 'icon' => 'fa-database']
                    ];

                    $permission_types = ['view', 'create', 'edit', 'delete', 'approve', 'export'];

                    foreach($modules as $module):
                        // Get existing permissions
                        $module_permissions = [];
                        if(isset($role_permissions)) {
                            foreach($role_permissions as $perm) {
                                if($perm->module_name == $module['name']) {
                                    $module_permissions = (array)$perm;
                                    break;
                                }
                            }
                        }
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <i class="fas <?php echo $module['icon']; ?> mr-2 text-gray-500"></i>
                            <span class="font-semibold text-gray-900"><?php echo $module['name']; ?></span>
                        </td>
                        <?php foreach($permission_types as $type): ?>
                        <td class="px-6 py-4 text-center">
                            <input type="checkbox"
                                   name="permissions[<?php echo $module['name']; ?>][can_<?php echo $type; ?>]"
                                   value="1"
                                   class="module-permission w-5 h-5 text-blue-600 rounded focus:ring-blue-500"
                                   data-module="<?php echo $module['name']; ?>"
                                   <?php echo (isset($module_permissions['can_'.$type]) && $module_permissions['can_'.$type]) ? 'checked' : ''; ?>>
                        </td>
                        <?php endforeach; ?>
                        <td class="px-6 py-4 text-center">
                            <button type="button"
                                    onclick="toggleRow('<?php echo $module['name']; ?>')"
                                    class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-check-double"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Bulk Actions -->
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div class="text-sm text-gray-600">
                    <strong>Quick Actions:</strong>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <button type="button" onclick="checkAll()" class="px-4 py-2 bg-green-600 text-white rounded text-sm hover:bg-green-700">
                        <i class="fas fa-check-double mr-1"></i>Grant All
                    </button>
                    <button type="button" onclick="uncheckAll()" class="px-4 py-2 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                        <i class="fas fa-times mr-1"></i>Revoke All
                    </button>
                    <button type="button" onclick="checkViewOnly()" class="px-4 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                        <i class="fas fa-eye mr-1"></i>View Only
                    </button>
                    <button type="button" onclick="checkViewEdit()" class="px-4 py-2 bg-purple-600 text-white rounded text-sm hover:bg-purple-700">
                        <i class="fas fa-edit mr-1"></i>View & Edit
                    </button>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="mt-6 flex justify-end gap-4">
            <a href="<?php echo base_url('user_permissions/roles'); ?>" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>Save Role Permissions
            </button>
        </div>

        <?php echo form_close(); ?>
    </div>
</div>

<script>
function checkAll() {
    document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = true);
}

function uncheckAll() {
    document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
}

function checkViewOnly() {
    document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
        cb.checked = cb.name.includes('can_view');
    });
}

function checkViewEdit() {
    document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
        cb.checked = cb.name.includes('can_view') || cb.name.includes('can_edit');
    });
}

function toggleRow(moduleName) {
    const checkboxes = document.querySelectorAll(`input[data-module="${moduleName}"]`);
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
}
</script>
