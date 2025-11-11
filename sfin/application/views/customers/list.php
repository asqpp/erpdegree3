<?php $this->load->view('components/ui_components'); ?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-6" data-aos="fade-down">
    <div>
        <h1 class="page-title">Customers</h1>
        <p class="text-gray-600 mt-1">Manage your customer database</p>
    </div>
    <div class="flex gap-3">
        <a href="<?php echo base_url('customers/export'); ?>" class="btn btn-outline">
            <i class="fas fa-download"></i>
            Export
        </a>
        <a href="<?php echo base_url('customers/add'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Add Customer
        </a>
    </div>
</div>

<!-- Flash Messages -->
<?php if ($this->session->flashdata('success')): ?>
    <?php echo alert($this->session->flashdata('success'), 'success'); ?>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
    <?php echo alert($this->session->flashdata('error'), 'danger'); ?>
<?php endif; ?>

<!-- Search and Filters Card -->
<?php card_start('Search & Filters', '', true); ?>
    <form method="get" action="<?php echo base_url('customers'); ?>" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="form-label">Search</label>
                <div class="relative">
                    <input
                        type="text"
                        name="search"
                        value="<?php echo htmlspecialchars($search); ?>"
                        placeholder="Code, Name, Email, Phone, Emirates ID..."
                        class="form-input pl-10"
                    >
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="1" <?php echo $filter_status === '1' ? 'selected' : ''; ?>>Active</option>
                    <option value="0" <?php echo $filter_status === '0' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>

            <!-- Customer Type Filter -->
            <div>
                <label class="form-label">Type</label>
                <select name="customer_type" class="form-select">
                    <option value="">All Types</option>
                    <option value="individual" <?php echo $filter_customer_type === 'individual' ? 'selected' : ''; ?>>Individual</option>
                    <option value="corporate" <?php echo $filter_customer_type === 'corporate' ? 'selected' : ''; ?>>Corporate</option>
                </select>
            </div>

            <!-- KYC Status Filter -->
            <div>
                <label class="form-label">KYC Status</label>
                <select name="kyc_status" class="form-select">
                    <option value="">All KYC Status</option>
                    <option value="pending" <?php echo $filter_kyc_status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="submitted" <?php echo $filter_kyc_status === 'submitted' ? 'selected' : ''; ?>>Submitted</option>
                    <option value="approved" <?php echo $filter_kyc_status === 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="rejected" <?php echo $filter_kyc_status === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i>
                Search
            </button>
            <a href="<?php echo base_url('customers'); ?>" class="btn btn-outline">
                <i class="fas fa-redo"></i>
                Reset
            </a>
        </div>
    </form>
<?php card_end(); ?>

<!-- Results Summary -->
<div class="flex items-center justify-between mb-4 text-sm text-gray-600" data-aos="fade-up">
    <div>
        Showing <strong><?php echo count($customers); ?></strong> of <strong><?php echo $total_rows; ?></strong> customers
    </div>
    <?php if ($search || $filter_status !== null || $filter_customer_type || $filter_kyc_status): ?>
        <div class="text-primary-600">
            <i class="fas fa-filter"></i>
            Filters applied
        </div>
    <?php endif; ?>
</div>

<!-- Customers Table Card -->
<?php card_start('', '', true); ?>
    <?php if (count($customers) > 0): ?>
        <div class="overflow-x-auto">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Customer Name</th>
                        <th>Type</th>
                        <th>Email / Phone</th>
                        <th>Emirates ID</th>
                        <th>KYC Status</th>
                        <th>Status</th>
                        <th>Portal</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $index => $customer): ?>
                        <tr data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                            <td>
                                <a href="<?php echo base_url('customers/view/' . $customer->id); ?>" class="text-primary-600 hover:text-primary-700 font-medium">
                                    <?php echo htmlspecialchars($customer->code); ?>
                                </a>
                            </td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-primary flex items-center justify-center text-white font-semibold">
                                        <?php echo strtoupper(substr($customer->name, 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">
                                            <?php echo htmlspecialchars($customer->name); ?>
                                        </div>
                                        <?php if ($customer->group_name): ?>
                                            <div class="text-xs text-gray-500">
                                                <i class="fas fa-users"></i>
                                                <?php echo htmlspecialchars($customer->group_name); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php echo badge(ucfirst($customer->customer_type), $customer->customer_type == 'corporate' ? 'info' : 'primary'); ?>
                            </td>
                            <td>
                                <div class="text-sm">
                                    <div class="text-gray-900">
                                        <i class="fas fa-envelope text-gray-400"></i>
                                        <?php echo htmlspecialchars($customer->email); ?>
                                    </div>
                                    <div class="text-gray-600 mt-1">
                                        <i class="fas fa-phone text-gray-400"></i>
                                        <?php echo htmlspecialchars($customer->phone); ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if ($customer->emirates_id): ?>
                                    <span class="text-sm font-mono"><?php echo htmlspecialchars($customer->emirates_id); ?></span>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo status_badge($customer->kyc_status); ?>
                            </td>
                            <td>
                                <?php echo $customer->is_active ? badge('Active', 'success') : badge('Inactive', 'gray'); ?>
                            </td>
                            <td class="text-center">
                                <?php if ($customer->portal_access): ?>
                                    <i class="fas fa-check-circle text-success-600" title="Portal Access Enabled"></i>
                                <?php else: ?>
                                    <i class="fas fa-times-circle text-gray-400" title="Portal Access Disabled"></i>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?php echo base_url('customers/view/' . $customer->id); ?>" class="btn btn-sm btn-ghost" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo base_url('customers/edit/' . $customer->id); ?>" class="btn btn-sm btn-ghost" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button
                                        onclick="deleteCustomer(<?php echo $customer->id; ?>, '<?php echo htmlspecialchars($customer->name); ?>')"
                                        class="btn btn-sm btn-ghost text-danger-600"
                                        title="Delete"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="mt-6">
                <?php echo pagination($current_page, $total_pages, base_url('customers')); ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No customers found</h3>
            <p class="text-gray-600 mb-6">
                <?php if ($search || $filter_status !== null || $filter_customer_type || $filter_kyc_status): ?>
                    No customers match your search criteria. Try adjusting your filters.
                <?php else: ?>
                    Get started by adding your first customer.
                <?php endif; ?>
            </p>
            <?php if (!$search && !$filter_status && !$filter_customer_type && !$filter_kyc_status): ?>
                <a href="<?php echo base_url('customers/add'); ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Add Your First Customer
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php card_end(); ?>

<script>
// Delete customer confirmation
async function deleteCustomer(id, name) {
    const confirmed = await Utils.confirm(
        'Delete Customer?',
        `Are you sure you want to delete "${name}"? This action cannot be undone.`
    );

    if (confirmed) {
        window.location.href = '<?php echo base_url("customers/delete/"); ?>' + id;
    }
}

// Auto-submit form on filter change
document.querySelectorAll('select[name="status"], select[name="customer_type"], select[name="kyc_status"]').forEach(select => {
    select.addEventListener('change', function() {
        this.form.submit();
    });
});
</script>
