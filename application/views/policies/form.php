<?php $this->load->view('components/ui_components'); ?>

<!-- Policy Issuance Form -->
<div class="mb-6" data-aos="fade-down">
    <h1 class="page-title">Issue New Policy</h1>
    <p class="text-gray-600 mt-1">Create a new insurance policy</p>
</div>

<?php if ($this->session->flashdata('success')): ?>
    <?php echo alert($this->session->flashdata('success'), 'success'); ?>
<?php endif; ?>

<?php if (validation_errors()): ?>
    <?php echo alert(validation_errors(), 'danger'); ?>
<?php endif; ?>

<form method="post" action="<?php echo base_url('policies/add'); ?>" class="space-y-6">

    <!-- Customer Selection -->
    <?php card_start('Customer Information', '', true); ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php form_select_group(
                'customer_id',
                'Select Customer',
                array_column($customers, 'name', 'id'),
                true,
                $selected_customer_id ?: set_value('customer_id'),
                'Choose customer for this policy'
            ); ?>

            <?php form_select_group(
                'policy_type_id',
                'Policy Type',
                array_column($policy_types, 'name', 'id'),
                true,
                set_value('policy_type_id'),
                'Select insurance type'
            ); ?>
        </div>
    <?php card_end(); ?>

    <!-- Policy Details -->
    <?php card_start('Policy Details', '', true); ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php form_input_group('issue_date', 'Issue Date', 'date', true, date('Y-m-d')); ?>
            <?php form_input_group('expiry_date', 'Expiry Date', 'date', true, date('Y-m-d', strtotime('+1 year'))); ?>
            <?php form_input_group('sum_insured', 'Sum Insured', 'number', true, '', '0', 'Total coverage amount'); ?>

            <?php form_input_group('premium_amount', 'Premium Amount', 'number', true, '', '0', 'Premium before VAT'); ?>

            <?php form_select_group(
                'payment_frequency',
                'Payment Frequency',
                ['annual' => 'Annual', 'semi-annual' => 'Semi-Annual', 'quarterly' => 'Quarterly', 'monthly' => 'Monthly'],
                true,
                'annual'
            ); ?>

            <?php form_select_group(
                'currency_id',
                'Currency',
                array_column($currencies, 'name', 'id'),
                false,
                1
            ); ?>
        </div>
    <?php card_end(); ?>

    <!-- Vehicle Details (for Motor Insurance) -->
    <?php card_start('Vehicle Details (Optional)', '', true); ?>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <?php form_input_group('vehicle_make', 'Make', 'text', false, '', 'e.g., Toyota'); ?>
            <?php form_input_group('vehicle_model', 'Model', 'text', false, '', 'e.g., Camry'); ?>
            <?php form_input_group('vehicle_year', 'Year', 'number', false, '', date('Y')); ?>
            <?php form_input_group('vehicle_plate_no', 'Plate Number', 'text', false, '', 'License plate'); ?>
            <?php form_input_group('vehicle_chassis_no', 'Chassis Number', 'text', false, '', '17-digit VIN'); ?>
        </div>
    <?php card_end(); ?>

    <!-- Agent/Broker -->
    <?php card_start('Commission Details', '', true); ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php form_select_group(
                'agent_id',
                'Agent',
                array_column($agents, 'name', 'id'),
                false,
                null,
                'Select agent if applicable'
            ); ?>

            <?php form_select_group(
                'broker_id',
                'Broker',
                array_column($brokers, 'name', 'id'),
                false,
                null,
                'Select broker if applicable'
            ); ?>

            <?php form_input_group('commission_rate', 'Commission Rate (%)', 'number', false, '0', '0', 'Percentage commission'); ?>
        </div>
    <?php card_end(); ?>

    <!-- Remarks -->
    <?php card_start('Additional Notes', '', true); ?>
        <div class="form-group">
            <label for="remarks" class="form-label">Remarks</label>
            <textarea id="remarks" name="remarks" rows="4" class="form-input" placeholder="Additional notes..."><?php echo set_value('remarks'); ?></textarea>
        </div>
    <?php card_end(); ?>

    <!-- Actions -->
    <div class="flex gap-3" data-aos="fade-up">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-save"></i>
            Issue Policy
        </button>
        <a href="<?php echo base_url('policies'); ?>" class="btn btn-outline btn-lg">
            <i class="fas fa-times"></i>
            Cancel
        </a>
        <button type="button" onclick="calculatePremium()" class="btn btn-info btn-lg ml-auto">
            <i class="fas fa-calculator"></i>
            Calculate Premium
        </button>
    </div>

</form>

<script>
// Premium Calculator
async function calculatePremium() {
    const policyType = document.querySelector('[name="policy_type_id"]').value;
    const sumInsured = document.querySelector('[name="sum_insured"]').value;
    const vehicleYear = document.querySelector('[name="vehicle_year"]').value;

    if (!policyType || !sumInsured) {
        Utils.showToast('Please select policy type and sum insured', 'error');
        return;
    }

    try {
        const response = await fetch('<?php echo base_url("policies/calculate_premium"); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `policy_type_id=${policyType}&sum_insured=${sumInsured}&vehicle_year=${vehicleYear}`
        });

        const data = await response.json();

        if (data.success) {
            document.querySelector('[name="premium_amount"]').value = data.premium_amount;
            Utils.showToast(`Premium: ${data.premium_amount} + VAT: ${data.vat_amount} = Total: ${data.total_premium}`, 'success');
        }
    } catch (error) {
        Utils.showToast('Failed to calculate premium', 'error');
    }
}

// Auto-calculate when sum insured changes
document.querySelector('[name="sum_insured"]').addEventListener('change', calculatePremium);
document.querySelector('[name="policy_type_id"]').addEventListener('change', calculatePremium);
</script>
