<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-building mr-2"></i>Company Settings
        </h1>
        <a href="<?php echo base_url('company_settings/edit'); ?>" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-edit mr-2"></i>Edit Settings
        </a>
    </div>

    <!-- Company Information -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold mb-4 pb-2 border-b">Company Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php if(isset($settings->logo_path) && $settings->logo_path): ?>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500 mb-2">Company Logo</label>
                <img src="<?php echo base_url($settings->logo_path); ?>" alt="Company Logo" class="h-24">
            </div>
            <?php endif; ?>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Company Name</label>
                <p class="text-lg font-semibold"><?php echo $settings->company_name ?? 'Not Set'; ?></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Trade License Number</label>
                <p class="font-medium"><?php echo $settings->trade_license_number ?? 'Not Set'; ?></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tax Registration Number (TRN)</label>
                <p class="font-medium"><?php echo $settings->tax_registration_number ?? 'Not Set'; ?></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">License Expiry Date</label>
                <p class="font-medium"><?php echo isset($settings->license_expiry_date) ? date('d/m/Y', strtotime($settings->license_expiry_date)) : 'Not Set'; ?></p>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500 mb-1">Address</label>
                <p class="font-medium"><?php echo $settings->address ?? 'Not Set'; ?></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">City</label>
                <p class="font-medium"><?php echo $settings->city ?? 'Not Set'; ?></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Emirate</label>
                <p class="font-medium"><?php echo $settings->emirate_name ?? 'Not Set'; ?></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">P.O. Box</label>
                <p class="font-medium"><?php echo $settings->po_box ?? 'Not Set'; ?></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Country</label>
                <p class="font-medium"><?php echo $settings->country ?? 'United Arab Emirates'; ?></p>
            </div>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold mb-4 pb-2 border-b">Contact Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Phone</label>
                <p class="font-medium"><i class="fas fa-phone mr-2 text-blue-600"></i><?php echo $settings->phone ?? 'Not Set'; ?></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                <p class="font-medium"><i class="fas fa-envelope mr-2 text-blue-600"></i><?php echo $settings->email ?? 'Not Set'; ?></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Website</label>
                <p class="font-medium"><i class="fas fa-globe mr-2 text-blue-600"></i><?php echo $settings->website ?? 'Not Set'; ?></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Fax</label>
                <p class="font-medium"><i class="fas fa-fax mr-2 text-blue-600"></i><?php echo $settings->fax ?? 'Not Set'; ?></p>
            </div>
        </div>
    </div>

    <!-- System Settings -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold mb-4 pb-2 border-b">System Settings</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Base Currency</label>
                <p class="text-lg font-semibold text-green-600"><?php echo $settings->base_currency ?? 'AED'; ?></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Default VAT Percentage</label>
                <p class="text-lg font-semibold text-blue-600"><?php echo $settings->default_vat_percentage ?? '5'; ?>%</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Fiscal Year Start</label>
                <p class="font-medium"><?php echo isset($settings->fiscal_year_start) ? date('F d', strtotime($settings->fiscal_year_start)) : 'January 01'; ?></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Fiscal Year End</label>
                <p class="font-medium"><?php echo isset($settings->fiscal_year_end) ? date('F d', strtotime($settings->fiscal_year_end)) : 'December 31'; ?></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Time Zone</label>
                <p class="font-medium"><?php echo $settings->timezone ?? 'Asia/Dubai'; ?></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Date Format</label>
                <p class="font-medium"><?php echo $settings->date_format ?? 'dd/mm/yyyy'; ?></p>
            </div>
        </div>
    </div>

    <!-- Backup Settings -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4 pb-2 border-b">
            <h2 class="text-xl font-bold">Backup Settings</h2>
            <a href="<?php echo base_url('company_settings/backup_settings'); ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                <i class="fas fa-cog mr-1"></i>Configure
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Auto Backup</label>
                <p class="font-semibold">
                    <?php if(isset($settings->backup_enabled) && $settings->backup_enabled): ?>
                        <span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>Enabled</span>
                    <?php else: ?>
                        <span class="text-red-600"><i class="fas fa-times-circle mr-1"></i>Disabled</span>
                    <?php endif; ?>
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Frequency</label>
                <p class="font-medium"><?php echo isset($settings->backup_frequency) ? ucfirst($settings->backup_frequency) : 'Not Set'; ?></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Retention Period</label>
                <p class="font-medium"><?php echo $settings->backup_retention_days ?? 30; ?> days</p>
            </div>
        </div>
    </div>
</div>
