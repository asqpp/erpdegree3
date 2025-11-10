# 🎨 Insurance ERP - Modern UI Framework Guide

## Overview

A complete, modern UI framework built with **Tailwind CSS**, **Alpine.js**, **AOS animations**, and **Chart.js** for the Insurance ERP system.

## ✨ Features

- ✅ **Tailwind CSS 3.4** - Utility-first CSS framework
- ✅ **Alpine.js 3.13** - Lightweight JavaScript framework
- ✅ **AOS** - Animate On Scroll library
- ✅ **GSAP** - Professional-grade animations
- ✅ **Chart.js** - Beautiful, responsive charts
- ✅ **SweetAlert2** - Elegant popups
- ✅ **Toastify** - Toast notifications
- ✅ **Font Awesome 6** - 10,000+ icons
- ✅ **Responsive Design** - Mobile-first approach
- ✅ **Dark Mode Ready** - Easy toggle support
- ✅ **RTL Support** - Arabic language ready

## 📦 What's Included

```
erpdegree3/
├── package.json              # NPM dependencies
├── tailwind.config.js         # Tailwind configuration
├── assets/
│   ├── css/
│   │   ├── main.css          # Source CSS (Tailwind imports)
│   │   └── output.css        # Compiled CSS (generated)
│   └── js/
│       └── app.js            # Main JavaScript application
├── application/
│   ├── controllers/
│   │   └── Dashboard.php     # Sample dashboard controller
│   └── views/
│       ├── templates/
│       │   └── modern_layout.php  # Base template layout
│       ├── dashboard/
│       │   └── index.php     # Modern dashboard
│       └── components/
│           └── ui_components.php  # Reusable components
```

## 🚀 Quick Start

### Step 1: Install Node.js & NPM

Make sure you have Node.js installed (v14 or higher):

```bash
# Check Node.js version
node --version

# Check NPM version
npm --version
```

### Step 2: Install Dependencies

```bash
cd /home/user/erpdegree3

# Install all dependencies
npm install
```

This will install:
- Tailwind CSS
- Alpine.js
- AOS (Animate On Scroll)
- Chart.js
- GSAP
- SweetAlert2
- Toastify

### Step 3: Build CSS

```bash
# Development mode (with watch)
npm run dev

# Or build for production
npm run build
```

The compiled CSS will be saved to `assets/css/output.css`.

### Step 4: Access Dashboard

```
http://localhost/erpdegree3/dashboard
```

## 🎨 UI Components Library

### Buttons

```php
<!-- Primary Button -->
<button class="btn btn-primary">
    <i class="fas fa-save"></i>
    Save
</button>

<!-- Secondary Button -->
<button class="btn btn-secondary">Edit</button>

<!-- Danger Button -->
<button class="btn btn-danger">Delete</button>

<!-- Success Button -->
<button class="btn btn-success">Approve</button>

<!-- Sizes -->
<button class="btn btn-primary btn-sm">Small</button>
<button class="btn btn-primary">Regular</button>
<button class="btn btn-primary btn-lg">Large</button>

<!-- Outline & Ghost -->
<button class="btn btn-outline">Cancel</button>
<button class="btn btn-ghost">More</button>
```

### Cards

```php
<!-- Basic Card -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Card Title</h3>
    </div>
    <div class="card-body">
        Card content goes here...
    </div>
</div>

<!-- Card with Hover Effect -->
<div class="card card-hover">
    Content...
</div>

<!-- Using Helper Function -->
<?php card_start('My Card Title'); ?>
    Your content here...
<?php card_end(); ?>
```

### Stat Cards

```php
<!-- Stat Card -->
<div class="stat-card bg-gradient-primary">
    <div class="flex items-start justify-between">
        <div>
            <p class="stat-label">Total Policies</p>
            <h3 class="stat-value">1,234</h3>
            <p class="text-sm mt-2">
                <i class="fas fa-arrow-up"></i>
                12% from last month
            </p>
        </div>
        <div class="bg-white bg-opacity-20 p-3 rounded-lg">
            <i class="fas fa-file-contract text-2xl"></i>
        </div>
    </div>
</div>

<!-- Using Helper Function -->
<?php render_stat_card(
    'Total Policies',
    '1,234',
    '<i class="fas fa-arrow-up"></i> 12% from last month',
    'fas fa-file-contract',
    'bg-gradient-primary',
    100
); ?>
```

### Forms

```php
<!-- Form Input -->
<?php form_input_group(
    'customer_name',
    'Customer Name',
    'text',
    true,  // required
    '',    // value
    'Enter customer name',  // placeholder
    'Full name as per ID'   // help text
); ?>

<!-- Form Select -->
<?php form_select_group(
    'policy_type',
    'Policy Type',
    [
        'motor' => 'Motor Insurance',
        'health' => 'Health Insurance',
        'life' => 'Life Insurance'
    ],
    true,  // required
    '',    // selected value
    'Choose the type of policy'  // help text
); ?>

<!-- Manual Form Input -->
<div class="form-group">
    <label for="email" class="form-label form-label-required">Email</label>
    <input type="email" id="email" name="email" class="form-input" required>
    <p class="form-help">We'll never share your email</p>
</div>
```

