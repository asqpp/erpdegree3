<?php $this->load->view('components/ui_components'); ?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-6" data-aos="fade-down">
    <div>
        <h1 class="page-title">Insurance Policies</h1>
        <p class="text-gray-600 mt-1">Manage policy issuance, renewals, and endorsements</p>
    </div>
    <div class="flex gap-3">
        <a href="<?php echo base_url('policies/export'); ?>" class="btn btn-outline">
            <i class="fas fa-download"></i>
            Export
        </a>
        <a href="<?php echo base_url('policies/add'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Issue Policy
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

<!-- Search and Filters -->
<?php card_start('Search & Filters', '', true); ?>
    <form method="get" action="<?php echo base_url('policies'); ?>" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="form-label">Search</label>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Policy No, Customer, Plate No..." class="form-input">
            </div>

            <!-- Status -->
            <div>
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" <?php echo $filter_status === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="expired" <?php echo $filter_status === 'expired' ? 'selected' : ''; ?>>Expired</option>
                    <option value="cancelled" <?php echo $filter_status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    <option value="renewed" <?php echo $filter_status === 'renewed' ? 'selected' : ''; ?>>Renewed</option>
                </select>
            </div>

            <!-- Policy Type -->
            <div>
                <label class="form-label">Policy Type</label>
                <select name="policy_type" class="form-select">
                    <option value="">All Types</option>
                    <?php foreach ($policy_types as $type): ?>
                        <option value="<?php echo $type->id; ?>" <?php echo $filter_policy_type == $type->id ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($type->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="form-label">From Date</label>
                <input type="date" name="date_from" value="<?php echo $filter_date_from; ?>" class="form-input">
            </div>

            <!-- Date To -->
            <div>
                <label class="form-label">To Date</label>
                <input type="date" name="date_to" value="<?php echo $filter_date_to; ?>" class="form-input">
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Search
            </button>
            <a href="<?php echo base_url('policies'); ?>" class="btn btn-outline">
                <i class="fas fa-redo"></i> Reset
            </a>
        </div>
    </form>
<?php card_end(); ?>

<!-- Results -->
<div class="flex items-center justify-between mb-4 text-sm text-gray-600" data-aos="fade-up">
    <div>Showing <strong><?php echo count($policies); ?></strong> of <strong><?php echo $total_rows; ?></strong> policies</div>
</div>

<!-- Policies Table -->
<?php card_start('', '', true); ?>
    <?php if (count($policies) > 0): ?>
        <div class="overflow-x-auto">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Policy No</th>
                        <th>Customer</th>
                        <th>Type</th>
                        <th>Issue Date</th>
                        <th>Expiry Date</th>
                        <th>Sum Insured</th>
                        <th>Premium</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($policies as $index => $policy): ?>
                        <tr data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                            <td>
                                <a href="<?php echo base_url('policies/view/' . $policy->id); ?>"
                                    class="text-primary-600 hover:text-primary-700 font-medium">
                                    <?php echo htmlspecialchars($policy->policy_no); ?>
                                </a>
                            </td>
                            <td>
                                <div class="font-medium"><?php echo htmlspecialchars($policy->customer_name); ?></div>
                                <?php if ($policy->vehicle_plate_no): ?>
                                    <div class="text-xs text-gray-500">
                                        <i class="fas fa-car"></i> <?php echo htmlspecialchars($policy->vehicle_plate_no); ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo badge(htmlspecialchars($policy->policy_type_name), 'info'); ?></td>
                            <td><?php echo format_date($policy->issue_date); ?></td>
                            <td>
                                <?php
                                $expiry = strtotime($policy->expiry_date);
                                $today = time();
                                $days_to_expiry = ceil(($expiry - $today) / 86400);
                                ?>
                                <div><?php echo format_date($policy->expiry_date); ?></div>
                                <?php if ($policy->status == 'active' && $days_to_expiry <= 30 && $days_to_expiry > 0): ?>
                                    <div class="text-xs text-warning-600">
                                        <i class="fas fa-exclamation-triangle"></i> <?php echo $days_to_expiry; ?> days left
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo format_currency($policy->sum_insured, $policy->currency_symbol); ?></td>
                            <td>
                                <div class="font-medium"><?php echo format_currency($policy->total_premium, $policy->currency_symbol); ?></div>
                                <div class="text-xs text-gray-500">+VAT</div>
                            </td>
                            <td><?php echo status_badge($policy->status); ?></td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?php echo base_url('policies/view/' . $policy->id); ?>"
                                        class="btn btn-sm btn-ghost" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($policy->status == 'active'): ?>
                                        <a href="<?php echo base_url('policies/renew/' . $policy->id); ?>"
                                            class="btn btn-sm btn-ghost text-success-600" title="Renew">
                                            <i class="fas fa-redo"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_pages > 1): ?>
            <div class="mt-6">
                <?php echo pagination($current_page, $total_pages, base_url('policies')); ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="text-center py-12">
            <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                <i class="fas fa-file-contract text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No policies found</h3>
            <p class="text-gray-600 mb-6">Get started by issuing your first policy.</p>
            <a href="<?php echo base_url('policies/add'); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Issue First Policy
            </a>
        </div>
    <?php endif; ?>
<?php card_end(); ?>
