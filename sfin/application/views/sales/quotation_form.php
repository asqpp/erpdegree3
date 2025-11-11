<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">
        <i class="fas fa-file-invoice mr-2"></i><?php echo isset($quotation) ? 'Edit' : 'New'; ?> Quotation
    </h1>

    <div class="bg-white rounded-lg shadow p-6">
        <?php echo form_open('sales/quotation_save', array('id' => 'quotationForm')); ?>
        <?php if(isset($quotation)): ?>
            <input type="hidden" name="quotation_id" value="<?php echo $quotation->quotation_id; ?>">
        <?php endif; ?>

        <!-- Customer & Basic Info -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Customer *</label>
                <select name="customer_id" id="customer_id" class="w-full border rounded px-3 py-2" required>
                    <option value="">Select Customer</option>
                    <?php foreach($customers as $customer): ?>
                    <option value="<?php echo $customer->customer_id; ?>" <?php echo (isset($quotation) && $quotation->customer_id == $customer->customer_id) ? 'selected' : ''; ?>>
                        <?php echo $customer->customer_name; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Insurance Type *</label>
                <select name="insurance_type" class="w-full border rounded px-3 py-2" required>
                    <option value="">Select Type</option>
                    <option value="motor" <?php echo (isset($quotation) && $quotation->insurance_type == 'motor') ? 'selected' : ''; ?>>Motor Insurance</option>
                    <option value="health" <?php echo (isset($quotation) && $quotation->insurance_type == 'health') ? 'selected' : ''; ?>>Health Insurance</option>
                    <option value="life" <?php echo (isset($quotation) && $quotation->insurance_type == 'life') ? 'selected' : ''; ?>>Life Insurance</option>
                    <option value="property" <?php echo (isset($quotation) && $quotation->insurance_type == 'property') ? 'selected' : ''; ?>>Property Insurance</option>
                    <option value="travel" <?php echo (isset($quotation) && $quotation->insurance_type == 'travel') ? 'selected' : ''; ?>>Travel Insurance</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Quotation Date *</label>
                <input type="date" name="quotation_date" value="<?php echo isset($quotation) ? $quotation->quotation_date : date('Y-m-d'); ?>" class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Valid Until *</label>
                <input type="date" name="valid_until" value="<?php echo isset($quotation) ? $quotation->valid_until : date('Y-m-d', strtotime('+30 days')); ?>" class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Insurance Company *</label>
                <select name="insurance_company_id" class="w-full border rounded px-3 py-2" required>
                    <option value="">Select Company</option>
                    <?php foreach($insurance_companies as $company): ?>
                    <option value="<?php echo $company->company_id; ?>" <?php echo (isset($quotation) && $quotation->insurance_company_id == $company->company_id) ? 'selected' : ''; ?>>
                        <?php echo $company->company_name; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Agent</label>
                <select name="agent_id" class="w-full border rounded px-3 py-2">
                    <option value="">Select Agent</option>
                    <?php foreach($agents as $agent): ?>
                    <option value="<?php echo $agent->employee_id; ?>" <?php echo (isset($quotation) && $quotation->agent_id == $agent->employee_id) ? 'selected' : ''; ?>>
                        <?php echo $agent->full_name; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Coverage Details -->
        <div class="border-t pt-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Coverage Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Coverage Amount *</label>
                    <input type="number" name="coverage_amount" step="0.01" value="<?php echo isset($quotation) ? $quotation->coverage_amount : ''; ?>" class="w-full border rounded px-3 py-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Premium Amount *</label>
                    <input type="number" name="premium_amount" step="0.01" value="<?php echo isset($quotation) ? $quotation->premium_amount : ''; ?>" class="w-full border rounded px-3 py-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Policy Term (months) *</label>
                    <input type="number" name="policy_term_months" value="<?php echo isset($quotation) ? $quotation->policy_term_months : '12'; ?>" class="w-full border rounded px-3 py-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Commission Rate (%)</label>
                    <input type="number" name="commission_rate" step="0.01" value="<?php echo isset($quotation) ? $quotation->commission_rate : '10.00'; ?>" class="w-full border rounded px-3 py-2">
                </div>
            </div>
        </div>

        <!-- Additional Details -->
        <div class="border-t pt-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Additional Information</h3>
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Coverage Details</label>
                    <textarea name="coverage_details" rows="4" class="w-full border rounded px-3 py-2" placeholder="List coverage benefits..."><?php echo isset($quotation) ? $quotation->coverage_details : ''; ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Terms & Conditions</label>
                    <textarea name="terms_conditions" rows="4" class="w-full border rounded px-3 py-2" placeholder="Enter terms and conditions..."><?php echo isset($quotation) ? $quotation->terms_conditions : ''; ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3" class="w-full border rounded px-3 py-2" placeholder="Internal notes..."><?php echo isset($quotation) ? $quotation->notes : ''; ?></textarea>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-4">
            <a href="<?php echo base_url('sales/quotation_list'); ?>" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" name="action" value="draft" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <i class="fas fa-save mr-2"></i>Save as Draft
            </button>
            <button type="submit" name="action" value="send" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-paper-plane mr-2"></i>Save & Send
            </button>
        </div>

        <?php echo form_close(); ?>
    </div>
</div>
