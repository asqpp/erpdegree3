<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-file-invoice mr-2"></i>Quotations
        </h1>
        <a href="<?php echo base_url('sales/quotation_form'); ?>" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>New Quotation
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form method="get" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <input type="text" name="search" placeholder="Search customer..." class="border rounded px-3 py-2" value="<?php echo $filters['search'] ?? ''; ?>">

            <select name="status" class="border rounded px-3 py-2">
                <option value="">All Status</option>
                <option value="draft" <?php echo (isset($filters['status']) && $filters['status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
                <option value="sent" <?php echo (isset($filters['status']) && $filters['status'] == 'sent') ? 'selected' : ''; ?>>Sent</option>
                <option value="accepted" <?php echo (isset($filters['status']) && $filters['status'] == 'accepted') ? 'selected' : ''; ?>>Accepted</option>
                <option value="rejected" <?php echo (isset($filters['status']) && $filters['status'] == 'rejected') ? 'selected' : ''; ?>>Rejected</option>
            </select>

            <select name="insurance_type" class="border rounded px-3 py-2">
                <option value="">All Types</option>
                <option value="motor">Motor</option>
                <option value="health">Health</option>
                <option value="life">Life</option>
                <option value="property">Property</option>
                <option value="travel">Travel</option>
            </select>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
            <a href="<?php echo base_url('sales/export_quotations'); ?>" class="bg-green-600 text-white px-4 py-2 rounded text-center">Export CSV</a>
        </form>
    </div>

    <!-- Quotations Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quote #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Premium</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valid Until</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if(isset($quotations) && count($quotations) > 0): ?>
                    <?php foreach($quotations as $quote): ?>
                    <tr>
                        <td class="px-6 py-4 font-medium"><?php echo $quote->quotation_number; ?></td>
                        <td class="px-6 py-4"><?php echo date('d/m/Y', strtotime($quote->quotation_date)); ?></td>
                        <td class="px-6 py-4"><?php echo $quote->customer_name; ?></td>
                        <td class="px-6 py-4"><?php echo ucfirst($quote->insurance_type); ?></td>
                        <td class="px-6 py-4 font-semibold">AED <?php echo number_format($quote->premium_amount, 2); ?></td>
                        <td class="px-6 py-4"><?php echo date('d/m/Y', strtotime($quote->valid_until)); ?></td>
                        <td class="px-6 py-4">
                            <?php
                            $statusColors = [
                                'draft' => 'bg-gray-100 text-gray-800',
                                'sent' => 'bg-blue-100 text-blue-800',
                                'accepted' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-800'
                            ];
                            $colorClass = $statusColors[$quote->status] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="px-2 py-1 <?php echo $colorClass; ?> rounded text-xs"><?php echo ucfirst($quote->status); ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="<?php echo base_url('sales/quotation_view/'.$quote->quotation_id); ?>" class="text-blue-600 hover:text-blue-800 mr-3">View</a>
                            <?php if($quote->status == 'draft'): ?>
                                <a href="<?php echo base_url('sales/quotation_form/'.$quote->quotation_id); ?>" class="text-green-600 hover:text-green-800">Edit</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">No quotations found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
