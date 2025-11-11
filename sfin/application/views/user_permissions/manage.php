<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-user-shield mr-2"></i>Manage Permissions
            </h1>
            <p class="text-gray-600 mt-2">User: <strong><?php echo $user->full_name; ?></strong> (<?php echo ucfirst($user->role); ?>)</p>
        </div>
        <a href="<?php echo base_url('user_permissions/list'); ?>" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <?php echo form_open('user_permissions/save/'.$user->user_id); ?>

        <div class="mb-6 bg-blue-50 border-l-4 border-blue-600 p-4">
            <p class="text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                User-specific permissions override role permissions. Leave unchecked to inherit from role.
            </p>
        </div>

        <!-- Modules Permissions -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Module</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold">
                            <i class="fas fa-eye mr-1"></i>View
                        </th>
                        <th class="px-6 py-3 text-center text-sm font-semibold">
                            <i class="fas fa-plus mr-1"></i>Create
                        </th>
                        <th class="px-6 py-3 text-center text-sm font-semibold">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </th>
                        <th class="px-6 py-3 text-center text-sm font-semibold">
                            <i class="fas fa-trash mr-1"></i>Delete
                        </th>
                        <th class="px-6 py-3 text-center text-sm font-semibold">
                            <i class="fas fa-check-circle mr-1"></i>Approve
                        </th>
                        <th class="px-6 py-3 text-center text-sm font-semibold">
                            <i class="fas fa-file-export mr-1"></i>Export
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    $modules = [
                        'Customers', 'Policies', 'Claims', 'Sales', 'Receipts', 'Payments',
                        'Debit Notes', 'Credit Notes', 'Accounting', 'Reports',
                        'HR', 'Settings', 'Users', 'Backup'
                    ];

                    $permission_types = ['view', 'create', 'edit', 'delete', 'approve', 'export'];

                    foreach($modules as $module):
                        // Get existing permissions
                        $module_permissions = [];
                        if(isset($user_permissions)) {
                            foreach($user_permissions as $perm) {
                                if($perm->module_name == $module) {
                                    $module_permissions = (array)$perm;
                                    break;
                                }
                            }
                        }
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-gray-900"><?php echo $module; ?></td>
                        <?php foreach($permission_types as $type): ?>
                        <td class="px-6 py-4 text-center">
                            <input type="checkbox"
                                   name="permissions[<?php echo $module; ?>][can_<?php echo $type; ?>]"
                                   value="1"
                                   class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500"
                                   <?php echo (isset($module_permissions['can_'.$type]) && $module_permissions['can_'.$type]) ? 'checked' : ''; ?>>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Bulk Actions -->
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    <strong>Quick Actions:</strong>
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="checkAll()" class="px-4 py-2 bg-green-600 text-white rounded text-sm hover:bg-green-700">
                        <i class="fas fa-check-double mr-1"></i>Check All
                    </button>
                    <button type="button" onclick="uncheckAll()" class="px-4 py-2 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                        <i class="fas fa-times mr-1"></i>Uncheck All
                    </button>
                    <button type="button" onclick="checkViewOnly()" class="px-4 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                        <i class="fas fa-eye mr-1"></i>View Only
                    </button>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="mt-6 flex justify-end gap-4">
            <a href="<?php echo base_url('user_permissions/list'); ?>" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>Save Permissions
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
</script>
