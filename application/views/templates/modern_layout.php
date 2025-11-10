<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Insurance ERP</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo base_url('assets/img/favicon.png'); ?>">

    <!-- Tailwind CSS Output -->
    <link href="<?php echo base_url('assets/css/output.css'); ?>" rel="stylesheet">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- AOS (Animate On Scroll) -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <!-- Toastify CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    <!-- Custom CSS -->
    <?php if (isset($custom_css)): ?>
        <?php foreach ($custom_css as $css): ?>
            <link href="<?php echo base_url($css); ?>" rel="stylesheet">
        <?php endforeach; ?>
    <?php endif; ?>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50" x-data="{ sidebarOpen: true }" x-cloak>

    <!-- Sidebar -->
    <aside
        x-show="sidebarOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed top-0 left-0 h-screen w-64 bg-white shadow-lg z-40"
    >
        <!-- Logo -->
        <div class="h-16 flex items-center px-6 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shield-alt text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="font-bold text-gray-900 text-lg">Insurance</h1>
                    <p class="text-xs text-gray-500">ERP System</p>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="sidebar-menu overflow-y-auto h-[calc(100vh-4rem)]">
            <!-- Dashboard -->
            <a href="<?php echo base_url('dashboard'); ?>" class="sidebar-item <?php echo $this->uri->segment(1) == 'dashboard' ? 'sidebar-item-active' : ''; ?>">
                <i class="fas fa-home w-5"></i>
                <span>Dashboard</span>
            </a>

            <!-- Customer Management -->
            <div x-data="{ open: false }">
                <button @click="open = !open" class="sidebar-item w-full">
                    <i class="fas fa-users w-5"></i>
                    <span class="flex-1 text-left">Customers</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-collapse class="ml-8 space-y-1 mt-1">
                    <a href="<?php echo base_url('customers'); ?>" class="sidebar-item text-sm">
                        <i class="fas fa-list w-4"></i>
                        <span>All Customers</span>
                    </a>
                    <a href="<?php echo base_url('customers/add'); ?>" class="sidebar-item text-sm">
                        <i class="fas fa-plus w-4"></i>
                        <span>Add Customer</span>
                    </a>
                    <a href="<?php echo base_url('customers/groups'); ?>" class="sidebar-item text-sm">
                        <i class="fas fa-layer-group w-4"></i>
                        <span>Customer Groups</span>
                    </a>
                </div>
            </div>

            <!-- Policy Management -->
            <div x-data="{ open: false }">
                <button @click="open = !open" class="sidebar-item w-full">
                    <i class="fas fa-file-contract w-5"></i>
                    <span class="flex-1 text-left">Policies</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-collapse class="ml-8 space-y-1 mt-1">
                    <a href="<?php echo base_url('policies'); ?>" class="sidebar-item text-sm">
                        <i class="fas fa-list w-4"></i>
                        <span>All Policies</span>
                    </a>
                    <a href="<?php echo base_url('policies/issue'); ?>" class="sidebar-item text-sm">
                        <i class="fas fa-plus w-4"></i>
                        <span>Issue Policy</span>
                    </a>
                    <a href="<?php echo base_url('policies/renewals'); ?>" class="sidebar-item text-sm">
                        <i class="fas fa-rotate w-4"></i>
                        <span>Renewals</span>
                    </a>
                    <a href="<?php echo base_url('policies/endorsements'); ?>" class="sidebar-item text-sm">
                        <i class="fas fa-edit w-4"></i>
                        <span>Endorsements</span>
                    </a>
                </div>
            </div>

            <!-- Claims Management -->
            <div x-data="{ open: false }">
                <button @click="open = !open" class="sidebar-item w-full">
                    <i class="fas fa-clipboard-check w-5"></i>
                    <span class="flex-1 text-left">Claims</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-collapse class="ml-8 space-y-1 mt-1">
                    <a href="<?php echo base_url('claims'); ?>" class="sidebar-item text-sm">
                        <i class="fas fa-list w-4"></i>
                        <span>All Claims</span>
                    </a>
                    <a href="<?php echo base_url('claims/register'); ?>" class="sidebar-item text-sm">
                        <i class="fas fa-plus w-4"></i>
                        <span>Register Claim</span>
                    </a>
                    <a href="<?php echo base_url('claims/pending'); ?>" class="sidebar-item text-sm">
                        <i class="fas fa-clock w-4"></i>
                        <span>Pending Claims</span>
                    </a>
                </div>
            </div>

            <!-- Accounting -->
            <div x-data="{ open: false }">
                <button @click="open = !open" class="sidebar-item w-full">
                    <i class="fas fa-calculator w-5"></i>
                    <span class="flex-1 text-left">Accounting</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-collapse class="ml-8 space-y-1 mt-1">
                    <a href="<?php echo base_url('accounts/chart'); ?>" class="sidebar-item text-sm">
                        <i class="fas fa-sitemap w-4"></i>
                        <span>Chart of Accounts</span>
                    </a>
                    <a href="<?php echo base_url('journals'); ?>" class="sidebar-item text-sm">
                        <i class="fas fa-book w-4"></i>
                        <span>Journals</span>
                    </a>
                    <a href="<?php echo base_url('reports/ledger'); ?>" class="sidebar-item text-sm">
                        <i class="fas fa-file-alt w-4"></i>
                        <span>Ledger</span>
                    </a>
                </div>
            </div>

            <!-- Reports -->
            <div x-data="{ open: false }">
                <button @click="open = !open" class="sidebar-item w-full">
                    <i class="fas fa-chart-bar w-5"></i>
                    <span class="flex-1 text-left">Reports</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-collapse class="ml-8 space-y-1 mt-1">
                    <a href="<?php echo base_url('reports/financial'); ?>" class="sidebar-item text-sm">
                        <i class="fas fa-coins w-4"></i>
                        <span>Financial Reports</span>
                    </a>
                    <a href="<?php echo base_url('reports/insurance'); ?>" class="sidebar-item text-sm">
                        <i class="fas fa-shield w-4"></i>
                        <span>Insurance Reports</span>
                    </a>
                    <a href="<?php echo base_url('reports/vat'); ?>" class="sidebar-item text-sm">
                        <i class="fas fa-percentage w-4"></i>
                        <span>VAT Reports</span>
                    </a>
                </div>
            </div>

            <!-- Settings -->
            <a href="<?php echo base_url('settings'); ?>" class="sidebar-item">
                <i class="fas fa-cog w-5"></i>
                <span>Settings</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="transition-all duration-300" :class="sidebarOpen ? 'ml-64' : 'ml-0'">

        <!-- Top Navbar -->
        <nav class="navbar">
            <!-- Left: Sidebar Toggle & Breadcrumb -->
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-bars text-gray-600"></i>
                </button>

                <!-- Breadcrumb -->
                <div class="breadcrumb hidden md:flex">
                    <a href="<?php echo base_url(); ?>" class="breadcrumb-item">
                        <i class="fas fa-home"></i>
                    </a>
                    <?php if (isset($breadcrumbs) && is_array($breadcrumbs)): ?>
                        <?php foreach ($breadcrumbs as $index => $crumb): ?>
                            <span class="breadcrumb-separator">/</span>
                            <?php if ($index < count($breadcrumbs) - 1): ?>
                                <a href="<?php echo $crumb['url']; ?>" class="breadcrumb-item"><?php echo $crumb['title']; ?></a>
                            <?php else: ?>
                                <span class="text-gray-900 font-medium"><?php echo $crumb['title']; ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right: Search, Notifications, User Menu -->
            <div class="flex items-center gap-3">
                <!-- Search (Desktop) -->
                <div class="relative hidden lg:block">
                    <input type="search" placeholder="Search..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent w-64">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>

                <!-- Notifications -->
                <div x-data="dropdown()" class="relative">
                    <button @click="toggle()" class="relative p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="fas fa-bell text-gray-600 text-lg"></i>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-danger-500 rounded-full"></span>
                    </button>

                    <div x-show="open" @click.away="close()" x-transition class="dropdown w-80">
                        <div class="px-4 py-3 border-b border-gray-200">
                            <p class="font-semibold text-gray-900">Notifications</p>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            <a href="#" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900">New claim registered</p>
                                <p class="text-xs text-gray-500 mt-1">2 minutes ago</p>
                            </a>
                            <a href="#" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900">Policy renewal due</p>
                                <p class="text-xs text-gray-500 mt-1">1 hour ago</p>
                            </a>
                        </div>
                        <a href="<?php echo base_url('notifications'); ?>" class="block px-4 py-3 text-center text-sm text-primary-600 hover:bg-gray-50 font-medium">
                            View all notifications
                        </a>
                    </div>
                </div>

                <!-- User Menu -->
                <div x-data="dropdown()" class="relative">
                    <button @click="toggle()" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <img src="<?php echo base_url('assets/img/default-avatar.png'); ?>" alt="User" class="w-8 h-8 rounded-full">
                        <span class="hidden md:block text-sm font-medium text-gray-700">Admin User</span>
                        <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                    </button>

                    <div x-show="open" @click.away="close()" x-transition class="dropdown">
                        <a href="<?php echo base_url('profile'); ?>" class="dropdown-item">
                            <i class="fas fa-user w-4"></i>
                            <span>Profile</span>
                        </a>
                        <a href="<?php echo base_url('settings'); ?>" class="dropdown-item">
                            <i class="fas fa-cog w-4"></i>
                            <span>Settings</span>
                        </a>
                        <hr class="my-1">
                        <a href="<?php echo base_url('logout'); ?>" class="dropdown-item text-danger-600">
                            <i class="fas fa-sign-out-alt w-4"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="p-6 mt-16 min-h-screen">
            <?php echo isset($content) ? $content : $this->load->view($main_content, '', true); ?>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-4 px-6">
            <div class="flex items-center justify-between text-sm text-gray-600">
                <p>&copy; <?php echo date('Y'); ?> Insurance ERP. All rights reserved.</p>
                <p>Version 3.0.0</p>
            </div>
        </footer>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

    <!-- AOS -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

    <!-- GSAP -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Toastify -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <!-- App JS -->
    <script src="<?php echo base_url('assets/js/app.js'); ?>"></script>

    <!-- Custom JS -->
    <?php if (isset($custom_js)): ?>
        <?php foreach ($custom_js as $js): ?>
            <script src="<?php echo base_url($js); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Inline Scripts -->
    <?php if (isset($inline_scripts)): ?>
        <script>
            <?php echo $inline_scripts; ?>
        </script>
    <?php endif; ?>

</body>
</html>
