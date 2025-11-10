<!-- =========================================
     REUSABLE UI COMPONENTS LIBRARY
     Use these components across your application
     ========================================= -->

<!-- EXAMPLE USAGE:
     <?php $this->load->view('components/ui_components'); ?>
-->

<!-- Button Examples -->
<div class="hidden" id="component-examples">
    <!-- Primary Button -->
    <button class="btn btn-primary">
        <i class="fas fa-save"></i>
        Save
    </button>

    <!-- Secondary Button -->
    <button class="btn btn-secondary">
        <i class="fas fa-edit"></i>
        Edit
    </button>

    <!-- Danger Button -->
    <button class="btn btn-danger">
        <i class="fas fa-trash"></i>
        Delete
    </button>

    <!-- Success Button -->
    <button class="btn btn-success">
        <i class="fas fa-check"></i>
        Approve
    </button>

    <!-- Warning Button -->
    <button class="btn btn-warning">
        <i class="fas fa-exclamation"></i>
        Warning
    </button>

    <!-- Info Button -->
    <button class="btn btn-info">
        <i class="fas fa-info"></i>
        Info
    </button>

    <!-- Outline Button -->
    <button class="btn btn-outline">
        Cancel
    </button>

    <!-- Ghost Button -->
    <button class="btn btn-ghost">
        More Options
    </button>
</div>

<!-- =========================================
     STAT CARD COMPONENT
     ========================================= -->
<?php if (!function_exists('render_stat_card')): ?>
<?php function render_stat_card($title, $value, $change, $icon, $gradient_class, $delay = 0) { ?>
    <div class="stat-card <?php echo $gradient_class; ?>" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
        <div class="flex items-start justify-between">
            <div>
                <p class="stat-label"><?php echo $title; ?></p>
                <h3 class="stat-value"><?php echo $value; ?></h3>
                <?php if ($change): ?>
                    <p class="text-sm mt-2"><?php echo $change; ?></p>
                <?php endif; ?>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                <i class="<?php echo $icon; ?> text-2xl"></i>
            </div>
        </div>
    </div>
<?php }} ?>

<!-- =========================================
     CARD COMPONENT
     ========================================= -->
<?php if (!function_exists('card_start')): ?>
<?php function card_start($title = '', $actions = '', $aos = true) { ?>
    <div class="card" <?php if ($aos): ?>data-aos="fade-up"<?php endif; ?>>
        <?php if ($title): ?>
            <div class="card-header">
                <h3 class="card-title"><?php echo $title; ?></h3>
                <?php if ($actions): ?>
                    <?php echo $actions; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="card-body">
<?php }} ?>

<?php if (!function_exists('card_end')): ?>
<?php function card_end() { ?>
        </div>
    </div>
<?php }} ?>

<!-- =========================================
     BADGE COMPONENT
     ========================================= -->
<?php if (!function_exists('badge')): ?>
<?php function badge($text, $type = 'primary') { ?>
    <span class="badge badge-<?php echo $type; ?>"><?php echo $text; ?></span>
<?php }} ?>

<!-- =========================================
     ALERT COMPONENT
     ========================================= -->
<?php if (!function_exists('alert')): ?>
<?php function alert($message, $type = 'info', $dismissible = true) { ?>
    <div class="alert alert-<?php echo $type; ?>" x-data="{ show: true }" x-show="show">
        <div class="flex items-start gap-3">
            <div class="flex-1"><?php echo $message; ?></div>
            <?php if ($dismissible): ?>
                <button @click="show = false" class="text-current opacity-70 hover:opacity-100">
                    <i class="fas fa-times"></i>
                </button>
            <?php endif; ?>
        </div>
    </div>
<?php }} ?>

<!-- =========================================
     MODAL COMPONENT
     ========================================= -->
