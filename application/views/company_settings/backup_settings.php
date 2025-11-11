<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">
        <i class="fas fa-database mr-2"></i>Backup Settings
    </h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Backup Settings Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <?php echo form_open('company_settings/save_backup_settings'); ?>

                <!-- Auto Backup -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="backup_enabled" value="1" <?php echo (isset($settings->backup_enabled) && $settings->backup_enabled) ? 'checked' : ''; ?> class="w-5 h-5 text-blue-600 rounded">
                        <span class="ml-3 text-lg font-semibold">Enable Automatic Backups</span>
                    </label>
                    <p class="text-sm text-gray-500 mt-2 ml-8">Automatically backup your database at scheduled intervals</p>
                </div>

                <!-- Backup Frequency -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Backup Frequency</label>
                    <select name="backup_frequency" class="w-full border rounded px-3 py-2">
                        <option value="daily" <?php echo (isset($settings->backup_frequency) && $settings->backup_frequency == 'daily') ? 'selected' : ''; ?>>Daily</option>
                        <option value="weekly" <?php echo (isset($settings->backup_frequency) && $settings->backup_frequency == 'weekly') ? 'selected' : ''; ?>>Weekly</option>
                        <option value="monthly" <?php echo (isset($settings->backup_frequency) && $settings->backup_frequency == 'monthly') ? 'selected' : ''; ?>>Monthly</option>
                    </select>
                </div>

                <!-- Backup Time -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Backup Time</label>
                    <input type="time" name="backup_time" value="<?php echo $settings->backup_time ?? '02:00'; ?>" class="w-full border rounded px-3 py-2">
                    <p class="text-xs text-gray-500 mt-1">Time when automatic backups will run (24-hour format)</p>
                </div>

                <!-- Retention Period -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Retention Period (days)</label>
                    <input type="number" name="backup_retention_days" value="<?php echo $settings->backup_retention_days ?? 30; ?>" min="1" max="365" class="w-full border rounded px-3 py-2">
                    <p class="text-xs text-gray-500 mt-1">How long to keep old backups before auto-deletion</p>
                </div>

                <!-- Backup Location -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Backup Location</label>
                    <input type="text" name="backup_path" value="<?php echo $settings->backup_path ?? './backups/'; ?>" class="w-full border rounded px-3 py-2">
                    <p class="text-xs text-gray-500 mt-1">Server path where backups will be stored</p>
                </div>

                <!-- Compression -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="backup_compression" value="1" <?php echo (isset($settings->backup_compression) && $settings->backup_compression) ? 'checked' : ''; ?> class="w-5 h-5 text-blue-600 rounded">
                        <span class="ml-3 font-medium">Compress backups (recommended)</span>
                    </label>
                    <p class="text-sm text-gray-500 mt-2 ml-8">Reduce backup file size using gzip compression</p>
                </div>

                <!-- Email Notifications -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="backup_email_notifications" value="1" <?php echo (isset($settings->backup_email_notifications) && $settings->backup_email_notifications) ? 'checked' : ''; ?> class="w-5 h-5 text-blue-600 rounded">
                        <span class="ml-3 font-medium">Email notifications</span>
                    </label>
                    <p class="text-sm text-gray-500 mt-2 ml-8">Receive email when backups are completed or fail</p>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notification Email</label>
                    <input type="email" name="backup_notification_email" value="<?php echo $settings->backup_notification_email ?? $settings->email; ?>" class="w-full border rounded px-3 py-2">
                </div>

                <!-- Save Button -->
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>Save Settings
                    </button>
                </div>

                <?php echo form_close(); ?>
            </div>
        </div>

        <!-- Info & Actions -->
        <div>
            <!-- Current Status -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="font-semibold mb-4">Backup Status</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <?php if(isset($settings->backup_enabled) && $settings->backup_enabled): ?>
                            <p class="font-semibold text-green-600"><i class="fas fa-check-circle mr-1"></i>Enabled</p>
                        <?php else: ?>
                            <p class="font-semibold text-red-600"><i class="fas fa-times-circle mr-1"></i>Disabled</p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Last Backup</p>
                        <p class="font-semibold"><?php echo isset($last_backup) ? date('d/m/Y H:i', strtotime($last_backup)) : 'Never'; ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Next Backup</p>
                        <p class="font-semibold"><?php echo isset($next_backup) ? date('d/m/Y H:i', strtotime($next_backup)) : 'Not Scheduled'; ?></p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="<?php echo base_url('backup'); ?>" class="block w-full text-center bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        <i class="fas fa-database mr-2"></i>View All Backups
                    </a>
                    <a href="<?php echo base_url('backup/create'); ?>" class="block w-full text-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                       onclick="return confirm('Create a manual backup now?')">
                        <i class="fas fa-play mr-2"></i>Create Backup Now
                    </a>
                </div>
            </div>

            <!-- Info Box -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Important:</strong> Regular backups protect your data. Test your backups periodically to ensure they work correctly.
                </p>
            </div>
        </div>
    </div>
</div>
