<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">
        <i class="fas fa-clipboard-list mr-2"></i>Audit Logs
    </h1>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form method="get" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <select name="user_id" class="border rounded px-3 py-2">
                <option value="">All Users</option>
                <?php if(isset($users)): foreach($users as $user): ?>
                <option value="<?php echo $user->user_id; ?>" <?php echo (isset($filters['user_id']) && $filters['user_id'] == $user->user_id) ? 'selected' : ''; ?>>
                    <?php echo $user->full_name; ?>
                </option>
                <?php endforeach; endif; ?>
            </select>

            <select name="action_type" class="border rounded px-3 py-2">
                <option value="">All Actions</option>
                <option value="create">Create</option>
                <option value="update">Update</option>
                <option value="delete">Delete</option>
                <option value="login">Login</option>
                <option value="logout">Logout</option>
                <option value="export">Export</option>
            </select>

            <input type="date" name="date_from" value="<?php echo $filters['date_from'] ?? ''; ?>" class="border rounded px-3 py-2" placeholder="From Date">
            <input type="date" name="date_to" value="<?php echo $filters['date_to'] ?? ''; ?>" class="border rounded px-3 py-2" placeholder="To Date">

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
        </form>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Timestamp</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Table</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Record ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP Address</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if(isset($audit_logs) && count($audit_logs) > 0): ?>
                    <?php foreach($audit_logs as $log): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm">
                            <?php echo date('d/m/Y H:i:s', strtotime($log->created_at)); ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold"><?php echo $log->user_name; ?></div>
                            <div class="text-xs text-gray-500"><?php echo $log->user_email; ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <?php
                            $actionColors = [
                                'create' => 'bg-green-100 text-green-800',
                                'update' => 'bg-blue-100 text-blue-800',
                                'delete' => 'bg-red-100 text-red-800',
                                'login' => 'bg-purple-100 text-purple-800',
                                'logout' => 'bg-gray-100 text-gray-800',
                                'export' => 'bg-yellow-100 text-yellow-800'
                            ];
                            $colorClass = $actionColors[$log->action_type] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="px-2 py-1 <?php echo $colorClass; ?> rounded text-xs font-semibold">
                                <?php echo ucfirst($log->action_type); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 font-mono text-sm"><?php echo $log->table_name ?? '-'; ?></td>
                        <td class="px-6 py-4 font-mono text-sm"><?php echo $log->record_id ?? '-'; ?></td>
                        <td class="px-6 py-4 text-sm"><?php echo $log->description; ?></td>
                        <td class="px-6 py-4 font-mono text-sm"><?php echo $log->ip_address; ?></td>
                        <td class="px-6 py-4">
                            <?php if($log->old_data || $log->new_data): ?>
                                <button onclick="viewDetails(<?php echo $log->audit_log_id; ?>)" class="text-blue-600 hover:text-blue-800 text-sm">
                                    <i class="fas fa-eye mr-1"></i>Details
                                </button>
                            <?php else: ?>
                                <span class="text-gray-400 text-sm">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">No audit logs found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if(isset($total_logs) && $total_logs > 50): ?>
    <div class="mt-6 flex justify-center">
        <!-- Add pagination here -->
    </div>
    <?php endif; ?>
</div>

<!-- Details Modal -->
<div id="detailsModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-3xl max-h-[80vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">Audit Log Details</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <div id="modalContent">
            <!-- Content loaded via AJAX -->
        </div>
    </div>
</div>

<script>
function viewDetails(logId) {
    document.getElementById('detailsModal').classList.remove('hidden');
    document.getElementById('modalContent').innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-3xl text-blue-600"></i></div>';

    fetch('<?php echo base_url('company_settings/get_audit_log_details/'); ?>' + logId)
        .then(response => response.json())
        .then(data => {
            let html = '<div class="space-y-4">';

            if (data.old_data) {
                html += '<div><h3 class="font-semibold mb-2">Previous Data:</h3><pre class="bg-gray-100 p-3 rounded text-sm overflow-x-auto">' + JSON.stringify(JSON.parse(data.old_data), null, 2) + '</pre></div>';
            }

            if (data.new_data) {
                html += '<div><h3 class="font-semibold mb-2">New Data:</h3><pre class="bg-gray-100 p-3 rounded text-sm overflow-x-auto">' + JSON.stringify(JSON.parse(data.new_data), null, 2) + '</pre></div>';
            }

            html += '</div>';
            document.getElementById('modalContent').innerHTML = html;
        })
        .catch(error => {
            document.getElementById('modalContent').innerHTML = '<div class="text-red-600">Error loading details</div>';
        });
}

function closeModal() {
    document.getElementById('detailsModal').classList.add('hidden');
}

// Close modal on outside click
document.getElementById('detailsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