<?php if (!function_exists('modal_start')): ?>
<?php function modal_start($id, $title) { ?>
    <div x-data="modal()" x-cloak>
        <div x-show="show" class="modal-overlay" @click.self="close()">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h3 class="modal-title"><?php echo $title; ?></h3>
                    <button @click="close()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
<?php }} ?>

<?php if (!function_exists('modal_end')): ?>
<?php function modal_end($footer_buttons = '') { ?>
                </div>
                <?php if ($footer_buttons): ?>
                    <div class="modal-footer">
                        <?php echo $footer_buttons; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php }} ?>

<!-- =========================================
     TABLE COMPONENT
     ========================================= -->
<?php if (!function_exists('table_start')): ?>
<?php function table_start($headers, $striped = false) { ?>
    <div class="overflow-x-auto">
        <table class="table <?php if ($striped): ?>table-striped<?php endif; ?>">
            <thead>
                <tr>
                    <?php foreach ($headers as $header): ?>
                        <th><?php echo $header; ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
<?php }} ?>

<?php if (!function_exists('table_end')): ?>
<?php function table_end() { ?>
            </tbody>
        </table>
    </div>
<?php }} ?>

<!-- =========================================
     FORM INPUT COMPONENT
     ========================================= -->
<?php if (!function_exists('form_input_group')): ?>
<?php function form_input_group($name, $label, $type = 'text', $required = false, $value = '', $placeholder = '', $help_text = '') { ?>
    <div class="form-group">
        <label for="<?php echo $name; ?>" class="form-label <?php if ($required): ?>form-label-required<?php endif; ?>">
            <?php echo $label; ?>
        </label>
        <input
            type="<?php echo $type; ?>"
            id="<?php echo $name; ?>"
            name="<?php echo $name; ?>"
            class="form-input"
            value="<?php echo $value; ?>"
            placeholder="<?php echo $placeholder ?: $label; ?>"
            <?php if ($required): ?>required<?php endif; ?>
        >
        <?php if ($help_text): ?>
            <p class="form-help"><?php echo $help_text; ?></p>
        <?php endif; ?>
    </div>
<?php }} ?>

<!-- =========================================
     FORM SELECT COMPONENT
     ========================================= -->
<?php if (!function_exists('form_select_group')): ?>
<?php function form_select_group($name, $label, $options, $required = false, $selected = '', $help_text = '') { ?>
    <div class="form-group">
        <label for="<?php echo $name; ?>" class="form-label <?php if ($required): ?>form-label-required<?php endif; ?>">
            <?php echo $label; ?>
        </label>
        <select
            id="<?php echo $name; ?>"
            name="<?php echo $name; ?>"
            class="form-select"
            <?php if ($required): ?>required<?php endif; ?>
        >
            <option value="">Select <?php echo $label; ?></option>
            <?php foreach ($options as $value => $text): ?>
                <option value="<?php echo $value; ?>" <?php if ($value == $selected): ?>selected<?php endif; ?>>
                    <?php echo $text; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if ($help_text): ?>
            <p class="form-help"><?php echo $help_text; ?></p>
        <?php endif; ?>
    </div>
<?php }} ?>

<!-- =========================================
     BREADCRUMB COMPONENT
     ========================================= -->
<?php if (!function_exists('render_breadcrumb')): ?>
<?php function render_breadcrumb($items) { ?>
    <div class="breadcrumb">
        <a href="<?php echo base_url(); ?>" class="breadcrumb-item">
            <i class="fas fa-home"></i>
        </a>
        <?php foreach ($items as $index => $item): ?>
            <span class="breadcrumb-separator">/</span>
            <?php if ($index < count($items) - 1): ?>
                <a href="<?php echo isset($item['url']) ? $item['url'] : '#'; ?>" class="breadcrumb-item">
                    <?php echo $item['title']; ?>
                </a>
            <?php else: ?>
                <span class="text-gray-900 font-medium"><?php echo $item['title']; ?></span>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php }} ?>

<!-- =========================================
     LOADING SPINNER COMPONENT
     ========================================= -->
