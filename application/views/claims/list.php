<?php $this->load->view('components/ui_components'); ?>

<div class="flex items-center justify-between mb-6" data-aos="fade-down">
    <div>
        <h1 class="page-title">Claims Management</h1>
        <p class="text-gray-600 mt-1">Process insurance claims from registration to settlement</p>
    </div>
    <div class="flex gap-3">
        <a href="<?php echo base_url('claims/export'); ?>" class="btn btn-outline">
            <i class="fas fa-download"></i> Export
        </a>
        <a href="<?php echo base_url('claims/add'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Register Claim
        </a>
    </div>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="stat-card bg-gradient-warning" data-aos="fade-up">
        <p class="stat-label">Registered</p>
        <h3 class="stat-value"><?php echo $stats['registered'] ?? 0; ?></h3>
    </div>
    <div class="stat-card bg-gradient-info" data-aos="fade-up" data-aos-delay="100">
        <p class="stat-label">Under Investigation</p>
        <h3 class="stat-value"><?php echo $stats['investigating'] ?? 0; ?></h3>
    </div>
    <div class="stat-card bg-gradient-success" data-aos="fade-up" data-aos-delay="200">
        <p class="stat-label">Approved</p>
        <h3 class="stat-value"><?php echo $stats['approved'] ?? 0; ?></h3>
    </div>
    <div class="stat-card bg-gradient-primary" data-aos="fade-up" data-aos-delay="300">
        <p class="stat-label">Settled</p>
        <h3 class="stat-value"><?php echo $stats['settled'] ?? 0; ?></h3>
    </div>
</div>

<!-- Search & Filters -->
<?php card_start('Search & Filters', '', true); ?>
    <form method="get" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                placeholder="Claim No, Policy No, Customer..." class="form-input">
        </div>
        <select name="status" class="form-select">
            <option value="">All Status</option>
            <option value="registered" <?php echo $filter_status === 'registered' ? 'selected' : ''; ?>>Registered</option>
            <option value="investigating" <?php echo $filter_status === 'investigating' ? 'selected' : ''; ?>>Investigating</option>
            <option value="approved" <?php echo $filter_status === 'approved' ? 'selected' : ''; ?>>Approved</option>
            <option value="rejected" <?php echo $filter_status === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
            <option value="settled" <?php echo $filter_status === 'settled' ? 'selected' : ''; ?>>Settled</option>
        </select>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Search
        </button>
    </form>
<?php card_end(); ?>

<!-- Claims Table -->
<?php card_start('', '', true); ?>
    <?php if (count($claims) > 0): ?>
        <div class="overflow-x-auto">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Claim No</th>
                        <th>Policy No</th>
                        <th>Customer</th>
                        <th>Type</th>
                        <th>Claim Date</th>
                        <th>Amount</th>
                        <th>Approved</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($claims as $claim): ?>
                    <tr data-aos="fade-up">
                        <td>
                            <a href="<?php echo base_url('claims/view/' . $claim->id); ?>"
                                class="text-primary-600 font-medium">
                                <?php echo htmlspecialchars($claim->claim_no); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($claim->policy_no); ?></td>
                        <td><?php echo htmlspecialchars($claim->customer_name); ?></td>
                        <td><?php echo badge($claim->claim_type_name, 'info'); ?></td>
                        <td><?php echo format_date($claim->claim_date); ?></td>
                        <td><?php echo format_currency($claim->claim_amount); ?></td>
                        <td><?php echo format_currency($claim->approved_amount ?: 0); ?></td>
                        <td><?php echo status_badge($claim->status); ?></td>
                        <td>
                            <a href="<?php echo base_url('claims/view/' . $claim->id); ?>"
                                class="btn btn-sm btn-ghost">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center py-12">
            <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold mb-2">No claims found</h3>
            <a href="<?php echo base_url('claims/add'); ?>" class="btn btn-primary mt-4">
                <i class="fas fa-plus"></i> Register First Claim
            </a>
        </div>
    <?php endif; ?>
<?php card_end(); ?>
