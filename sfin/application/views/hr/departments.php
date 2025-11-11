<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-building mr-2"></i>Departments
        </h1>
        <button onclick="openAddModal()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>New Department
        </button>
    </div>

    <!-- Departments Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php if(isset($departments) && count($departments) > 0): ?>
            <?php foreach($departments as $dept): ?>
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800"><?php echo $dept->department_name; ?></h3>
                        <p class="text-sm text-gray-500 mt-1">Code: <?php echo $dept->department_code; ?></p>
                    </div>
                    <span class="px-3 py-1 <?php echo $dept->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?> rounded text-xs font-semibold">
                        <?php echo $dept->is_active ? 'Active' : 'Inactive'; ?>
                    </span>
                </div>

                <?php if($dept->description): ?>
                <p class="text-gray-600 text-sm mb-4"><?php echo $dept->description; ?></p>
                <?php endif; ?>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="bg-blue-50 p-3 rounded text-center">
                        <div class="text-2xl font-bold text-blue-600"><?php echo $dept->employee_count ?? 0; ?></div>
                        <div class="text-xs text-gray-600">Employees</div>
                    </div>
                    <div class="bg-green-50 p-3 rounded text-center">
                        <div class="text-2xl font-bold text-green-600"><?php echo $dept->active_employee_count ?? 0; ?></div>
                        <div class="text-xs text-gray-600">Active</div>
                    </div>
                </div>

                <?php if($dept->department_head): ?>
                <div class="border-t pt-3">
                    <p class="text-xs text-gray-500">Department Head</p>
                    <p class="font-semibold text-sm"><?php echo $dept->head_name; ?></p>
                </div>
                <?php endif; ?>

                <div class="mt-4 flex gap-2">
                    <button onclick="viewDepartment(<?php echo $dept->department_id; ?>)" class="flex-1 bg-blue-50 text-blue-600 px-3 py-2 rounded hover:bg-blue-100 text-sm">
                        <i class="fas fa-eye mr-1"></i>View
                    </button>
                    <button onclick="editDepartment(<?php echo $dept->department_id; ?>)" class="flex-1 bg-green-50 text-green-600 px-3 py-2 rounded hover:bg-green-100 text-sm">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-3 bg-white rounded-lg shadow p-12 text-center">
                <i class="fas fa-building text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">No departments found. Create your first department to get started.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add/Edit Department Modal -->
<div id="departmentModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-2xl">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold" id="modalTitle">New Department</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <?php echo form_open('hr/save_department', array('id' => 'departmentForm')); ?>
            <input type="hidden" name="department_id" id="department_id">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department Name *</label>
                    <input type="text" name="department_name" id="department_name" class="w-full border rounded px-3 py-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department Code</label>
                    <input type="text" name="department_code" id="department_code" class="w-full border rounded px-3 py-2" placeholder="Auto-generated if empty">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="3" class="w-full border rounded px-3 py-2"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department Head</label>
                    <select name="department_head" id="department_head" class="w-full border rounded px-3 py-2">
                        <option value="">No Head Assigned</option>
                        <?php if(isset($employees)): foreach($employees as $emp): ?>
                        <option value="<?php echo $emp->employee_id; ?>"><?php echo $emp->full_name; ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="is_active" id="is_active" class="w-full border rounded px-3 py-2">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-4">
                <button type="button" onclick="closeModal()" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Save Department
                </button>
            </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('departmentModal').classList.remove('hidden');
    document.getElementById('modalTitle').textContent = 'New Department';
    document.getElementById('departmentForm').reset();
    document.getElementById('department_id').value = '';
}

function closeModal() {
    document.getElementById('departmentModal').classList.add('hidden');
}

function editDepartment(id) {
    // Fetch department data via AJAX and populate form
    fetch('<?php echo base_url('hr/get_department/'); ?>' + id)
        .then(response => response.json())
        .then(data => {
            document.getElementById('departmentModal').classList.remove('hidden');
            document.getElementById('modalTitle').textContent = 'Edit Department';
            document.getElementById('department_id').value = data.department_id;
            document.getElementById('department_name').value = data.department_name;
            document.getElementById('department_code').value = data.department_code;
            document.getElementById('description').value = data.description || '';
            document.getElementById('department_head').value = data.department_head || '';
            document.getElementById('is_active').value = data.is_active;
        });
}

function viewDepartment(id) {
    window.location.href = '<?php echo base_url('hr/employees?department_id='); ?>' + id;
}

// Close modal on outside click
document.getElementById('departmentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
