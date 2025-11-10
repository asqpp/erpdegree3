<?php $this->load->view('components/ui_components'); ?>

<?php
$is_edit = isset($customer);
$form_action = $is_edit ? base_url('customers/edit/' . $customer->id) : base_url('customers/add');
?>

<!-- Page Header -->
<div class="mb-6" data-aos="fade-down">
    <h1 class="page-title"><?php echo $is_edit ? 'Edit Customer' : 'Add New Customer'; ?></h1>
    <p class="text-gray-600 mt-1">
        <?php echo $is_edit ? 'Update customer information' : 'Create a new customer record'; ?>
    </p>
</div>

<!-- Flash Messages -->
<?php if ($this->session->flashdata('success')): ?>
    <?php echo alert($this->session->flashdata('success'), 'success'); ?>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
    <?php echo alert($this->session->flashdata('error'), 'danger'); ?>
<?php endif; ?>

<!-- Validation Errors -->
<?php if (validation_errors()): ?>
    <?php echo alert(validation_errors(), 'danger'); ?>
<?php endif; ?>

<form method="post" action="<?php echo $form_action; ?>" class="space-y-6">

    <!-- Basic Information -->
    <?php card_start('Basic Information', '', true); ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Customer Name -->
            <?php form_input_group(
                'name',
                'Customer Name',
                'text',
                true,
                $is_edit ? $customer->name : set_value('name'),
                'Enter full name',
                'Full legal name as per official documents'
            ); ?>

            <!-- Customer Type -->
            <?php form_select_group(
                'customer_type',
                'Customer Type',
                [
                    'individual' => 'Individual',
                    'corporate' => 'Corporate'
                ],
                true,
                $is_edit ? $customer->customer_type : set_value('customer_type'),
                'Select whether this is an individual or corporate customer'
            ); ?>

            <!-- Email -->
            <?php form_input_group(
                'email',
                'Email Address',
                'email',
                true,
                $is_edit ? $customer->email : set_value('email'),
                'customer@example.com',
                'Primary email for communication'
            ); ?>

            <!-- Phone -->
            <?php form_input_group(
                'phone',
                'Phone Number',
                'tel',
                true,
                $is_edit ? $customer->phone : set_value('phone'),
                '+971 XX XXX XXXX',
                'Primary contact number'
            ); ?>

            <!-- Mobile -->
            <?php form_input_group(
                'mobile',
                'Mobile Number',
                'tel',
                false,
                $is_edit ? $customer->mobile : set_value('mobile'),
                '+971 5X XXX XXXX',
                'Alternative mobile number'
            ); ?>

            <!-- Gender -->
            <?php form_select_group(
                'gender',
                'Gender',
                [
                    'male' => 'Male',
                    'female' => 'Female',
                    'other' => 'Other'
                ],
                false,
                $is_edit ? $customer->gender : set_value('gender')
            ); ?>

            <!-- Date of Birth -->
            <?php form_input_group(
                'date_of_birth',
                'Date of Birth',
                'date',
                false,
                $is_edit ? $customer->date_of_birth : set_value('date_of_birth')
            ); ?>

            <!-- Nationality -->
            <?php form_input_group(
                'nationality',
                'Nationality',
                'text',
                false,
                $is_edit ? $customer->nationality : set_value('nationality'),
                'e.g., Emirati, Indian, British'
            ); ?>

            <!-- Language -->
            <?php form_select_group(
                'language',
                'Preferred Language',
                [
                    'en' => 'English',
                    'ar' => 'Arabic',
                    'hi' => 'Hindi',
                    'ur' => 'Urdu'
                ],
                false,
                $is_edit ? $customer->language : set_value('language', 'en')
            ); ?>

        </div>
    <?php card_end(); ?>

    <!-- Identification Documents -->
    <?php card_start('Identification Documents', '', true); ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Emirates ID -->
            <?php form_input_group(
                'emirates_id',
                'Emirates ID',
                'text',
                false,
                $is_edit ? $customer->emirates_id : set_value('emirates_id'),
                '784-XXXX-XXXXXXX-X',
                'UAE Emirates ID number'
            ); ?>

            <!-- Passport Number -->
            <?php form_input_group(
                'passport_no',
                'Passport Number',
                'text',
                false,
                $is_edit ? $customer->passport_no : set_value('passport_no'),
                'Passport number'
            ); ?>

            <!-- TRN Number -->
            <?php form_input_group(
                'trn_no',
                'TRN Number',
                'text',
                false,
                $is_edit ? $customer->trn_no : set_value('trn_no'),
                'Tax Registration Number',
                'UAE Tax Registration Number (for corporate)'
            ); ?>

        </div>
    <?php card_end(); ?>

    <!-- Address Information -->
    <?php card_start('Address Information', '', true); ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Address Line 1 -->
            <?php form_input_group(
                'address_line_1',
                'Address Line 1',
                'text',
                false,
                set_value('address_line_1'),
                'Street address, building name'
            ); ?>

            <!-- Address Line 2 -->
            <?php form_input_group(
                'address_line_2',
                'Address Line 2',
                'text',
                false,
                set_value('address_line_2'),
                'Apartment, suite, unit, floor'
            ); ?>

            <!-- City -->
            <?php form_input_group(
                'city',
                'City',
                'text',
                false,
                set_value('city'),
                'e.g., Dubai'
            ); ?>

            <!-- Emirate -->
            <?php form_select_group(
                'emirate',
                'Emirate',
                [
                    'Abu Dhabi' => 'Abu Dhabi',
                    'Dubai' => 'Dubai',
                    'Sharjah' => 'Sharjah',
                    'Ajman' => 'Ajman',
                    'Umm Al Quwain' => 'Umm Al Quwain',
                    'Ras Al Khaimah' => 'Ras Al Khaimah',
                    'Fujairah' => 'Fujairah'
                ],
                false,
                set_value('emirate')
            ); ?>

            <!-- PO Box -->
            <?php form_input_group(
                'po_box',
                'PO Box',
                'text',
                false,
                set_value('po_box'),
                'PO Box number'
            ); ?>

            <!-- Country -->
            <?php form_input_group(
                'country',
                'Country',
                'text',
                false,
                set_value('country', 'UAE'),
                'Country'
            ); ?>

        </div>
    <?php card_end(); ?>

    <!-- Business Settings -->
    <?php card_start('Business Settings', '', true); ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <!-- Customer Group -->
            <div class="form-group">
                <label for="customer_group_id" class="form-label">Customer Group</label>
                <select id="customer_group_id" name="customer_group_id" class="form-select">
                    <option value="">None</option>
                    <?php foreach ($customer_groups as $group): ?>
                        <option value="<?php echo $group->id; ?>" <?php echo ($is_edit && $customer->customer_group_id == $group->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($group->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="form-help">Assign to a customer group for special pricing</p>
            </div>

            <!-- Agent -->
            <div class="form-group">
                <label for="agent_id" class="form-label">Assigned Agent</label>
                <select id="agent_id" name="agent_id" class="form-select">
                    <option value="">None</option>
                    <?php foreach ($agents as $agent): ?>
                        <option value="<?php echo $agent->id; ?>" <?php echo ($is_edit && $customer->agent_id == $agent->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($agent->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="form-help">Agent responsible for this customer</p>
            </div>

            <!-- Credit Limit -->
            <?php form_input_group(
                'credit_limit',
                'Credit Limit',
                'number',
                false,
                $is_edit ? $customer->credit_limit : set_value('credit_limit', '0'),
                '0',
                'Maximum credit allowed (AED)'
            ); ?>

            <!-- Credit Days -->
            <?php form_input_group(
                'credit_days',
                'Credit Days',
                'number',
                false,
                $is_edit ? $customer->credit_days : set_value('credit_days', '0'),
                '0',
                'Payment terms in days'
            ); ?>

        </div>
    <?php card_end(); ?>

    <!-- Additional Information -->
    <?php card_start('Additional Information', '', true); ?>
        <div class="space-y-4">

            <!-- Notes -->
            <div class="form-group">
                <label for="notes" class="form-label">Notes</label>
                <textarea
                    id="notes"
                    name="notes"
                    rows="4"
                    class="form-input"
                    placeholder="Additional notes about this customer..."
                ><?php echo $is_edit ? $customer->notes : set_value('notes'); ?></textarea>
                <p class="form-help">Internal notes (not visible to customer)</p>
            </div>

            <!-- Checkboxes -->
            <div class="flex flex-wrap gap-6">
                <div class="flex items-center gap-2">
                    <input
                        type="checkbox"
                        id="is_active"
                        name="is_active"
                        value="1"
                        <?php echo ($is_edit ? $customer->is_active : true) ? 'checked' : ''; ?>
                        class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500"
                    >
                    <label for="is_active" class="text-sm font-medium text-gray-700 cursor-pointer">
                        Active Customer
                    </label>
                </div>

                <div class="flex items-center gap-2">
                    <input
                        type="checkbox"
                        id="portal_access"
                        name="portal_access"
                        value="1"
                        <?php echo ($is_edit && $customer->portal_access) ? 'checked' : ''; ?>
                        class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500"
                    >
                    <label for="portal_access" class="text-sm font-medium text-gray-700 cursor-pointer">
                        Enable Customer Portal Access
                    </label>
                </div>
            </div>

        </div>
    <?php card_end(); ?>

    <!-- Form Actions -->
    <div class="flex items-center gap-3" data-aos="fade-up">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-save"></i>
            <?php echo $is_edit ? 'Update Customer' : 'Create Customer'; ?>
        </button>
        <a href="<?php echo $is_edit ? base_url('customers/view/' . $customer->id) : base_url('customers'); ?>" class="btn btn-outline btn-lg">
            <i class="fas fa-times"></i>
            Cancel
        </a>
    </div>

</form>

<script>
// Auto-generate customer code display (optional enhancement)
document.getElementById('name').addEventListener('blur', function() {
    if (!this.value) return;

    // You could add AJAX call here to preview the customer code
    console.log('Customer name:', this.value);
});

// Corporate vs Individual field toggles (optional enhancement)
document.getElementById('customer_type').addEventListener('change', function() {
    const isCorporate = this.value === 'corporate';

    // Show/hide TRN field based on type
    const trnGroup = document.querySelector('[name="trn_no"]').closest('.form-group');
    if (trnGroup) {
        trnGroup.style.display = isCorporate ? 'block' : 'none';
    }
});

// Trigger on page load
document.getElementById('customer_type').dispatchEvent(new Event('change'));
</script>
