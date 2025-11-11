<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-file-alt mr-2"></i>Journal Entries
        </h1>
        <a href="<?php echo base_url('accounting/add_journal_entry'); ?>" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>New Journal Entry
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Total Entries</div>
            <div class="text-3xl font-bold text-blue-600"><?php echo number_format($statistics['total_entries'] ?? 0); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">This Month</div>
            <div class="text-3xl font-bold text-green-600"><?php echo number_format($statistics['month_entries'] ?? 0); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Posted</div>
            <div class="text-3xl font-bold text-purple-600"><?php echo number_format($statistics['posted_entries'] ?? 0); ?></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-500 text-sm">Draft</div>
            <div class="text-3xl font-bold text-yellow-600"><?php echo number_format($statistics['draft_entries'] ?? 0); ?></div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form method="get" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <input type="text" name="search" placeholder="Search..." class="border rounded px-3 py-2" value="<?php echo $filters['search'] ?? ''; ?>">
            <input type="date" name="date_from" class="border rounded px-3 py-2" value="<?php echo $filters['date_from'] ?? ''; ?>">
            <input type="date" name="date_to" class="border rounded px-3 py-2" value="<?php echo $filters['date_to'] ?? ''; ?>">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
            <a href="<?php echo base_url('accounting/export_journal'); ?>" class="bg-green-600 text-white px-4 py-2 rounded text-center">Export CSV</a>
        </form>
    </div>

    <!-- Journal Entries Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entry #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Credit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if(isset($journal_entries) && count($journal_entries) > 0): ?>
                    <?php foreach($journal_entries as $entry): ?>
                    <tr>
                        <td class="px-6 py-4 font-medium">JE-<?php echo str_pad($entry->journal_entry_id, 6, '0', STR_PAD_LEFT); ?></td>
                        <td class="px-6 py-4"><?php echo date('d/m/Y', strtotime($entry->entry_date)); ?></td>
                        <td class="px-6 py-4"><?php echo $entry->description; ?></td>
                        <td class="px-6 py-4 text-right font-semibold text-blue-600">AED <?php echo number_format($entry->total_debit, 2); ?></td>
                        <td class="px-6 py-4 text-right font-semibold text-green-600">AED <?php echo number_format($entry->total_credit, 2); ?></td>
                        <td class="px-6 py-4">
                            <?php if($entry->is_posted): ?>
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Posted</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">Draft</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <a href="<?php echo base_url('accounting/view_journal_entry/'.$entry->journal_entry_id); ?>" class="text-blue-600 hover:text-blue-800 mr-3">View</a>
                            <?php if(!$entry->is_posted): ?>
                                <a href="<?php echo base_url('accounting/post_journal_entry/'.$entry->journal_entry_id); ?>"
                                   class="text-green-600 hover:text-green-800"
                                   onclick="return confirm('Post this journal entry? This action cannot be undone.')">Post</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">No journal entries found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
