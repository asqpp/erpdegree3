<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">
        <i class="fas fa-edit mr-2"></i>Edit Company Settings
    </h1>

    <div class="bg-white rounded-lg shadow p-6">
        <?php echo form_open_multipart('company_settings/save', array('id' => 'settingsForm')); ?>

        <!-- Company Information -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Company Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Company Logo</label>
                    <?php if(isset($settings->logo_path) && $settings->logo_path): ?>
                        <img src="<?php echo base_url($settings->logo_path); ?>" alt="Current Logo" class="h-20 mb-2">
                    <?php endif; ?>
                    <input type="file" name="company_logo" accept="image/*" class="w-full border rounded px-3 py-2">
                    <p class="text-xs text-gray-500 mt-1">Recommended size: 200x80px, Max: 2MB</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Company Name *</label>
                    <input type="text" name="company_name" value="<?php echo $settings->company_name ?? ''; ?>" class="w-full border rounded px-3 py-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trade License Number</label>
                    <input type="text" name="trade_license_number" value="<?php echo $settings->trade_license_number ?? ''; ?>" class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tax Registration Number (TRN) *</label>
                    <input type="text" name="tax_registration_number" value="<?php echo $settings->tax_registration_number ?? ''; ?>" class="w-full border rounded px-3 py-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">License Expiry Date</label>
                    <input type="date" name="license_expiry_date" value="<?php echo $settings->license_expiry_date ?? ''; ?>" class="w-full border rounded px-3 py-2">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <textarea name="address" rows="2" class="w-full border rounded px-3 py-2"><?php echo $settings->address ?? ''; ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                    <input type="text" name="city" value="<?php echo $settings->city ?? ''; ?>" class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Emirate</label>
                    <select name="emirate_id" class="w-full border rounded px-3 py-2">
                        <option value="">Select Emirate</option>
                        <?php if(isset($emirates)): foreach($emirates as $emirate): ?>
                        <option value="<?php echo $emirate->emirate_id; ?>" <?php echo (isset($settings->emirate_id) && $settings->emirate_id == $emirate->emirate_id) ? 'selected' : ''; ?>>
                            <?php echo $emirate->emirate_name; ?>
                        </option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">P.O. Box</label>
                    <input type="text" name="po_box" value="<?php echo $settings->po_box ?? ''; ?>" class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                    <input type="text" name="country" value="<?php echo $settings->country ?? 'United Arab Emirates'; ?>" class="w-full border rounded px-3 py-2">
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Contact Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone *</label>
                    <input type="text" name="phone" value="<?php echo $settings->phone ?? ''; ?>" class="w-full border rounded px-3 py-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" name="email" value="<?php echo $settings->email ?? ''; ?>" class="w-full border rounded px-3 py-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                    <input type="url" name="website" value="<?php echo $settings->website ?? ''; ?>" class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fax</label>
                    <input type="text" name="fax" value="<?php echo $settings->fax ?? ''; ?>" class="w-full border rounded px-3 py-2">
                </div>
            </div>
        </div>

        <!-- System Settings -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4 pb-2 border-b">System Settings</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Base Currency</label>
                    <select name="base_currency" class="w-full border rounded px-3 py-2">
                        <?php
                        $currencies = ['AED' => 'UAE Dirham (AED)', 'SAR' => 'Saudi Riyal (SAR)', 'USD' => 'US Dollar (USD)', 'EUR' => 'Euro (EUR)'];
                        foreach($currencies as $code => $name):
                        ?>
                        <option value="<?php echo $code; ?>" <?php echo (isset($settings->base_currency) && $settings->base_currency == $code) ? 'selected' : ''; ?>>
                            <?php echo $name; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Default VAT Percentage (%)</label>
                    <input type="number" name="default_vat_percentage" step="0.01" value="<?php echo $settings->default_vat_percentage ?? '5.00'; ?>" class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fiscal Year Start</label>
                    <input type="date" name="fiscal_year_start" value="<?php echo $settings->fiscal_year_start ?? date('Y').'-01-01'; ?>" class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fiscal Year End</label>
                    <input type="date" name="fiscal_year_end" value="<?php echo $settings->fiscal_year_end ?? date('Y').'-12-31'; ?>" class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Time Zone</label>
                    <select name="timezone" class="w-full border rounded px-3 py-2">
                        <option value="Asia/Dubai" <?php echo (isset($settings->timezone) && $settings->timezone == 'Asia/Dubai') ? 'selected' : ''; ?>>Asia/Dubai (UAE)</option>
                        <option value="Asia/Riyadh" <?php echo (isset($settings->timezone) && $settings->timezone == 'Asia/Riyadh') ? 'selected' : ''; ?>>Asia/Riyadh (Saudi Arabia)</option>
                        <option value="UTC" <?php echo (isset($settings->timezone) && $settings->timezone == 'UTC') ? 'selected' : ''; ?>>UTC</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Format</label>
                    <select name="date_format" class="w-full border rounded px-3 py-2">
                        <option value="dd/mm/yyyy" <?php echo (isset($settings->date_format) && $settings->date_format == 'dd/mm/yyyy') ? 'selected' : ''; ?>>DD/MM/YYYY</option>
                        <option value="mm/dd/yyyy" <?php echo (isset($settings->date_format) && $settings->date_format == 'mm/dd/yyyy') ? 'selected' : ''; ?>>MM/DD/YYYY</option>
                        <option value="yyyy-mm-dd" <?php echo (isset($settings->date_format) && $settings->date_format == 'yyyy-mm-dd') ? 'selected' : ''; ?>>YYYY-MM-DD</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-4">
            <a href="<?php echo base_url('company_settings'); ?>" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>Save Settings
            </button>
        </div>

        <?php echo form_close(); ?>
    </div>
</div>
