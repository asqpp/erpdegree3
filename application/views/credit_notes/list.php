<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-file-invoice-dollar mr-2"></i>Credit Notes
        </h1>
        <a href="<?php echo base_url('credit_notes/add'); ?>" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
            <i class="fas fa-plus mr-2"></i>New Credit Note
        </a>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Total Credit Notes</div>
            <div class="text-3xl font-bold text-indigo-600"><?php echo number_format($statistics['total_credit_notes']); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Total Amount</div>
            <div class="text-3xl font-bold text-green-600">AED <?php echo number_format($statistics['total_amount'], 2); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Draft</div>
            <div class="text-3xl font-bold text-yellow-600"><?php echo number_format($statistics['draft_notes']); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Posted</div>
            <div class="text-3xl font-bold text-green-600"><?php echo number_format($statistics['posted_notes']); ?></div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form method="get" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <input type="text" name="search" placeholder="Search..." class="border rounded px-3 py-2" value="<?php echo $filters['search']; ?>">
            <select name="status" class="border rounded px-3 py-2">
                <option value="">All Status</option>
                <option value="draft" <?php echo ($filters['status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
                <option value="posted" <?php echo ($filters['status'] == 'posted') ? 'selected' : ''; ?>>Posted</option>
            </select>
            <input type="date" name="date_from" class="border rounded px-3 py-2" value="<?php echo $filters['date_from']; ?>" placeholder="Date From">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Filter</button>
            <a href="<?php echo base_url('credit_notes/export'); ?>" class="bg-green-600 text-white px-4 py-2 rounded text-center">Export CSV</a>
        </form>
    </div>

    <!-- Credit Notes Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Note #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">VAT</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach($credit_notes as $note): ?>
                <tr>
                    <td class="px-6 py-4 font-medium"><?php echo $note->credit_note_number; ?></td>
                    <td class="px-6 py-4"><?php echo date('d/m/Y', strtotime($note->credit_note_date)); ?></td>
                    <td class="px-6 py-4"><?php echo $note->customer_name; ?></td>
                    <td class="px-6 py-4">AED <?php echo number_format($note->subtotal, 2); ?></td>
                    <td class="px-6 py-4">AED <?php echo number_format($note->vat_amount, 2); ?></td>
                    <td class="px-6 py-4 font-semibold">AED <?php echo number_format($note->total_amount, 2); ?></td>
                    <td class="px-6 py-4">
                        <?php if($note->status == 'draft'): ?>
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">Draft</span>
                        <?php else: ?>
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Posted</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <a href="<?php echo base_url('credit_notes/view/'.$note->credit_note_id); ?>" class="text-blue-600 hover:text-blue-800 mr-3">View</a>
                        <?php if($note->status == 'draft'): ?>
                            <a href="<?php echo base_url('credit_notes/post/'.$note->credit_note_id); ?>"
                               class="text-green-600 hover:text-green-800 mr-3"
                               onclick="return confirm('Post this credit note to accounts?')">Post</a>
                        <?php endif; ?>
                        <a href="<?php echo base_url('credit_notes/print_note/'.$note->credit_note_id); ?>" class="text-indigo-600 hover:text-indigo-800">Print</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
