<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <div class="max-w-3xl mx-auto">
        <div class="flex items-center mb-6">
            <a href="<?php echo base_url('backup'); ?>" class="text-gray-600 hover:text-gray-800 mr-4">
                <i class="fas fa-arrow-left text-2xl"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-upload mr-2"></i>Upload Backup
            </h1>
        </div>

        <div class="bg-white rounded-lg shadow p-8">
            <?php echo form_open_multipart('backup/process_upload', array('id' => 'uploadForm')); ?>

            <!-- Upload Area -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Backup File *</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-500 transition">
                    <i class="fas fa-cloud-upload-alt text-6xl text-gray-400 mb-4"></i>
                    <p class="text-lg font-semibold text-gray-700 mb-2">Drop your backup file here or click to browse</p>
                    <p class="text-sm text-gray-500 mb-4">Supported formats: .sql, .sql.gz, .zip</p>
                    <input type="file"
                           name="backup_file"
                           id="backup_file"
                           accept=".sql,.gz,.zip"
                           class="hidden"
                           required
                           onchange="showFileName(this)">
                    <label for="backup_file" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 cursor-pointer">
                        <i class="fas fa-folder-open mr-2"></i>Choose File
                    </label>
                    <div id="file-name" class="mt-4 text-sm font-semibold text-gray-700"></div>
                </div>
            </div>

            <!-- Backup Details -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Backup Name *</label>
                <input type="text"
                       name="backup_name"
                       class="w-full border rounded px-3 py-2"
                       placeholder="e.g., Manual Backup 2025-01-15"
                       required>
                <p class="text-xs text-gray-500 mt-1">Give this backup a descriptive name</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                <textarea name="description"
                          rows="3"
                          class="w-full border rounded px-3 py-2"
                          placeholder="Add any notes about this backup..."></textarea>
            </div>

            <!-- Restore After Upload Option -->
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <label class="flex items-start">
                    <input type="checkbox" name="restore_after_upload" value="1" class="w-5 h-5 text-blue-600 rounded mt-0.5">
                    <div class="ml-3">
                        <span class="font-semibold text-red-800">Restore immediately after upload</span>
                        <p class="text-sm text-red-700 mt-1">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            WARNING: This will replace all current data with the backup data. Only check this if you're certain!
                        </p>
                    </div>
                </label>
            </div>

            <!-- Upload Progress (Hidden by default) -->
            <div id="upload-progress" class="hidden mb-6">
                <div class="flex justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Uploading...</span>
                    <span class="text-sm font-medium text-gray-700" id="progress-percent">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div id="progress-bar" class="bg-blue-600 h-2.5 rounded-full" style="width: 0%"></div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-4">
                <a href="<?php echo base_url('backup'); ?>" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" id="upload-btn" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-upload mr-2"></i>Upload Backup
                </button>
            </div>

            <?php echo form_close(); ?>
        </div>

        <!-- Info Boxes -->
        <div class="mt-6 space-y-4">
            <!-- File Requirements -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-800 font-semibold mb-2">
                    <i class="fas fa-info-circle mr-2"></i>File Requirements:
                </p>
                <ul class="text-sm text-blue-700 list-disc list-inside space-y-1">
                    <li>Maximum file size: 500MB</li>
                    <li>Accepted formats: .sql (uncompressed), .sql.gz (gzip compressed), .zip</li>
                    <li>File must be a valid MySQL database backup</li>
                    <li>Ensure the backup is from a compatible system version</li>
                </ul>
            </div>

            <!-- Security Notice -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-sm text-yellow-800 font-semibold mb-2">
                    <i class="fas fa-shield-alt mr-2"></i>Security Notice:
                </p>
                <ul class="text-sm text-yellow-700 list-disc list-inside space-y-1">
                    <li>Only upload backups from trusted sources</li>
                    <li>Verify backup file integrity before uploading</li>
                    <li>Always test backups in a non-production environment first</li>
                    <li>Create a current backup before restoring an uploaded one</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function showFileName(input) {
    const fileNameDisplay = document.getElementById('file-name');
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
        fileNameDisplay.innerHTML = `
            <i class="fas fa-file-archive text-blue-600 mr-2"></i>
            <span class="font-semibold">${file.name}</span>
            <span class="text-gray-500 ml-2">(${sizeMB} MB)</span>
        `;

        // Validate file size
        if (file.size > 500 * 1024 * 1024) {
            alert('File size exceeds 500MB limit. Please compress or split your backup file.');
            input.value = '';
            fileNameDisplay.innerHTML = '';
        }
    }
}

// Form submission with progress tracking (if supported)
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('backup_file');
    if (!fileInput.files || !fileInput.files[0]) {
        e.preventDefault();
        alert('Please select a backup file to upload.');
        return;
    }

    // Show progress bar
    document.getElementById('upload-progress').classList.remove('hidden');
    document.getElementById('upload-btn').disabled = true;
    document.getElementById('upload-btn').innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';
});

// Drag and drop support
const dropZone = document.querySelector('.border-dashed');
const fileInput = document.getElementById('backup_file');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, () => {
        dropZone.classList.add('border-blue-500', 'bg-blue-50');
    }, false);
});

['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, () => {
        dropZone.classList.remove('border-blue-500', 'bg-blue-50');
    }, false);
});

dropZone.addEventListener('drop', (e) => {
    const dt = e.dataTransfer;
    const files = dt.files;
    fileInput.files = files;
    showFileName(fileInput);
}, false);
</script>