### Tables

```php
<!-- Basic Table -->
<?php table_start(['Policy No', 'Customer', 'Type', 'Status', 'Actions']); ?>
    <tr>
        <td>#MTR-2025-001</td>
        <td>Ahmed Al Maktoum</td>
        <td><?php echo badge('Motor', 'primary'); ?></td>
        <td><?php echo status_badge('active'); ?></td>
        <td>
            <button class="btn btn-sm btn-primary">View</button>
        </td>
    </tr>
<?php table_end(); ?>

<!-- Striped Table -->
<?php table_start(['Name', 'Email', 'Status'], true); ?>
    <!-- Table rows -->
<?php table_end(); ?>
```

### Badges

```php
<!-- Status Badges -->
<?php echo badge('Active', 'success'); ?>
<?php echo badge('Pending', 'warning'); ?>
<?php echo badge('Rejected', 'danger'); ?>
<?php echo badge('Draft', 'gray'); ?>

<!-- Auto Status Badge -->
<?php echo status_badge('active'); ?>   <!-- Green -->
<?php echo status_badge('pending'); ?>  <!-- Yellow -->
<?php echo status_badge('rejected'); ?> <!-- Red -->
```

### Alerts

```php
<!-- Success Alert -->
<?php alert('Record saved successfully!', 'success'); ?>

<!-- Warning Alert -->
<?php alert('This action cannot be undone.', 'warning'); ?>

<!-- Danger Alert -->
<?php alert('An error occurred!', 'danger'); ?>

<!-- Info Alert -->
<?php alert('New feature available!', 'info'); ?>

<!-- Non-dismissible Alert -->
<?php alert('Important notice', 'warning', false); ?>
```

### Modals

```php
<!-- Modal Component -->
<div x-data="modal()">
    <!-- Trigger Button -->
    <button @click="open()" class="btn btn-primary">
        Open Modal
    </button>

    <!-- Modal -->
    <div x-show="show" class="modal-overlay" @click.self="close()">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Modal Title</h3>
                <button @click="close()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                Modal content here...
            </div>
            <div class="modal-footer">
                <button @click="close()" class="btn btn-outline">Cancel</button>
                <button class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>
```

### Tabs

```php
<?php tabs_start(['Details', 'Documents', 'History']); ?>

    <?php tab_content_start(0); ?>
        Details content...
    <?php tab_content_end(); ?>

    <?php tab_content_start(1); ?>
        Documents content...
    <?php tab_content_end(); ?>

    <?php tab_content_start(2); ?>
        History content...
    <?php tab_content_end(); ?>

<?php tabs_end(); ?>
```

### Dropdowns

```php
<div x-data="dropdown()">
    <button @click="toggle()" class="btn btn-primary">
        Options <i class="fas fa-chevron-down"></i>
    </button>

    <div x-show="open" @click.away="close()" class="dropdown">
        <a href="#" class="dropdown-item">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="#" class="dropdown-item">
            <i class="fas fa-trash"></i> Delete
        </a>
    </div>
</div>
```

## 📊 Charts

### Line Chart

```javascript
const ctx = document.getElementById('myChart');
ChartHelpers.createLineChart(ctx,
    ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
    [{
        label: 'Premium Collected',
        data: [65000, 75000, 85000, 95000, 105000],
        borderColor: 'rgb(14, 165, 233)',
        backgroundColor: 'rgba(14, 165, 233, 0.1)',
        tension: 0.4,
        fill: true
    }]
);
```

### Bar Chart

```javascript
ChartHelpers.createBarChart(ctx,
    ['Q1', 'Q2', 'Q3', 'Q4'],
    [{
        label: 'Sales',
        data: [12, 19, 15, 20],
        backgroundColor: 'rgba(14, 165, 233, 0.8)'
    }]
);
```

### Pie/Donut Chart

```javascript
ChartHelpers.createPieChart(ctx,
    ['Motor', 'Health', 'Life', 'Others'],
    [45, 25, 20, 10]
);

// Or Donut
ChartHelpers.createDonutChart(ctx,
    ['Motor', 'Health', 'Life', 'Others'],
    [45, 25, 20, 10]
);
```

## 🎭 Animations

### AOS (Animate On Scroll)

```html
<!-- Fade Up -->
<div data-aos="fade-up">Content</div>

<!-- Fade In with Delay -->
<div data-aos="fade-in" data-aos-delay="200">Content</div>

<!-- Slide In -->
<div data-aos="slide-left" data-aos-duration="800">Content</div>

<!-- Available Animations -->
- fade
- fade-up
- fade-down
- fade-left
- fade-right
- slide-up
- slide-down
- slide-left
- slide-right
- zoom-in
- zoom-out
- flip-left
- flip-right
```

### Custom Tailwind Animations

