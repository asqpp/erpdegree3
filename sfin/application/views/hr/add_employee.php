<?php $this->load->view('templates/modern_layout'); ?>
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">
        <i class="fas fa-user-plus mr-2"></i><?php echo isset($employee) ? 'Edit' : 'New'; ?> Employee
    </h1>

    <div class="bg-white rounded-lg shadow p-6">
        <?php echo form_open_multipart('hr/save_employee', array('id' => 'employeeForm')); ?>
        <?php if(isset($employee)): ?>
            <input type="hidden" name="employee_id" value="<?php echo $employee->employee_id; ?>">
        <?php endif; ?>

        <!-- Personal Information -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Personal Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                    <input type="text" name="full_name" value="<?php echo isset($employee) ? $employee->full_name : ''; ?>" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" name="email" value="<?php echo isset($employee) ? $employee->email : ''; ?>" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone *</label>
                    <input type="text" name="phone" value="<?php echo isset($employee) ? $employee->phone : ''; ?>" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="<?php echo isset($employee) ? $employee->date_of_birth : ''; ?>" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                    <select name="gender" class="w-full border rounded px-3 py-2">
                        <option value="male" <?php echo (isset($employee) && $employee->gender == 'male') ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo (isset($employee) && $employee->gender == 'female') ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nationality</label>
                    <input type="text" name="nationality" value="<?php echo isset($employee) ? $employee->nationality : ''; ?>" class="w-full border rounded px-3 py-2">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <textarea name="address" rows="2" class="w-full border rounded px-3 py-2"><?php echo isset($employee) ? $employee->address : ''; ?></textarea>
                </div>
            </div>
        </div>

        <!-- Employment Details -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Employment Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Employee Code</label>
                    <input type="text" name="employee_code" value="<?php echo isset($employee) ? $employee->employee_code : ''; ?>" class="w-full border rounded px-3 py-2" placeholder="Auto-generated if empty">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department *</label>
                    <select name="department_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">Select Department</option>
                        <?php foreach($departments as $dept): ?>
                        <option value="<?php echo $dept->department_id; ?>" <?php echo (isset($employee) && $employee->department_id == $dept->department_id) ? 'selected' : ''; ?>>
                            <?php echo $dept->department_name; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Position *</label>
                    <input type="text" name="position" value="<?php echo isset($employee) ? $employee->position : ''; ?>" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Join Date *</label>
                    <input type="date" name="join_date" value="<?php echo isset($employee) ? $employee->join_date : date('Y-m-d'); ?>" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contract Type</label>
                    <select name="contract_type" class="w-full border rounded px-3 py-2">
                        <option value="full_time" <?php echo (isset($employee) && $employee->contract_type == 'full_time') ? 'selected' : ''; ?>>Full Time</option>
                        <option value="part_time" <?php echo (isset($employee) && $employee->contract_type == 'part_time') ? 'selected' : ''; ?>>Part Time</option>
                        <option value="contract" <?php echo (isset($employee) && $employee->contract_type == 'contract') ? 'selected' : ''; ?>>Contract</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full border rounded px-3 py-2">
                        <option value="active" <?php echo (!isset($employee) || $employee->status == 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="on_leave" <?php echo (isset($employee) && $employee->status == 'on_leave') ? 'selected' : ''; ?>>On Leave</option>
                        <option value="inactive" <?php echo (isset($employee) && $employee->status == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Salary Information -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Salary Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Basic Salary (AED) *</label>
                    <input type="number" name="basic_salary" step="0.01" value="<?php echo isset($employee) ? $employee->basic_salary : ''; ?>" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Housing Allowance (AED)</label>
                    <input type="number" name="housing_allowance" step="0.01" value="<?php echo isset($employee) ? $employee->housing_allowance : '0'; ?>" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Transportation Allowance (AED)</label>
                    <input type="number" name="transport_allowance" step="0.01" value="<?php echo isset($employee) ? $employee->transport_allowance : '0'; ?>" class="w-full border rounded px-3 py-2">
                </div>
            </div>
        </div>

        <!-- Documents -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Document Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Emirates ID</label>
                    <input type="text" name="emirates_id" value="<?php echo isset($employee) ? $employee->emirates_id : ''; ?>" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Passport Number</label>
                    <input type="text" name="passport_number" value="<?php echo isset($employee) ? $employee->passport_number : ''; ?>" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Visa Number</label>
                    <input type="text" name="visa_number" value="<?php echo isset($employee) ? $employee->visa_number : ''; ?>" class="w-full border rounded px-3 py-2">
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-4">
            <a href="<?php echo base_url('hr/employees'); ?>" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>Save Employee
            </button>
        </div>

        <?php echo form_close(); ?>
    </div>
</div>
