<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-database mr-2"></i>Database Backups
        </h1>
        <div class="flex gap-2">
            <a href="<?php echo base_url('backup/upload'); ?>" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700">
                <i class="fas fa-upload mr-2"></i>Upload Backup
            </a>
            <a href="<?php echo base_url('backup/create'); ?>"
               onclick="return confirm('Create a new backup now? This may take a few moments.');"
               class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Create Backup
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Total Backups</div>
            <div class="text-3xl font-bold text-blue-600"><?php echo number_format($statistics['total_backups'] ?? 0); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Total Size</div>
            <div class="text-3xl font-bold text-green-600"><?php echo $statistics['total_size'] ?? '0 MB'; ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Last Backup</div>
            <div class="text-lg font-bold text-purple-600"><?php echo isset($statistics['last_backup']) ? date('d/m/Y', strtotime($statistics['last_backup'])) : 'Never'; ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Auto Backup</div>
            <div class="text-xl font-bold <?php echo (isset($statistics['auto_enabled']) && $statistics['auto_enabled']) ? 'text-green-600' : 'text-red-600'; ?>">
                <?php echo (isset($statistics['auto_enabled']) && $statistics['auto_enabled']) ? 'Enabled' : 'Disabled'; ?>
            </div>
        </div>
    </div>

    <!-- Backups Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Backup Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Size</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if(isset($backups) && count($backups) > 0): ?>
                    <?php foreach($backups as $backup): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <i class="fas fa-file-archive text-2xl text-blue-600 mr-3"></i>
                                <div>
                                    <div class="font-semibold"><?php echo $backup->backup_name; ?></div>
                                    <div class="text-xs text-gray-500"><?php echo basename($backup->backup_file_path); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm"><?php echo date('d/m/Y', strtotime($backup->backup_date)); ?></div>
                            <div class="text-xs text-gray-500"><?php echo date('H:i:s', strtotime($backup->backup_date)); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <?php
                            $typeColors = [
                                'manual' => 'bg-blue-100 text-blue-800',
                                'automatic' => 'bg-green-100 text-green-800',
                                'scheduled' => 'bg-purple-100 text-purple-800'
                            ];
                            $typeColor = $typeColors[$backup->backup_type] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="px-2 py-1 <?php echo $typeColor; ?> rounded text-xs">
                                <?php echo ucfirst($backup->backup_type); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 font-semibold">
                            <?php
                            $size_mb = $backup->backup_size / (1024 * 1024);
                            echo $size_mb < 1 ? number_format($backup->backup_size / 1024, 2) . ' KB' : number_format($size_mb, 2) . ' MB';
                            ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php
                            $statusColors = [
                                'in_progress' => 'bg-yellow-100 text-yellow-800',
                                'completed' => 'bg-green-100 text-green-800',
                                'failed' => 'bg-red-100 text-red-800'
                            ];
                            $statusColor = $statusColors[$backup->status] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="px-2 py-1 <?php echo $statusColor; ?> rounded text-xs font-semibold">
                                <?php echo ucfirst($backup->status); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm"><?php echo $backup->created_by_name ?? 'System'; ?></td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <?php if($backup->status == 'completed'): ?>
                                    <a href="<?php echo base_url('backup/download/'.$backup->backup_id); ?>"
                                       class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-download mr-1"></i>Download
                                    </a>
                                    <a href="<?php echo base_url('backup/restore/'.$backup->backup_id); ?>"
                                       class="text-green-600 hover:text-green-800"
                                       onclick="return confirm('⚠️ WARNING: Restoring will replace all current data with backup data. This action cannot be undone.\n\nAre you absolutely sure you want to restore this backup?')">
                                        <i class="fas fa-redo mr-1"></i>Restore
                                    </a>
                                    <a href="<?php echo base_url('backup/delete/'.$backup->backup_id); ?>"
                                       class="text-red-600 hover:text-red-800"
                                       onclick="return confirm('Delete this backup? This action cannot be undone.')">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <i class="fas fa-database text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg">No backups found</p>
                            <p class="text-gray-400 text-sm mt-2">Create your first backup to get started</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Warning Note -->
    <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
        <div class="flex">
            <i class="fas fa-exclamation-triangle text-yellow-400 mr-3 mt-1"></i>
            <div>
                <p class="text-sm text-yellow-800 font-semibold">Important Backup Information:</p>
                <ul class="text-sm text-yellow-700 mt-2 list-disc list-inside space-y-1">
                    <li>Always test your backups by restoring to a test environment first</li>
                    <li>Keep backups in a secure, off-site location</li>
                    <li>Verify backup integrity regularly</li>
                    <li>Document your backup and restore procedures</li>
                </ul>
            </div>
        </div>
    </div>
</div>
