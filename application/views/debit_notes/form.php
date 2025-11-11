<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">
        <i class="fas fa-file-invoice mr-2"></i>New Debit Note
    </h1>

    <div class="bg-white rounded-lg shadow p-6">
        <?php echo form_open('debit_notes/add', array('id' => 'debitNoteForm')); ?>

        <!-- Header Information -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                <input type="date" name="debit_note_date" value="<?php echo date('Y-m-d'); ?>" class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                <select name="supplier_id" id="supplier_id" class="w-full border rounded px-3 py-2" required>
                    <option value="">Select Supplier</option>
                    <?php foreach($suppliers as $supplier): ?>
                    <option value="<?php echo $supplier->supplier_id; ?>"><?php echo $supplier->supplier_name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Reference</label>
                <input type="text" name="reference_number" class="w-full border rounded px-3 py-2" placeholder="Optional">
            </div>
        </div>

        <!-- Items Section -->
        <div class="border-t pt-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Items</h3>
                <button type="button" onclick="addItem()" class="bg-purple-600 text-white px-4 py-2 rounded text-sm">
                    <i class="fas fa-plus mr-1"></i>Add Item
                </button>
            </div>

            <div id="items-container">
                <div class="item-row grid grid-cols-12 gap-4 mb-4">
                    <div class="col-span-5">
                        <label class="block text-xs text-gray-600 mb-1">Account</label>
                        <select name="items[0][account_id]" class="w-full border rounded px-2 py-2 text-sm" required>
                            <option value="">Select Account</option>
                            <?php foreach($accounts as $account): ?>
                            <option value="<?php echo $account->account_id; ?>"><?php echo $account->account_name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-span-4">
                        <label class="block text-xs text-gray-600 mb-1">Description</label>
                        <input type="text" name="items[0][description]" class="w-full border rounded px-2 py-2 text-sm">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs text-gray-600 mb-1">Amount</label>
                        <input type="number" name="items[0][amount]" step="0.01" class="item-amount w-full border rounded px-2 py-2 text-sm" onchange="calculateTotal()" required>
                    </div>
                    <div class="col-span-1 flex items-end">
                        <button type="button" onclick="removeItem(this)" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Totals Section -->
        <div class="border-t pt-6 mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Narration</label>
                    <textarea name="narration" rows="4" class="w-full border rounded px-3 py-2" placeholder="Optional notes"></textarea>
                </div>

                <div class="bg-gray-50 p-4 rounded">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-semibold" id="subtotal-display">AED 0.00</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">VAT (5%):</span>
                        <span class="font-semibold" id="vat-display">AED 0.00</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t pt-2">
                        <span>Total:</span>
                        <span class="text-purple-600" id="total-display">AED 0.00</span>
                    </div>

                    <input type="hidden" name="subtotal" id="subtotal" value="0">
                    <input type="hidden" name="vat_amount" id="vat_amount" value="0">
                    <input type="hidden" name="total_amount" id="total_amount" value="0">
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex justify-end gap-4">
            <a href="<?php echo base_url('debit_notes'); ?>" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                <i class="fas fa-save mr-2"></i>Save Debit Note
            </button>
        </div>

        <?php echo form_close(); ?>
    </div>
</div>

<script>
let itemCount = 1;

function addItem() {
    const container = document.getElementById('items-container');
    const newItem = document.querySelector('.item-row').cloneNode(true);

    // Update name attributes
    newItem.querySelectorAll('input, select').forEach(input => {
        const name = input.getAttribute('name');
        if (name) {
            input.setAttribute('name', name.replace('[0]', `[${itemCount}]`));
            input.value = '';
        }
    });

    container.appendChild(newItem);
    itemCount++;
}

function removeItem(button) {
    const container = document.getElementById('items-container');
    if (container.children.length > 1) {
        button.closest('.item-row').remove();
        calculateTotal();
    }
}

function calculateTotal() {
    let subtotal = 0;
    document.querySelectorAll('.item-amount').forEach(input => {
        subtotal += parseFloat(input.value) || 0;
    });

    const vat = subtotal * 0.05;
    const total = subtotal + vat;

    document.getElementById('subtotal').value = subtotal.toFixed(2);
    document.getElementById('vat_amount').value = vat.toFixed(2);
    document.getElementById('total_amount').value = total.toFixed(2);

    document.getElementById('subtotal-display').textContent = 'AED ' + subtotal.toFixed(2);
    document.getElementById('vat-display').textContent = 'AED ' + vat.toFixed(2);
    document.getElementById('total-display').textContent = 'AED ' + total.toFixed(2);
}
</script>
