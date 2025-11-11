<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">
        <i class="fas fa-money-bill-wave mr-2"></i>New Payment Voucher
    </h1>

    <div class="bg-white rounded-lg shadow p-6">
        <?php echo form_open('receipts/add_payment', array('id' => 'paymentForm')); ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Voucher Date</label>
                <input type="date" name="voucher_date" value="<?php echo date('Y-m-d'); ?>" class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Party Name</label>
                <input type="text" name="party_name" class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                <select name="payment_method" class="w-full border rounded px-3 py-2" required>
                    <option value="cash">Cash</option>
                    <option value="cheque">Cheque</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="card">Card</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bank Account</label>
                <select name="bank_account_id" class="w-full border rounded px-3 py-2" required>
                    <?php foreach($bank_accounts as $account): ?>
                    <option value="<?php echo $account->account_id; ?>"><?php echo $account->account_name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Total Amount</label>
                <input type="number" name="total_amount" step="0.01" class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Narration</label>
                <textarea name="narration" rows="3" class="w-full border rounded px-3 py-2"></textarea>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-4">
            <a href="<?php echo base_url('receipts/payments'); ?>" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                <i class="fas fa-save mr-2"></i>Save Payment
            </button>
        </div>

        <?php echo form_close(); ?>
    </div>
</div>
