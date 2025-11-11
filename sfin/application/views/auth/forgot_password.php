<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Forgot Password'; ?></title>
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
            <p class="text-blue-100 mt-2">Reset Your Password</p>
        </div>

        <!-- Forgot Password Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <div class="text-center mb-6">
                <div class="bg-blue-100 rounded-full w-16 h-16 mx-auto flex items-center justify-center mb-4">
                    <i class="fas fa-key text-3xl text-blue-600"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Forgot Password?</h2>
                <p class="text-gray-600 mt-2 text-sm">
                    No worries! Enter your email and we'll send you a reset link.
                </p>
            </div>

            <!-- Flash Messages -->
            <?php if($this->session->flashdata('success')): ?>
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                    <div class="flex items-start">
                        <i class="fas fa-check-circle mr-3 mt-0.5"></i>
                        <p class="text-sm"><?php echo $this->session->flashdata('success'); ?></p>
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

            <!-- Forgot Password Form -->
            <?php echo form_open('auth/forgot_password', array('class' => 'space-y-6')); ?>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2 text-gray-400"></i>Email Address
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="<?php echo set_value('email'); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                           placeholder="you@example.com"
                           required
                           autofocus>
                    <p class="text-xs text-gray-500 mt-1">Enter the email address associated with your account</p>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition transform hover:scale-[1.02] shadow-lg">
                    <i class="fas fa-paper-plane mr-2"></i>Send Reset Link
                </button>

            <?php echo form_close(); ?>

            <!-- Back to Login Link -->
            <div class="mt-6 text-center">
                <a href="<?php echo base_url('auth/login'); ?>"
                   class="text-sm text-gray-600 hover:text-gray-800 transition inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Login
                </a>
            </div>

            <!-- Help Text -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 mr-3 mt-0.5"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Need help?</p>
                        <p>If you don't receive an email within a few minutes, please check your spam folder or contact support.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-white text-sm">
            <p>&copy; <?php echo date('Y'); ?> Insurance ERP. All rights reserved.</p>
        </div>
    </div>

    <script>
        // Auto-hide success messages after 10 seconds
        setTimeout(function() {
            const successAlerts = document.querySelectorAll('.border-green-500');
            successAlerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 10000);
    </script>
</body>
</html>
