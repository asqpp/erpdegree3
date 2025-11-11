<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Backup Controller
 * Handles database backup and restore operations
 */
class Backup extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Backup_model');
        $this->load->library('session');
        $this->load->helper(array('url', 'download'));
        $this->load->dbutil();

        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }

        // Check if user has admin role
        if ($this->session->userdata('role_id') != 1) {
            show_error('You do not have permission to access this page', 403);
        }
    }

    /**
     * List all backups
     */
    public function index() {
        $data['title'] = 'Database Backups';
        $data['backups'] = $this->Backup_model->get_backups();
        $data['statistics'] = $this->Backup_model->get_statistics();

        $this->load->view('templates/header', $data);
        $this->load->view('backup/list', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Create new backup
     */
    public function create() {
        // Generate backup filename
        $backup_name = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $backup_path = FCPATH . 'backups/';

        // Create backups directory if it doesn't exist
        if (!is_dir($backup_path)) {
            mkdir($backup_path, 0755, true);
        }

        $backup_file = $backup_path . $backup_name;

        try {
            // Get database configuration
            $db_config = $this->db->database;

            // Create backup using mysqldump
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s 2>&1',
                escapeshellarg($this->db->username),
                escapeshellarg($this->db->password),
                escapeshellarg($this->db->hostname),
                escapeshellarg($this->db->database),
                escapeshellarg($backup_file)
            );

            exec($command, $output, $return_var);

            if ($return_var === 0 && file_exists($backup_file)) {
                // Get file size
                $file_size = filesize($backup_file);

                // Save backup record to database
                $backup_data = array(
                    'backup_name' => $backup_name,
                    'backup_file_path' => 'backups/' . $backup_name,
                    'backup_size' => $file_size,
                    'backup_type' => 'manual',
                    'status' => 'completed',
                    'created_by' => $this->session->userdata('user_id'),
                    'company_id' => $this->session->userdata('company_id')
                );

                $this->Backup_model->insert_backup($backup_data);

                $this->session->set_flashdata('success', 'Database backup created successfully (' . $this->format_bytes($file_size) . ')');
            } else {
                // Backup failed
                $error_message = implode("\n", $output);

                $backup_data = array(
                    'backup_name' => $backup_name,
                    'backup_file_path' => 'backups/' . $backup_name,
                    'backup_size' => 0,
                    'backup_type' => 'manual',
                    'status' => 'failed',
                    'error_message' => $error_message,
                    'created_by' => $this->session->userdata('user_id'),
                    'company_id' => $this->session->userdata('company_id')
                );

                $this->Backup_model->insert_backup($backup_data);

                $this->session->set_flashdata('error', 'Backup failed: ' . $error_message);
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Backup error: ' . $e->getMessage());
        }

        redirect('backup');
    }

    /**
     * Download backup file
     */
    public function download($backup_id) {
        $backup = $this->Backup_model->get_backup($backup_id);

        if (!$backup) {
            show_404();
        }

        $file_path = FCPATH . $backup->backup_file_path;

        if (file_exists($file_path)) {
            force_download($file_path, NULL);
        } else {
            $this->session->set_flashdata('error', 'Backup file not found');
            redirect('backup');
        }
    }

    /**
     * Restore from backup
     */
    public function restore($backup_id) {
        $backup = $this->Backup_model->get_backup($backup_id);

        if (!$backup) {
            show_404();
        }

        $file_path = FCPATH . $backup->backup_file_path;

        if (!file_exists($file_path)) {
            $this->session->set_flashdata('error', 'Backup file not found');
            redirect('backup');
        }

        try {
            // Execute SQL file
            $command = sprintf(
                'mysql --user=%s --password=%s --host=%s %s < %s 2>&1',
                escapeshellarg($this->db->username),
                escapeshellarg($this->db->password),
                escapeshellarg($this->db->hostname),
                escapeshellarg($this->db->database),
                escapeshellarg($file_path)
            );

            exec($command, $output, $return_var);

            if ($return_var === 0) {
                $this->session->set_flashdata('success', 'Database restored successfully from backup: ' . $backup->backup_name);
            } else {
                $error_message = implode("\n", $output);
                $this->session->set_flashdata('error', 'Restore failed: ' . $error_message);
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Restore error: ' . $e->getMessage());
        }

        redirect('backup');
    }

    /**
     * Delete backup
     */
    public function delete($backup_id) {
        $backup = $this->Backup_model->get_backup($backup_id);

        if (!$backup) {
            show_404();
        }

        $file_path = FCPATH . $backup->backup_file_path;

        // Delete file from disk
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // Delete database record
        if ($this->Backup_model->delete_backup($backup_id)) {
            $this->session->set_flashdata('success', 'Backup deleted successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete backup');
        }

        redirect('backup');
    }

    /**
     * Upload backup file
     */
    public function upload() {
        if ($this->input->post()) {
            $backup_path = FCPATH . 'backups/';

            // Create backups directory if it doesn't exist
            if (!is_dir($backup_path)) {
                mkdir($backup_path, 0755, true);
            }

            $config['upload_path'] = $backup_path;
            $config['allowed_types'] = 'sql';
            $config['max_size'] = 102400; // 100MB
            $config['file_name'] = 'uploaded_backup_' . time() . '.sql';

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('backup_file')) {
                $upload_data = $this->upload->data();

                // Save backup record to database
                $backup_data = array(
                    'backup_name' => $upload_data['file_name'],
                    'backup_file_path' => 'backups/' . $upload_data['file_name'],
                    'backup_size' => $upload_data['file_size'] * 1024, // Convert KB to bytes
                    'backup_type' => 'manual',
                    'status' => 'completed',
                    'created_by' => $this->session->userdata('user_id'),
                    'company_id' => $this->session->userdata('company_id')
                );

                $this->Backup_model->insert_backup($backup_data);

                $this->session->set_flashdata('success', 'Backup file uploaded successfully');
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors());
            }

            redirect('backup');
        }

        $data['title'] = 'Upload Backup';

        $this->load->view('templates/header', $data);
        $this->load->view('backup/upload', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Automatic backup (can be called via cron job)
     */
    public function auto_backup() {
        // This method can be called via cron job for automatic backups
        $backup_name = 'auto_backup_' . date('Y-m-d_H-i-s') . '.sql';
        $backup_path = FCPATH . 'backups/';

        if (!is_dir($backup_path)) {
            mkdir($backup_path, 0755, true);
        }

        $backup_file = $backup_path . $backup_name;

        try {
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s 2>&1',
                escapeshellarg($this->db->username),
                escapeshellarg($this->db->password),
                escapeshellarg($this->db->hostname),
                escapeshellarg($this->db->database),
                escapeshellarg($backup_file)
            );

            exec($command, $output, $return_var);

            if ($return_var === 0 && file_exists($backup_file)) {
                $file_size = filesize($backup_file);

                $backup_data = array(
                    'backup_name' => $backup_name,
                    'backup_file_path' => 'backups/' . $backup_name,
                    'backup_size' => $file_size,
                    'backup_type' => 'automatic',
                    'status' => 'completed',
                    'created_by' => 1, // System user
                    'company_id' => 1
                );

                $this->Backup_model->insert_backup($backup_data);

                echo "Automatic backup completed: $backup_name (" . $this->format_bytes($file_size) . ")\n";
            } else {
                echo "Automatic backup failed\n";
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function format_bytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
