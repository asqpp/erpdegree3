<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard Controller
 * Displays main dashboard with statistics and charts
 */
class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Load any required models, libraries, helpers
        $this->load->helper('url');
        // Check if user is logged in (add your auth check here)
        // if (!$this->session->userdata('logged_in')) {
        //     redirect('login');
        // }
    }

    /**
     * Display dashboard
     */
    public function index() {
        $data = [
            'page_title' => 'Dashboard',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('dashboard')]
            ],
            'main_content' => 'dashboard/index'
        ];

        // Load dashboard with modern layout
        $this->load->view('templates/modern_layout', $data);
    }

    /**
     * Get dashboard statistics (AJAX)
     */
    public function get_stats() {
        // Example: Return JSON data for charts
        $stats = [
            'total_policies' => 1234,
            'active_claims' => 45,
            'premium_collected' => 2500000,
            'total_customers' => 567,
            'premium_trend' => [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                'data' => [65000, 75000, 85000, 95000, 105000, 115000, 125000, 135000, 145000, 155000, 165000, 175000]
            ],
            'claims_by_status' => [
                'labels' => ['Registered', 'Investigating', 'Approved', 'Settled', 'Rejected'],
                'data' => [12, 8, 15, 20, 5]
            ],
            'policy_distribution' => [
                'labels' => ['Motor', 'Health', 'Life', 'Others'],
                'data' => [45, 25, 20, 10]
            ]
        ];

        header('Content-Type: application/json');
        echo json_encode($stats);
    }

    /**
     * Get recent activities (AJAX)
     */
    public function get_activities() {
        $activities = [
            [
                'type' => 'policy',
                'title' => 'New policy issued',
                'description' => 'Policy #MTR-2025-001 issued to Ahmed Al Maktoum',
                'time' => '5 minutes ago',
                'icon' => 'check',
                'color' => 'success'
            ],
            [
                'type' => 'claim',
                'title' => 'Claim approved',
                'description' => 'Claim #CLM-001 approved for AED 15,000',
                'time' => '1 hour ago',
                'icon' => 'exclamation',
                'color' => 'warning'
            ],
            // Add more activities...
        ];

        header('Content-Type: application/json');
        echo json_encode($activities);
    }
}
