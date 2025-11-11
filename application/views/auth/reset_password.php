<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Reset Password'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-600 via-purple-600 to-pink-500 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <!-- Logo / Brand -->
        <div class="text-center mb-8">
            <div class="bg-white rounded-full w-20 h-20 mx-auto flex items-center justify-center shadow-lg mb-4">
                <i class="fas fa-shield-alt text-4xl text-blue-600"></i>
            </div>
            <h1 class="text-white text-3xl font-bold">Insurance ERP</h1>
            <p class="text-blue-100 mt-2">Create New Password</p>
        </div>

        <!-- Reset Password Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <div class="text-center mb-6">
                <div class="bg-green-100 rounded-full w-16 h-16 mx-auto flex items-center justify-center mb-4">
                    <i class="fas fa-lock-open text-3xl text-green-600"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Reset Your Password</h2>
                <p class="text-gray-600 mt-2 text-sm">
                    <?php if(isset($user)): ?>
                        Hi <strong><?php echo $user->full_name; ?></strong>, create a new password for your account.
                    <?php else: ?>
                        Enter your new password below.
                    <?php endif; ?>
                </p>
            </div>

            <!-- Flash Messages -->
            <?php if($this->session->flashdata('success')): ?>
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-3"></i>
                        <p><?php echo $this->session->flashdata('success'); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($this->session->flashdata('error')): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-3"></i>
                        <p><?php echo $this->session->flashdata('error'); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Validation Errors -->
            <?php if(validation_errors()): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                    <?php echo validation_errors('<p class="mb-1"><i class="fas fa-times-circle mr-2"></i>', '</p>'); ?>
                </div>
            <?php endif; ?>

            <!-- Reset Password Form -->
            <?php echo form_open('auth/reset_password/' . (isset($token) ? $token : ''), array('class' => 'space-y-6')); ?>

                <!-- New Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-gray-400"></i>New Password
                    </label>
                    <div class="relative">
                        <input type="password"
                               id="password"
                               name="password"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition pr-12"
                               placeholder="••••••••"
                               required
                               autofocus>
                        <button type="button"
                                onclick="togglePassword('password')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye" id="toggleIcon1"></i>
                        </button>
                    </div>
                    <!-- Password Strength Indicator -->
                    <div id="password-strength" class="mt-2"></div>
                    <p class="text-xs text-gray-500 mt-1">Minimum 6 characters. Use a strong password.</p>
                </div>

                <!-- Confirm Password Field -->
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-gray-400"></i>Confirm New Password
                    </label>
                    <div class="relative">
                        <input type="password"
                               id="confirm_password"
                               name="confirm_password"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition pr-12"
                               placeholder="••••••••"
                               required>
                        <button type="button"
                                onclick="togglePassword('confirm_password')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye" id="toggleIcon2"></i>
                        </button>
                    </div>
                    <div id="password-match" class="mt-1 text-xs"></div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        id="submitBtn"
                        class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition transform hover:scale-[1.02] shadow-lg">
                    <i class="fas fa-check-circle mr-2"></i>Reset Password
                </button>

            <?php echo form_close(); ?>

            <!-- Password Requirements -->
            <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                <p class="text-sm font-semibold text-gray-700 mb-2">Password Requirements:</p>
                <ul class="text-xs text-gray-600 space-y-1">
                    <li id="req-length" class="flex items-center">
                        <i class="fas fa-circle text-gray-400 mr-2 text-xs"></i>
                        At least 6 characters long
                    </li>
                    <li id="req-upper" class="flex items-center">
                        <i class="fas fa-circle text-gray-400 mr-2 text-xs"></i>
                        Contains uppercase and lowercase letters (recommended)
                    </li>
                    <li id="req-number" class="flex items-center">
                        <i class="fas fa-circle text-gray-400 mr-2 text-xs"></i>
                        Contains at least one number (recommended)
                    </li>
                    <li id="req-special" class="flex items-center">
                        <i class="fas fa-circle text-gray-400 mr-2 text-xs"></i>
                        Contains special character (recommended)
                    </li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-white text-sm">
            <p>&copy; <?php echo date('Y'); ?> Insurance ERP. All rights reserved.</p>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const toggleIcon = document.getElementById(fieldId === 'password' ? 'toggleIcon1' : 'toggleIcon2');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Password strength indicator
        document.getElementById('password').addEventListener('input', function(e) {
            const password = e.target.value;
            const strengthDiv = document.getElementById('password-strength');

            let strength = 0;
            let strengthText = '';
            let strengthColor = '';

            if (password.length >= 6) {
                strength++;
                updateRequirement('req-length', true);
            } else {
                updateRequirement('req-length', false);
            }

            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) {
                strength++;
                updateRequirement('req-upper', true);
            } else {
                updateRequirement('req-upper', false);
            }

            if (/[0-9]/.test(password)) {
                strength++;
                updateRequirement('req-number', true);
            } else {
                updateRequirement('req-number', false);
            }

            if (/[^a-zA-Z0-9]/.test(password)) {
                strength++;
                updateRequirement('req-special', true);
            } else {
                updateRequirement('req-special', false);
            }

            if (password.length === 0) {
                strengthDiv.innerHTML = '';
            } else if (strength <= 1) {
                strengthText = 'Weak';
                strengthColor = 'bg-red-500';
            } else if (strength === 2) {
                strengthText = 'Fair';
                strengthColor = 'bg-yellow-500';
            } else if (strength === 3) {
                strengthText = 'Good';
                strengthColor = 'bg-blue-500';
            } else {
                strengthText = 'Strong';
                strengthColor = 'bg-green-500';
            }

            if (password.length > 0) {
                strengthDiv.innerHTML = `
                    <div class="flex items-center gap-2">
                        <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="${strengthColor} h-full rounded-full transition-all duration-300" style="width: ${(strength / 4) * 100}%"></div>
                        </div>
                        <span class="text-xs font-medium">${strengthText}</span>
                    </div>
                `;
            }
        });

        // Password match indicator
        document.getElementById('confirm_password').addEventListener('input', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = e.target.value;
            const matchDiv = document.getElementById('password-match');

            if (confirmPassword.length === 0) {
                matchDiv.innerHTML = '';
            } else if (password === confirmPassword) {
                matchDiv.innerHTML = '<span class="text-green-600"><i class="fas fa-check mr-1"></i>Passwords match</span>';
            } else {
                matchDiv.innerHTML = '<span class="text-red-600"><i class="fas fa-times mr-1"></i>Passwords do not match</span>';
            }
        });

        function updateRequirement(id, met) {
            const elem = document.getElementById(id);
            const icon = elem.querySelector('i');

            if (met) {
                icon.classList.remove('fa-circle', 'text-gray-400');
                icon.classList.add('fa-check-circle', 'text-green-600');
                elem.classList.add('text-green-700');
            } else {
                icon.classList.remove('fa-check-circle', 'text-green-600');
                icon.classList.add('fa-circle', 'text-gray-400');
                elem.classList.remove('text-green-700');
            }
        }
    </script>
</body>
</html>
