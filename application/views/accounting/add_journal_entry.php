<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">
        <i class="fas fa-file-alt mr-2"></i>New Journal Entry
    </h1>

    <div class="bg-white rounded-lg shadow p-6">
        <?php echo form_open('accounting/save_journal_entry', array('id' => 'journalEntryForm')); ?>

        <!-- Header Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Entry Date *</label>
                <input type="date" name="entry_date" value="<?php echo date('Y-m-d'); ?>" class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Reference Number</label>
                <input type="text" name="reference_number" class="w-full border rounded px-3 py-2" placeholder="Optional">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                <input type="text" name="description" class="w-full border rounded px-3 py-2" placeholder="Brief description of the transaction" required>
            </div>
        </div>

        <!-- Journal Entry Lines -->
        <div class="border-t pt-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Journal Entry Lines</h3>
                <button type="button" onclick="addLine()" class="bg-blue-600 text-white px-4 py-2 rounded text-sm">
                    <i class="fas fa-plus mr-1"></i>Add Line
                </button>
            </div>

            <div id="lines-container">
                <!-- Line 1 - Debit -->
                <div class="line-row grid grid-cols-12 gap-4 mb-4 p-4 bg-gray-50 rounded">
                    <div class="col-span-5">
                        <label class="block text-xs text-gray-600 mb-1">Account *</label>
                        <select name="lines[0][account_id]" class="w-full border rounded px-2 py-2 text-sm" required>
                            <option value="">Select Account</option>
                            <?php foreach($accounts as $account): ?>
                            <option value="<?php echo $account->account_id; ?>"><?php echo $account->account_code; ?> - <?php echo $account->account_name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-span-3">
                        <label class="block text-xs text-gray-600 mb-1">Description</label>
                        <input type="text" name="lines[0][line_description]" class="w-full border rounded px-2 py-2 text-sm">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs text-gray-600 mb-1">Debit</label>
                        <input type="number" name="lines[0][debit_amount]" step="0.01" class="line-debit w-full border rounded px-2 py-2 text-sm" onchange="calculateTotals()">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs text-gray-600 mb-1">Credit</label>
                        <input type="number" name="lines[0][credit_amount]" step="0.01" class="line-credit w-full border rounded px-2 py-2 text-sm" onchange="calculateTotals()">
                    </div>
                </div>

                <!-- Line 2 - Credit -->
                <div class="line-row grid grid-cols-12 gap-4 mb-4 p-4 bg-gray-50 rounded">
                    <div class="col-span-5">
                        <label class="block text-xs text-gray-600 mb-1">Account *</label>
                        <select name="lines[1][account_id]" class="w-full border rounded px-2 py-2 text-sm" required>
                            <option value="">Select Account</option>
                            <?php foreach($accounts as $account): ?>
                            <option value="<?php echo $account->account_id; ?>"><?php echo $account->account_code; ?> - <?php echo $account->account_name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-span-3">
                        <label class="block text-xs text-gray-600 mb-1">Description</label>
                        <input type="text" name="lines[1][line_description]" class="w-full border rounded px-2 py-2 text-sm">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs text-gray-600 mb-1">Debit</label>
                        <input type="number" name="lines[1][debit_amount]" step="0.01" class="line-debit w-full border rounded px-2 py-2 text-sm" onchange="calculateTotals()">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs text-gray-600 mb-1">Credit</label>
                        <input type="number" name="lines[1][credit_amount]" step="0.01" class="line-credit w-full border rounded px-2 py-2 text-sm" onchange="calculateTotals()">
                    </div>
                </div>
            </div>
        </div>

        <!-- Totals Section -->
        <div class="border-t pt-6 mt-6">
            <div class="bg-blue-50 p-4 rounded">
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="text-sm text-gray-600">Total Debit</div>
                        <div class="text-2xl font-bold text-blue-600" id="total-debit-display">AED 0.00</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">Total Credit</div>
                        <div class="text-2xl font-bold text-green-600" id="total-credit-display">AED 0.00</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">Difference</div>
                        <div class="text-2xl font-bold" id="difference-display">AED 0.00</div>
                    </div>
                </div>
                <div id="balance-warning" class="mt-4 text-center text-red-600 font-semibold hidden">
                    ⚠️ Entry is out of balance! Debit and Credit totals must be equal.
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
            <textarea name="notes" rows="3" class="w-full border rounded px-3 py-2" placeholder="Additional notes (optional)"></textarea>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex justify-end gap-4">
            <a href="<?php echo base_url('accounting/journal_entries'); ?>" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" name="action" value="draft" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <i class="fas fa-save mr-2"></i>Save as Draft
            </button>
            <button type="submit" name="action" value="post" id="post-btn" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" disabled>
                <i class="fas fa-check mr-2"></i>Post Entry
            </button>
        </div>

        <?php echo form_close(); ?>
    </div>
</div>

<script>
let lineCount = 2;

function addLine() {
    const container = document.getElementById('lines-container');
    const newLine = document.querySelector('.line-row').cloneNode(true);

    // Update name attributes
    newLine.querySelectorAll('input, select').forEach(input => {
        const name = input.getAttribute('name');
        if (name) {
            input.setAttribute('name', name.replace('[0]', `[${lineCount}]`).replace('[1]', `[${lineCount}]`));
            input.value = '';
        }
    });

    container.appendChild(newLine);
    lineCount++;
    calculateTotals();
}

function calculateTotals() {
    let totalDebit = 0;
    let totalCredit = 0;

    document.querySelectorAll('.line-debit').forEach(input => {
        totalDebit += parseFloat(input.value) || 0;
    });

    document.querySelectorAll('.line-credit').forEach(input => {
        totalCredit += parseFloat(input.value) || 0;
    });

    const difference = Math.abs(totalDebit - totalCredit);

    document.getElementById('total-debit-display').textContent = 'AED ' + totalDebit.toFixed(2);
    document.getElementById('total-credit-display').textContent = 'AED ' + totalCredit.toFixed(2);
    document.getElementById('difference-display').textContent = 'AED ' + difference.toFixed(2);

    const warning = document.getElementById('balance-warning');
    const postBtn = document.getElementById('post-btn');
    const differenceDisplay = document.getElementById('difference-display');

    if (difference < 0.01 && totalDebit > 0 && totalCredit > 0) {
        warning.classList.add('hidden');
        postBtn.disabled = false;
        differenceDisplay.classList.remove('text-red-600');
        differenceDisplay.classList.add('text-green-600');
    } else {
        warning.classList.remove('hidden');
        postBtn.disabled = true;
        differenceDisplay.classList.remove('text-green-600');
        differenceDisplay.classList.add('text-red-600');
    }
}

// Initialize totals
calculateTotals();
</script>
