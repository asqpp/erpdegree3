<?php $this->load->view('components/ui_components'); ?>

<!-- Page Header -->
<div class="flex items-start justify-between mb-6" data-aos="fade-down">
    <div class="flex items-start gap-4">
        <!-- Customer Avatar -->
        <div class="w-16 h-16 rounded-full bg-gradient-primary flex items-center justify-center text-white text-2xl font-bold">
            <?php echo strtoupper(substr($customer->name, 0, 1)); ?>
        </div>

        <div>
            <h1 class="page-title mb-1"><?php echo htmlspecialchars($customer->name); ?></h1>
            <div class="flex items-center gap-3 text-sm text-gray-600">
                <span class="font-mono"><?php echo htmlspecialchars($customer->code); ?></span>
                <span>•</span>
                <?php echo badge(ucfirst($customer->customer_type), $customer->customer_type == 'corporate' ? 'info' : 'primary'); ?>
                <?php echo $customer->is_active ? badge('Active', 'success') : badge('Inactive', 'gray'); ?>
                <?php echo status_badge($customer->kyc_status); ?>
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <a href="<?php echo base_url('customers/edit/' . $customer->id); ?>" class="btn btn-primary">
            <i class="fas fa-edit"></i>
            Edit
        </a>
        <button onclick="deleteCustomer()" class="btn btn-danger">
            <i class="fas fa-trash"></i>
            Delete
        </button>
    </div>
</div>

<!-- Flash Messages -->
<?php if ($this->session->flashdata('success')): ?>
    <?php echo alert($this->session->flashdata('success'), 'success'); ?>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
    <?php echo alert($this->session->flashdata('error'), 'danger'); ?>
<?php endif; ?>

<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="stat-card bg-gradient-primary" data-aos="fade-up" data-aos-delay="0">
        <div class="flex items-start justify-between">
            <div>
                <p class="stat-label">Total Policies</p>
                <h3 class="stat-value"><?php echo count($policies); ?></h3>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                <i class="fas fa-file-contract text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="stat-card bg-gradient-success" data-aos="fade-up" data-aos-delay="100">
        <div class="flex items-start justify-between">
            <div>
                <p class="stat-label">Documents</p>
                <h3 class="stat-value"><?php echo count($documents); ?></h3>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                <i class="fas fa-folder text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="stat-card bg-gradient-warning" data-aos="fade-up" data-aos-delay="200">
        <div class="flex items-start justify-between">
            <div>
                <p class="stat-label">Credit Limit</p>
                <h3 class="stat-value"><?php echo format_currency($customer->credit_limit); ?></h3>
                <p class="text-sm mt-1"><?php echo $customer->credit_days; ?> days</p>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                <i class="fas fa-credit-card text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="stat-card bg-gradient-info" data-aos="fade-up" data-aos-delay="300">
        <div class="flex items-start justify-between">
            <div>
                <p class="stat-label">Portal Access</p>
                <h3 class="stat-value">
                    <?php echo $customer->portal_access ? 'Enabled' : 'Disabled'; ?>
                </h3>
                <button
                    onclick="togglePortal()"
                    class="text-sm mt-2 underline hover:no-underline"
                >
                    <?php echo $customer->portal_access ? 'Disable' : 'Enable'; ?>
                </button>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                <i class="fas fa-globe text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabs -->