<?php if (!function_exists('loading_spinner')): ?>
<?php function loading_spinner($size = 'md', $text = '') { ?>
    <div class="flex items-center justify-center gap-3">
        <div class="spinner <?php if ($size == 'lg'): ?>h-8 w-8<?php elseif ($size == 'sm'): ?>h-4 w-4<?php endif; ?>"></div>
        <?php if ($text): ?>
            <span class="text-gray-600"><?php echo $text; ?></span>
        <?php endif; ?>
    </div>
<?php }} ?>

<!-- =========================================
     PAGINATION COMPONENT
     ========================================= -->
<?php if (!function_exists('pagination')): ?>
<?php function pagination($current_page, $total_pages, $base_url) { ?>
    <div class="flex items-center justify-between mt-6">
        <div class="text-sm text-gray-600">
            Showing page <?php echo $current_page; ?> of <?php echo $total_pages; ?>
        </div>
        <div class="flex gap-2">
            <?php if ($current_page > 1): ?>
                <a href="<?php echo $base_url . '?page=' . ($current_page - 1); ?>" class="btn btn-outline btn-sm">
                    <i class="fas fa-chevron-left"></i>
                    Previous
                </a>
            <?php endif; ?>

            <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                <a
                    href="<?php echo $base_url . '?page=' . $i; ?>"
                    class="btn btn-sm <?php echo $i == $current_page ? 'btn-primary' : 'btn-outline'; ?>"
                >
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($current_page < $total_pages): ?>
                <a href="<?php echo $base_url . '?page=' . ($current_page + 1); ?>" class="btn btn-outline btn-sm">
                    Next
                    <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php }} ?>

<!-- =========================================
     TABS COMPONENT
     ========================================= -->
<?php if (!function_exists('tabs_start')): ?>
<?php function tabs_start($tabs, $default_active = 0) { ?>
    <div x-data="tabs(<?php echo $default_active; ?>)">
        <div class="tabs">
            <?php foreach ($tabs as $index => $tab): ?>
                <button
                    @click="setTab(<?php echo $index; ?>)"
                    class="tab"
                    :class="{ 'tab-active': activeTab === <?php echo $index; ?> }"
                >
                    <?php echo $tab; ?>
                </button>
            <?php endforeach; ?>
        </div>
        <div class="mt-6">
<?php }} ?>

<?php if (!function_exists('tab_content_start')): ?>
<?php function tab_content_start($index) { ?>
            <div x-show="activeTab === <?php echo $index; ?>" x-transition>
<?php }} ?>

<?php if (!function_exists('tab_content_end')): ?>
<?php function tab_content_end() { ?>
            </div>
<?php }} ?>

<?php if (!function_exists('tabs_end')): ?>
<?php function tabs_end() { ?>
        </div>
    </div>
<?php }} ?>

<!-- =========================================
     STATUS BADGE HELPER
     ========================================= -->
<?php if (!function_exists('status_badge')): ?>
<?php function status_badge($status) {
    $badges = [
        'active' => 'success',
        'inactive' => 'gray',
        'pending' => 'warning',
        'approved' => 'success',
        'rejected' => 'danger',
        'paid' => 'success',
        'unpaid' => 'warning',
        'overdue' => 'danger',
        'draft' => 'gray',
    ];
    $type = isset($badges[strtolower($status)]) ? $badges[strtolower($status)] : 'gray';
    return badge(ucfirst($status), $type);
} ?>
<?php endif; ?>

<!-- =========================================
     CURRENCY FORMAT HELPER
     ========================================= -->
<?php if (!function_exists('format_currency')): ?>
<?php function format_currency($amount, $currency = 'AED') {
    return $currency . ' ' . number_format($amount, 2);
} ?>
<?php endif; ?>

<!-- =========================================
     DATE FORMAT HELPER
     ========================================= -->
<?php if (!function_exists('format_date')): ?>
<?php function format_date($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
} ?>
<?php endif; ?>
