<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Backup Model
 * Handles database operations for backup management
 */
class Backup_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get all backups
     */
    public function get_backups() {
        $this->db->select('db.*, u.username as created_by_name');
        $this->db->from('database_backups db');
        $this->db->join('users u', 'u.user_id = db.created_by', 'left');
        $this->db->order_by('db.created_at', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get single backup by ID
     */
    public function get_backup($backup_id) {
        $this->db->select('db.*, u.username as created_by_name');
        $this->db->from('database_backups db');
        $this->db->join('users u', 'u.user_id = db.created_by', 'left');
        $this->db->where('db.backup_id', $backup_id);

        return $this->db->get()->row();
    }

    /**
     * Insert backup record
     */
    public function insert_backup($data) {
        $this->db->insert('database_backups', $data);
        return $this->db->insert_id();
    }

    /**
     * Delete backup record
     */
    public function delete_backup($backup_id) {
        $this->db->where('backup_id', $backup_id);
        $this->db->delete('database_backups');

        return $this->db->affected_rows() > 0;
    }

    /**
     * Get backup statistics
     */
    public function get_statistics() {
        $stats = array(
            'total_backups' => 0,
            'total_size' => 0,
            'completed' => 0,
            'failed' => 0,
            'last_backup_date' => null
        );

        // Total backups
        $this->db->select('COUNT(*) as count, SUM(backup_size) as total_size');
        $this->db->from('database_backups');
        $result = $this->db->get()->row();

        if ($result) {
            $stats['total_backups'] = $result->count;
            $stats['total_size'] = $result->total_size ? $result->total_size : 0;
        }

        // Count by status
        $this->db->select('COUNT(*) as count');
        $this->db->from('database_backups');
        $this->db->where('status', 'completed');
        $stats['completed'] = $this->db->get()->row()->count;

        $this->db->select('COUNT(*) as count');
        $this->db->from('database_backups');
        $this->db->where('status', 'failed');
        $stats['failed'] = $this->db->get()->row()->count;

        // Last backup date
        $this->db->select('created_at');
        $this->db->from('database_backups');
        $this->db->where('status', 'completed');
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(1);
        $last_backup = $this->db->get()->row();

        if ($last_backup) {
            $stats['last_backup_date'] = $last_backup->created_at;
        }

        return $stats;
    }

    /**
     * Delete old backups (keep only last N backups)
     */
    public function delete_old_backups($keep_count = 10) {
        // Get list of backups to delete (excluding the most recent N)
        $this->db->select('backup_id, backup_file_path');
        $this->db->from('database_backups');
        $this->db->where('status', 'completed');
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(10000, $keep_count); // Skip first N, get rest

        $old_backups = $this->db->get()->result();

        $deleted_count = 0;

        foreach ($old_backups as $backup) {
            // Delete file from disk
            $file_path = FCPATH . $backup->backup_file_path;
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            // Delete database record
            $this->db->where('backup_id', $backup->backup_id);
            $this->db->delete('database_backups');

            $deleted_count++;
        }

        return $deleted_count;
    }
}
