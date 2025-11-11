<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| AUTO-LOADER (Insurance ERP Custom Configuration)
| -------------------------------------------------------------------
| This file contains the autoload configuration for Insurance ERP System
|
| Key Libraries:
| - database: Database connection
| - session: Session management
| - form_validation: Form validation
|
| Key Helpers:
| - url: URL helpers
| - file: File operations
| - form: Form helpers
| - date: Date/time helpers
| - security: Security helpers
|
*/

$autoload['packages'] = array();

// Core libraries needed for Insurance ERP
$autoload['libraries'] = array(
    'database',           // Database access
    'session',            // Session management
    'form_validation'     // Form validation
);

$autoload['drivers'] = array();

// Essential helpers for the system
$autoload['helper'] = array(
    'url',               // URL helpers
    'file',              // File operations
    'form',              // Form helpers
    'security',          // Security helpers
    'text',              // Text manipulation
    'date'               // Date/time helpers
);

$autoload['config'] = array();

$autoload['language'] = array();

$autoload['model'] = array();
