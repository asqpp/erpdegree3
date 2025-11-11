<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold">Debit Note: <?php echo $debit_note->debit_note_number; ?></h1>
                <?php if($debit_note->status == 'draft'): ?>
                    <span class="inline-block mt-2 px-3 py-1 bg-yellow-100 text-yellow-800 rounded text-sm">Draft</span>
                <?php else: ?>
                    <span class="inline-block mt-2 px-3 py-1 bg-green-100 text-green-800 rounded text-sm">Posted</span>
                <?php endif; ?>
            </div>
            <div>
                <?php if($debit_note->status == 'draft'): ?>
                    <a href="<?php echo base_url('debit_notes/post/'.$debit_note->debit_note_id); ?>"
                       class="bg-green-600 text-white px-4 py-2 rounded mr-2"
                       onclick="return confirm('Post this debit note to accounts? This action cannot be undone.')">
                        <i class="fas fa-check mr-2"></i>Post
                    </a>
                <?php endif; ?>
                <a href="<?php echo base_url('debit_notes/print_note/'.$debit_note->debit_note_id); ?>" class="bg-purple-600 text-white px-4 py-2 rounded mr-2">
                    <i class="fas fa-print mr-2"></i>Print
                </a>
                <a href="<?php echo base_url('debit_notes'); ?>" class="bg-gray-600 text-white px-4 py-2 rounded">Back</a>
            </div>
        </div>

        <!-- Header Information -->
        <div class="grid grid-cols-2 gap-6 mb-6 pb-6 border-b">
            <div>
                <p class="text-sm text-gray-500">Date</p>
                <p class="font-semibold"><?php echo date('d/m/Y', strtotime($debit_note->debit_note_date)); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Supplier</p>
                <p class="font-semibold"><?php echo $debit_note->supplier_name; ?></p>
            </div>
            <?php if($debit_note->reference_number): ?>
            <div>
                <p class="text-sm text-gray-500">Reference</p>
                <p class="font-semibold"><?php echo $debit_note->reference_number; ?></p>
            </div>
            <?php endif; ?>
            <?php if($debit_note->journal_entry_id): ?>
            <div>
                <p class="text-sm text-gray-500">Journal Entry</p>
                <p class="font-semibold">
                    <a href="<?php echo base_url('accounting/journal_entry_view/'.$debit_note->journal_entry_id); ?>" class="text-blue-600 hover:underline">
                        JE-<?php echo str_pad($debit_note->journal_entry_id, 6, '0', STR_PAD_LEFT); ?>
                    </a>
                </p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Items Table -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-4">Items</h3>
            <table class="min-w-full border">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    <?php $i = 1; foreach($debit_note_items as $item): ?>
                    <tr>
                        <td class="px-4 py-3"><?php echo $i++; ?></td>
                        <td class="px-4 py-3"><?php echo $item->account_name; ?></td>
                        <td class="px-4 py-3"><?php echo $item->description ?: '-'; ?></td>
                        <td class="px-4 py-3 text-right">AED <?php echo number_format($item->amount, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="flex justify-end">
            <div class="w-80 bg-gray-50 p-4 rounded">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-semibold">AED <?php echo number_format($debit_note->subtotal, 2); ?></span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600">VAT (<?php echo $debit_note->vat_percentage; ?>%):</span>
                    <span class="font-semibold">AED <?php echo number_format($debit_note->vat_amount, 2); ?></span>
                </div>
                <div class="flex justify-between text-lg font-bold border-t pt-2">
                    <span>Total:</span>
                    <span class="text-purple-600">AED <?php echo number_format($debit_note->total_amount, 2); ?></span>
                </div>
            </div>
        </div>

        <!-- Narration -->
        <?php if($debit_note->narration): ?>
        <div class="mt-6 pt-6 border-t">
            <p class="text-sm text-gray-500 mb-2">Narration</p>
            <p class="text-gray-700"><?php echo nl2br($debit_note->narration); ?></p>
        </div>
        <?php endif; ?>

        <!-- Footer Info -->
        <div class="mt-6 pt-6 border-t text-sm text-gray-500">
            <p>Created by: <?php echo $debit_note->created_by_name; ?> on <?php echo date('d/m/Y H:i', strtotime($debit_note->created_at)); ?></p>
            <?php if($debit_note->posted_at): ?>
                <p>Posted on: <?php echo date('d/m/Y H:i', strtotime($debit_note->posted_at)); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>