<?php tabs_start(['Details', 'Documents & KYC', 'Policies', 'Contacts & Addresses', 'Activity Log']); ?>

    <!-- Tab 1: Details -->
    <?php tab_content_start(0); ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Personal Information -->
            <?php card_start('Personal Information'); ?>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-600">Email</dt>
                        <dd class="font-medium text-gray-900 mt-1">
                            <i class="fas fa-envelope text-gray-400"></i>
                            <?php echo htmlspecialchars($customer->email); ?>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-600">Phone</dt>
                        <dd class="font-medium text-gray-900 mt-1">
                            <i class="fas fa-phone text-gray-400"></i>
                            <?php echo htmlspecialchars($customer->phone); ?>
                        </dd>
                    </div>
                    <?php if ($customer->mobile): ?>
                    <div>
                        <dt class="text-gray-600">Mobile</dt>
                        <dd class="font-medium text-gray-900 mt-1">
                            <i class="fas fa-mobile text-gray-400"></i>
                            <?php echo htmlspecialchars($customer->mobile); ?>
                        </dd>
                    </div>
                    <?php endif; ?>
                    <?php if ($customer->date_of_birth): ?>
                    <div>
                        <dt class="text-gray-600">Date of Birth</dt>
                        <dd class="font-medium text-gray-900 mt-1">
                            <?php echo format_date($customer->date_of_birth); ?>
                        </dd>
                    </div>
                    <?php endif; ?>
                    <?php if ($customer->gender): ?>
                    <div>
                        <dt class="text-gray-600">Gender</dt>
                        <dd class="font-medium text-gray-900 mt-1">
                            <?php echo ucfirst($customer->gender); ?>
                        </dd>
                    </div>
                    <?php endif; ?>
                    <?php if ($customer->nationality): ?>
                    <div>
                        <dt class="text-gray-600">Nationality</dt>
                        <dd class="font-medium text-gray-900 mt-1">
                            <?php echo htmlspecialchars($customer->nationality); ?>
                        </dd>
                    </div>
                    <?php endif; ?>
                    <?php if ($customer->language): ?>
                    <div>
                        <dt class="text-gray-600">Language</dt>
                        <dd class="font-medium text-gray-900 mt-1">
                            <?php echo strtoupper($customer->language); ?>
                        </dd>
                    </div>
                    <?php endif; ?>
                </dl>
            <?php card_end(); ?>

            <!-- Identification -->
            <?php card_start('Identification Documents'); ?>
                <dl class="grid grid-cols-1 gap-4 text-sm">
                    <?php if ($customer->emirates_id): ?>
                    <div>
                        <dt class="text-gray-600">Emirates ID</dt>
                        <dd class="font-medium text-gray-900 mt-1 font-mono">
                            <?php echo htmlspecialchars($customer->emirates_id); ?>
                        </dd>
                    </div>
                    <?php endif; ?>
                    <?php if ($customer->passport_no): ?>
                    <div>
                        <dt class="text-gray-600">Passport Number</dt>
                        <dd class="font-medium text-gray-900 mt-1 font-mono">
                            <?php echo htmlspecialchars($customer->passport_no); ?>
                        </dd>
                    </div>
                    <?php endif; ?>
                    <?php if ($customer->trn_no): ?>
                    <div>
                        <dt class="text-gray-600">TRN Number</dt>
                        <dd class="font-medium text-gray-900 mt-1 font-mono">
                            <?php echo htmlspecialchars($customer->trn_no); ?>
                        </dd>
                    </div>
                    <?php endif; ?>
                </dl>
            <?php card_end(); ?>

            <!-- Business Information -->
            <?php card_start('Business Information'); ?>
                <dl class="grid grid-cols-1 gap-4 text-sm">
                    <?php if ($customer->group_name): ?>
                    <div>
                        <dt class="text-gray-600">Customer Group</dt>
                        <dd class="font-medium text-gray-900 mt-1">
                            <i class="fas fa-users text-gray-400"></i>
                            <?php echo htmlspecialchars($customer->group_name); ?>
                        </dd>
                    </div>
                    <?php endif; ?>
                    <?php if ($customer->agent_name): ?>
                    <div>
                        <dt class="text-gray-600">Assigned Agent</dt>
                        <dd class="font-medium text-gray-900 mt-1">
                            <i class="fas fa-user-tie text-gray-400"></i>
                            <?php echo htmlspecialchars($customer->agent_name); ?>
                        </dd>
                    </div>
                    <?php endif; ?>
                    <div>
                        <dt class="text-gray-600">Credit Limit</dt>
                        <dd class="font-medium text-gray-900 mt-1">
                            <?php echo format_currency($customer->credit_limit); ?>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-600">Payment Terms</dt>
                        <dd class="font-medium text-gray-900 mt-1">
                            <?php echo $customer->credit_days; ?> days
                        </dd>
                    </div>
                </dl>
            <?php card_end(); ?>

            <!-- Additional Notes -->
            <?php if ($customer->notes): ?>
            <?php card_start('Notes'); ?>
                <p class="text-sm text-gray-700 whitespace-pre-wrap"><?php echo htmlspecialchars($customer->notes); ?></p>
            <?php card_end(); ?>
            <?php endif; ?>

        </div>
    <?php tab_content_end(); ?>

    <!-- Tab 2: Documents & KYC -->
    <?php tab_content_start(1); ?>
        <div class="space-y-6">

            <!-- KYC Status Card -->
            <?php card_start('KYC Status'); ?>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-2">Current KYC Status</p>
                        <div class="text-lg"><?php echo status_badge($customer->kyc_status); ?></div>
                        <?php if ($customer->kyc_verified_at): ?>
                            <p class="text-xs text-gray-500 mt-2">
                                Verified on <?php echo format_date($customer->kyc_verified_at); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <button onclick="updateKYCStatus()" class="btn btn-primary">
                            <i class="fas fa-check-circle"></i>
                            Update KYC Status
                        </button>
                    </div>
                </div>
            <?php card_end(); ?>

            <!-- Upload Document -->
            <?php card_start('Upload Document'); ?>
                <form id="uploadForm" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="form-group">
                            <label class="form-label form-label-required">Document Type</label>
                            <select name="document_type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="emirates_id">Emirates ID</option>
                                <option value="passport">Passport</option>
                                <option value="visa">Visa</option>
                                <option value="trade_license">Trade License</option>
                                <option value="proof_of_address">Proof of Address</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label form-label-required">Document Name</label>
                            <input type="text" name="document_name" class="form-input" placeholder="e.g., Front Copy" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label form-label-required">Select File</label>
                            <input type="file" name="document" id="documentFile" class="form-input" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i>
                        Upload Document
                    </button>
                </form>
            <?php card_end(); ?>

            <!-- Documents List -->
            <?php card_start('Uploaded Documents (' . count($documents) . ')'); ?>
                <?php if (count($documents) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Document Type</th>
                                    <th>Document Name</th>
                                    <th>File Size</th>
                                    <th>Uploaded By</th>
                                    <th>Upload Date</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($documents as $doc): ?>
                                <tr>
                                    <td><?php echo badge(ucwords(str_replace('_', ' ', $doc->document_type)), 'primary'); ?></td>
                                    <td class="font-medium"><?php echo htmlspecialchars($doc->document_name); ?></td>
                                    <td class="text-sm text-gray-600">
                                        <?php echo number_format($doc->file_size); ?> KB
                                    </td>
                                    <td class="text-sm">
                                        <?php echo htmlspecialchars($doc->uploaded_by_name); ?>
                                    </td>
                                    <td class="text-sm">
                                        <?php echo format_date($doc->uploaded_at, 'd M Y, H:i'); ?>
                                    </td>
                                    <td class="text-right">
                                        <a href="<?php echo base_url($doc->file_path); ?>" target="_blank" class="btn btn-sm btn-ghost" title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-folder-open text-4xl mb-3"></i>
                        <p>No documents uploaded yet</p>
                    </div>
                <?php endif; ?>
            <?php card_end(); ?>

        </div>
    <?php tab_content_end(); ?>

    <!-- Tab 3: Policies -->
    <?php tab_content_start(2); ?>
        <?php card_start('Customer Policies (' . count($policies) . ')'); ?>
            <?php if (count($policies) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Policy No</th>
                                <th>Type</th>
                                <th>Issue Date</th>
                                <th>Expiry Date</th>
                                <th>Sum Insured</th>
                                <th>Premium</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($policies as $policy): ?>
                            <tr>
                                <td>
                                    <a href="<?php echo base_url('policies/view/' . $policy->id); ?>" class="text-primary-600 hover:text-primary-700 font-medium">
                                        <?php echo htmlspecialchars($policy->policy_no); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($policy->policy_type_name); ?></td>
                                <td><?php echo format_date($policy->issue_date); ?></td>
                                <td><?php echo format_date($policy->expiry_date); ?></td>
                                <td><?php echo format_currency($policy->sum_insured, $policy->currency_symbol); ?></td>
                                <td><?php echo format_currency($policy->premium_amount, $policy->currency_symbol); ?></td>
                                <td><?php echo status_badge($policy->status); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-file-contract text-4xl mb-3"></i>
                    <p class="mb-4">No policies found for this customer</p>
                    <a href="<?php echo base_url('policies/add?customer_id=' . $customer->id); ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Issue Policy
                    </a>
                </div>
            <?php endif; ?>
        <?php card_end(); ?>
    <?php tab_content_end(); ?>

    <!-- Tab 4: Contacts & Addresses -->
    <?php tab_content_start(3); ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Contacts -->
            <?php card_start('Contacts (' . count($contacts) . ')'); ?>
                <?php if (count($contacts) > 0): ?>
                    <div class="space-y-4">
                        <?php foreach ($contacts as $contact): ?>
                        <div class="border-b border-gray-200 pb-4 last:border-0">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900">
                                        <?php echo htmlspecialchars($contact->contact_name); ?>
                                        <?php if ($contact->is_primary): ?>
                                            <?php echo badge('Primary', 'success'); ?>
                                        <?php endif; ?>
                                    </h4>
                                    <?php if ($contact->designation): ?>
                                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($contact->designation); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="mt-2 text-sm text-gray-600 space-y-1">
                                <?php if ($contact->email): ?>
                                    <div><i class="fas fa-envelope w-4"></i> <?php echo htmlspecialchars($contact->email); ?></div>
                                <?php endif; ?>
                                <?php if ($contact->phone): ?>
                                    <div><i class="fas fa-phone w-4"></i> <?php echo htmlspecialchars($contact->phone); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-center text-gray-500 py-4">No additional contacts</p>
                <?php endif; ?>
            <?php card_end(); ?>

            <!-- Addresses -->
            <?php card_start('Addresses (' . count($addresses) . ')'); ?>
                <?php if (count($addresses) > 0): ?>
                    <div class="space-y-4">
                        <?php foreach ($addresses as $address): ?>
                        <div class="border-b border-gray-200 pb-4 last:border-0">
                            <div class="flex items-start justify-between mb-2">
                                <h4 class="font-medium text-gray-900">
                                    <?php echo ucfirst($address->address_type); ?> Address
                                    <?php if ($address->is_primary): ?>
                                        <?php echo badge('Primary', 'success'); ?>
                                    <?php endif; ?>
                                </h4>
                            </div>
                            <div class="text-sm text-gray-600">
                                <?php if ($address->address_line_1): ?>
                                    <div><?php echo htmlspecialchars($address->address_line_1); ?></div>
                                <?php endif; ?>
                                <?php if ($address->address_line_2): ?>
                                    <div><?php echo htmlspecialchars($address->address_line_2); ?></div>
                                <?php endif; ?>
                                <div>
                                    <?php echo implode(', ', array_filter([
                                        $address->city,
                                        $address->emirate,
                                        $address->country
                                    ])); ?>
                                </div>
                                <?php if ($address->po_box): ?>
                                    <div>PO Box: <?php echo htmlspecialchars($address->po_box); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-center text-gray-500 py-4">No addresses added</p>
                <?php endif; ?>
            <?php card_end(); ?>

        </div>
    <?php tab_content_end(); ?>

    <!-- Tab 5: Activity Log -->
    <?php tab_content_start(4); ?>
        <?php card_start('Recent Activities'); ?>
            <?php if (count($activities) > 0): ?>
                <div class="space-y-4">
                    <?php foreach ($activities as $activity): ?>
                    <div class="flex gap-4 border-b border-gray-200 pb-4 last:border-0">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-history text-gray-600"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($activity->action); ?></p>
                            <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                                <span>
                                    <i class="fas fa-user"></i>
                                    <?php echo htmlspecialchars($activity->user_name); ?>
                                </span>
                                <span>•</span>
                                <span>
                                    <i class="fas fa-clock"></i>
                                    <?php echo format_date($activity->created_at, 'd M Y, H:i'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-history text-4xl mb-3"></i>
                    <p>No activity recorded yet</p>
                </div>
            <?php endif; ?>
        <?php card_end(); ?>
    <?php tab_content_end(); ?>

<?php tabs_end(); ?>

<script>
// Delete customer
async function deleteCustomer() {
    const confirmed = await Utils.confirm(
        'Delete Customer?',
        'Are you sure you want to delete "<?php echo addslashes($customer->name); ?>"? This action cannot be undone.'
    );

    if (confirmed) {
        window.location.href = '<?php echo base_url("customers/delete/" . $customer->id); ?>';
    }
}

// Toggle portal access
async function togglePortal() {
    const response = await Utils.ajax('<?php echo base_url("customers/toggle_portal/" . $customer->id); ?>', {
        method: 'POST'
    });

    if (response.success) {
        Utils.showToast(response.portal_access ? 'Portal access enabled' : 'Portal access disabled', 'success');
        setTimeout(() => location.reload(), 1000);
    } else {
        Utils.showToast(response.message || 'Failed to update portal access', 'error');
    }
}

// Upload document
document.getElementById('uploadForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    try {
        const response = await fetch('<?php echo base_url("customers/upload_document/" . $customer->id); ?>', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            Utils.showToast(result.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            Utils.showToast(result.message, 'error');
        }
    } catch (error) {
        Utils.showToast('Failed to upload document', 'error');
    }
});

// Update KYC status
async function updateKYCStatus() {
    const { value: status } = await Swal.fire({
        title: 'Update KYC Status',
        input: 'select',
        inputOptions: {
            'pending': 'Pending',
            'submitted': 'Submitted',
            'approved': 'Approved',
            'rejected': 'Rejected'
        },
        inputValue: '<?php echo $customer->kyc_status; ?>',
        inputPlaceholder: 'Select KYC Status',
        showCancelButton: true,
        inputValidator: (value) => {
            if (!value) {
                return 'Please select a status'
            }
        }
    });

    if (status) {
        const { value: notes } = await Swal.fire({
            title: 'Add Notes (Optional)',
            input: 'textarea',
            inputPlaceholder: 'Enter any notes...',
            showCancelButton: true
        });

        const response = await Utils.ajax('<?php echo base_url("customers/update_kyc_status/" . $customer->id); ?>', {
            method: 'POST',
            data: { kyc_status: status, notes: notes || '' }
        });

        if (response.success) {
            Utils.showToast(response.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            Utils.showToast(response.message || 'Failed to update KYC status', 'error');
        }
    }
}
</script>