```html
<!-- Fade In -->
<div class="animate-fade-in">Content</div>

<!-- Fade In Up -->
<div class="animate-fade-in-up">Content</div>

<!-- Bounce In -->
<div class="animate-bounce-in">Content</div>

<!-- Slide In Right -->
<div class="animate-slide-in-right">Content</div>
```

### GSAP Animations

```javascript
// Fade in element
gsap.from('.my-element', {
    opacity: 0,
    y: 50,
    duration: 1
});

// Stagger animation
gsap.from('.card', {
    opacity: 0,
    y: 30,
    duration: 0.8,
    stagger: 0.1
});
```

## 🛠 Utility Functions

### JavaScript Utilities

```javascript
// Format Currency
Utils.formatCurrency(1500, 'AED');  // AED 1,500.00

// Format Date
Utils.formatDate('2025-01-10');  // 10/01/2025

// Show Toast
Utils.showToast('Success!', 'success');
Utils.showToast('Error occurred', 'error');

// Confirm Dialog
const confirmed = await Utils.confirm('Delete Policy?', 'This cannot be undone');
if (confirmed) {
    // Proceed with deletion
}

// Copy to Clipboard
Utils.copyToClipboard('Text to copy');

// Download as CSV
Utils.downloadCSV(data, 'export.csv');

// AJAX Request
const result = await Utils.ajax('/api/customers', {
    method: 'POST',
    data: { name: 'John Doe' }
});
```

### PHP Helpers

```php
// Format Currency
<?php echo format_currency(1500); ?>  // AED 1,500.00

// Format Date
<?php echo format_date('2025-01-10'); ?>  // 10/01/2025

// Status Badge
<?php echo status_badge('active'); ?>

// Render Breadcrumb
<?php render_breadcrumb([
    ['title' => 'Customers', 'url' => base_url('customers')],
    ['title' => 'Add New']
]); ?>
```

## 🎨 Color Palette

### Primary Colors
- `primary-50` to `primary-950` - Blue tones
- `secondary-50` to `secondary-950` - Purple tones

### Status Colors
- `success-*` - Green (for approved, active, paid)
- `warning-*` - Yellow/Orange (for pending, review)
- `danger-*` - Red (for rejected, inactive, overdue)
- `info-*` - Blue (for information)

### Gradient Classes
```html
<div class="bg-gradient-primary">...</div>
<div class="bg-gradient-success">...</div>
<div class="bg-gradient-warning">...</div>
<div class="bg-gradient-danger">...</div>
```

## 📱 Responsive Design

All components are mobile-first and responsive:

```html
<!-- Responsive Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Cards -->
</div>

<!-- Responsive Hidden/Shown -->
<div class="hidden md:block">Desktop only</div>
<div class="md:hidden">Mobile only</div>

<!-- Responsive Text -->
<h1 class="text-xl md:text-2xl lg:text-3xl">Title</h1>
```

## 🌙 Dark Mode (Optional)

To enable dark mode:

```javascript
// Toggle dark mode
Alpine.store('app').toggleDarkMode();
```

## 🌍 RTL Support (Arabic)

```html
<!-- Set RTL direction -->
<html dir="rtl" lang="ar">

<!-- Arabic text will automatically use Cairo font -->
<p class="font-arabic">النص العربي</p>
```

## 📝 Creating New Pages

### 1. Create View File

```php
// application/views/customers/list.php
<div class="page-header">
    <h1 class="page-title">Customers</h1>
</div>

<div class="card">
    <!-- Your content -->
</div>
```

### 2. Create Controller

```php
// application/controllers/Customers.php
public function index() {
    $data = [
        'page_title' => 'Customers',
        'breadcrumbs' => [
            ['title' => 'Customers']
        ],
        'main_content' => 'customers/list'
    ];

    $this->load->view('templates/modern_layout', $data);
}
```

## 🐛 Troubleshooting

### CSS Not Loading

```bash
# Rebuild Tailwind CSS
npm run build

# Check output.css was generated
ls -lh assets/css/output.css
```

### Alpine.js Not Working

Check browser console for errors. Make sure Alpine.js is loaded:

```html
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>
```

### Charts Not Displaying

Ensure Chart.js is loaded and canvas element exists:

```javascript
const ctx = document.getElementById('myChart');
if (ctx) {
    // Create chart
}
```

## 📚 Resources

- [Tailwind CSS Docs](https://tailwindcss.com/docs)
- [Alpine.js Docs](https://alpinejs.dev)
- [AOS Docs](https://michalsnik.github.io/aos/)
- [Chart.js Docs](https://www.chartjs.org/docs)
- [Font Awesome Icons](https://fontawesome.com/icons)

## 🎯 Next Steps

1. ✅ Build Tailwind CSS (`npm run build`)
2. ✅ Access dashboard (`/dashboard`)
3. 📝 Create new pages using components
4. 🎨 Customize colors in `tailwind.config.js`
5. 🚀 Build your modules!

---

**Ready to build beautiful UIs!** 🎨✨
