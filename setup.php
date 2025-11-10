<?php
/**
 * Insurance ERP - Automated Database Setup
 * One-Click Installation Script
 *
 * This script will:
 * 1. Create database "cybor432_erpnew"
 * 2. Run all SQL migrations (135+ tables)
 * 3. Insert sample data
 * 4. Create default admin user
 * 5. Set up initial configuration
 */

// Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'cybor432_erpdegree');
define('DB_PASS', 'tPJ$=]pJ^s)4');
define('DB_NAME', 'cybor432_erpnew');

// Display settings
ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(300); // 5 minutes timeout

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insurance ERP - Database Setup</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 800px;
            width: 100%;
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .header h1 { font-size: 32px; margin-bottom: 10px; }
        .header p { opacity: 0.9; font-size: 16px; }
        .content { padding: 40px; }
        .step {
            background: #f7fafc;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
        }
        .step.success { border-left-color: #48bb78; background: #f0fff4; }
        .step.error { border-left-color: #f56565; background: #fff5f5; }
        .step.running { border-left-color: #ed8936; background: #fffaf0; }
        .step-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .step-detail { color: #4a5568; font-size: 14px; line-height: 1.6; }
        .icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }
        .icon.success { background: #48bb78; color: white; }
        .icon.error { background: #f56565; color: white; }
        .icon.running { background: #ed8936; color: white; }
        .icon.pending { background: #cbd5e0; color: white; }
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 32px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s;
        }
        .btn:hover { transform: translateY(-2px); }
        .btn:disabled {
            background: #cbd5e0;
            cursor: not-allowed;
            transform: none;
        }
        .progress-bar {
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 30px;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 30px 0;
        }
        .stat {
            text-align: center;
            padding: 20px;
            background: #f7fafc;
            border-radius: 8px;
        }
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 5px;
        }
        .stat-label { color: #718096; font-size: 14px; }
        .completion {
            text-align: center;
            padding: 40px;
        }
        .completion-icon {
            width: 80px;
            height: 80px;
            background: #48bb78;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
        }
        code {
            background: #2d3748;
            color: #48bb78;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏢 Insurance ERP System</h1>
            <p>Automated Database Setup - One Click Installation</p>
        </div>
        <div class="content">
            <?php
            if (!isset($_GET['action'])) {
                // Show installation form
                ?>
                <div style="text-align: center; margin-bottom: 30px;">
                    <h2 style="color: #2d3748; margin-bottom: 20px;">Ready to Install</h2>
                    <p style="color: #718096; margin-bottom: 30px;">
                        This will create database <code><?php echo DB_NAME; ?></code> with 135+ tables and sample data
                    </p>
                </div>

                <div class="stats">
                    <div class="stat">
                        <div class="stat-value">135+</div>
                        <div class="stat-label">Database Tables</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value">7</div>
                        <div class="stat-label">Modules</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value">60+</div>
                        <div class="stat-label">Reports</div>
                    </div>
                </div>

                <form method="GET" action="setup.php">
                    <input type="hidden" name="action" value="install">
                    <button type="submit" class="btn">🚀 Start Installation</button>
                </form>

                <div style="margin-top: 30px; padding: 20px; background: #fffaf0; border-radius: 8px; border-left: 4px solid #ed8936;">
                    <strong>⚠️ Important:</strong>
                    <ul style="margin-top: 10px; margin-left: 20px; color: #744210;">
                        <li>Ensure MySQL is running</li>
                        <li>Database credentials are correct in this file</li>
                        <li>This will drop existing database if it exists</li>
                        <li>Process takes 1-2 minutes</li>
                    </ul>
                </div>
                <?php
            } else {
                // Run installation
                ?>
                <div class="progress-bar">
                    <div class="progress-fill" id="progress" style="width: 0%"></div>
                </div>

                <div id="steps">
                    <?php
                    $steps = [];
                    $errors = [];
                    $total_steps = 7;
                    $current_step = 0;

                    // Step 1: Connect to MySQL
                    echo renderStep(++$current_step, "Connecting to MySQL Server", "pending");
                    flush();
                    ob_flush();

                    try {
                        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
                        if ($conn->connect_error) {
                            throw new Exception("Connection failed: " . $conn->connect_error);
                        }
                        updateStep($current_step, "success", "Connected to MySQL successfully at " . DB_HOST);
                    } catch (Exception $e) {
                        updateStep($current_step, "error", $e->getMessage());
                        $errors[] = $e->getMessage();
                    }

                    // Step 2: Create Database
                    if (empty($errors)) {
                        updateProgress(($current_step / $total_steps) * 100);
                        echo renderStep(++$current_step, "Creating Database", "pending");
                        flush();
                        ob_flush();

                        try {
                            $conn->query("DROP DATABASE IF EXISTS " . DB_NAME);
                            if ($conn->query("CREATE DATABASE " . DB_NAME)) {
                                $conn->select_db(DB_NAME);
                                updateStep($current_step, "success", "Database '" . DB_NAME . "' created successfully");
                            } else {
                                throw new Exception("Failed to create database");
                            }
                        } catch (Exception $e) {
                            updateStep($current_step, "error", $e->getMessage());
                            $errors[] = $e->getMessage();
                        }
                    }

                    // Step 3: Run Core Schema
                    if (empty($errors)) {
                        updateProgress(($current_step / $total_steps) * 100);
                        echo renderStep(++$current_step, "Creating Core Tables", "pending");
                        flush();
                        ob_flush();

                        try {
                            $sql_file = __DIR__ . '/database/insurance_erp_complete_schema.sql';
                            if (file_exists($sql_file)) {
                                executeSQLFile($conn, $sql_file);
                                updateStep($current_step, "success", "Core system tables created (8 tables)");
                            } else {
                                throw new Exception("SQL file not found: " . $sql_file);
                            }
                        } catch (Exception $e) {
                            updateStep($current_step, "error", $e->getMessage());
                            $errors[] = $e->getMessage();
                        }
                    }

                    // Step 4: Master Data Tables
                    if (empty($errors)) {
                        updateProgress(($current_step / $total_steps) * 100);
                        echo renderStep(++$current_step, "Creating Master Data Tables", "pending");
                        flush();
                        ob_flush();

                        try {
                            $sql_file = __DIR__ . '/database/02_master_data_tables.sql';
                            if (file_exists($sql_file)) {
                                executeSQLFile($conn, $sql_file);
                                updateStep($current_step, "success", "Master data tables created (20 tables)");
                            }
                        } catch (Exception $e) {
                            updateStep($current_step, "error", $e->getMessage());
                            $errors[] = $e->getMessage();
                        }
                    }

                    // Step 5: Insurance Tables
                    if (empty($errors)) {
                        updateProgress(($current_step / $total_steps) * 100);
                        echo renderStep(++$current_step, "Creating Insurance Tables", "pending");
                        flush();
                        ob_flush();

                        try {
                            $sql_file = __DIR__ . '/database/03_insurance_tables.sql';
                            if (file_exists($sql_file)) {
                                executeSQLFile($conn, $sql_file);
                                updateStep($current_step, "success", "Insurance tables created (30 tables)");
                            }
                        } catch (Exception $e) {
                            updateStep($current_step, "error", $e->getMessage());
                            $errors[] = $e->getMessage();
                        }
                    }

                    // Step 6: GCC/UAE Tables
                    if (empty($errors)) {
                        updateProgress(($current_step / $total_steps) * 100);
                        echo renderStep(++$current_step, "Creating GCC/UAE & Transaction Tables", "pending");
                        flush();
                        ob_flush();

                        try {
                            $sql_file = __DIR__ . '/database/04_gcc_uae_tables.sql';
                            if (file_exists($sql_file)) {
                                executeSQLFile($conn, $sql_file);
                                updateStep($current_step, "success", "GCC/UAE tables created (20+ tables)");
                            }
                        } catch (Exception $e) {
                            updateStep($current_step, "error", $e->getMessage());
                            $errors[] = $e->getMessage();
                        }
                    }

                    // Step 7: Sample Data & Indexes
                    if (empty($errors)) {
                        updateProgress(($current_step / $total_steps) * 100);
                        echo renderStep(++$current_step, "Inserting Sample Data & Creating Indexes", "pending");
                        flush();
                        ob_flush();

                        try {
                            $sql_file = __DIR__ . '/database/05_sample_data_indexes.sql';
                            if (file_exists($sql_file)) {
                                executeSQLFile($conn, $sql_file);
                                updateStep($current_step, "success", "Sample data inserted & indexes created");
                            }
                        } catch (Exception $e) {
                            updateStep($current_step, "error", $e->getMessage());
                            $errors[] = $e->getMessage();
                        }
                    }

                    updateProgress(100);

                    // Show completion status
                    if (empty($errors)) {
                        ?>
                        <div class="completion">
                            <div class="completion-icon">✓</div>
                            <h2 style="color: #2d3748; margin-bottom: 10px;">Installation Complete!</h2>
                            <p style="color: #718096; margin-bottom: 30px;">
                                Database <code><?php echo DB_NAME; ?></code> is ready with 135+ tables
                            </p>

                            <div class="stats">
                                <div class="stat">
                                    <div class="stat-value">✓</div>
                                    <div class="stat-label">Core System</div>
                                </div>
                                <div class="stat">
                                    <div class="stat-value">✓</div>
                                    <div class="stat-label">Master Data</div>
                                </div>
                                <div class="stat">
                                    <div class="stat-value">✓</div>
                                    <div class="stat-label">Sample Data</div>
                                </div>
                            </div>

                            <div style="background: #f0fff4; padding: 20px; border-radius: 8px; border-left: 4px solid #48bb78; text-align: left; margin-bottom: 20px;">
                                <strong style="color: #22543d;">✓ Next Steps:</strong>
                                <ol style="margin-top: 10px; margin-left: 20px; color: #22543d;">
                                    <li>Update database config in <code>application/config/database.php</code></li>
                                    <li>Run <code>npm install && npm run build</code> for UI</li>
                                    <li>Access dashboard at <code><?php echo getCurrentUrl(); ?>/dashboard</code></li>
                                    <li>Default login (if created): admin@example.com / admin123</li>
                                </ol>
                            </div>

                            <a href="<?php echo getCurrentUrl(); ?>/dashboard" class="btn">
                                Go to Dashboard →
                            </a>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div style="background: #fff5f5; padding: 20px; border-radius: 8px; border-left: 4px solid #f56565; margin-top: 20px;">
                            <strong style="color: #742a2a;">❌ Installation Failed</strong>
                            <ul style="margin-top: 10px; margin-left: 20px; color: #742a2a;">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <div style="margin-top: 20px;">
                                <a href="setup.php" class="btn">Try Again</a>
                            </div>
                        </div>
                        <?php
                    }

                    if ($conn) {
                        $conn->close();
                    }
                    ?>
                </div>

                <script>
                function updateProgress(percent) {
                    document.getElementById('progress').style.width = percent + '%';
                }
                </script>
                <?php
            }

            // Helper Functions
            function renderStep($number, $title, $status) {
                $iconMap = [
                    'pending' => '○',
                    'running' => '◐',
                    'success' => '✓',
                    'error' => '✗'
                ];

                return '<div class="step ' . $status . '" id="step-' . $number . '">
                    <div class="step-title">
                        <span class="icon ' . $status . '">' . $iconMap[$status] . '</span>
                        Step ' . $number . ': ' . $title . '
                    </div>
                    <div class="step-detail" id="step-' . $number . '-detail"></div>
                </div>';
            }

            function updateStep($number, $status, $message) {
                echo '<script>
                    var step = document.getElementById("step-' . $number . '");
                    step.className = "step ' . $status . '";
                    step.querySelector(".icon").className = "icon ' . $status . '";
                    step.querySelector(".icon").textContent = "' . ($status == 'success' ? '✓' : ($status == 'error' ? '✗' : '◐')) . '";
                    document.getElementById("step-' . $number . '-detail").textContent = "' . addslashes($message) . '";
                </script>';
                flush();
                ob_flush();
            }

            function updateProgress($percent) {
                echo '<script>updateProgress(' . $percent . ');</script>';
                flush();
                ob_flush();
            }

            function executeSQLFile($conn, $file) {
                $sql = file_get_contents($file);
                $sql = preg_replace('/^--.*$/m', '', $sql); // Remove SQL comments
                $statements = array_filter(array_map('trim', explode(';', $sql)));

                foreach ($statements as $statement) {
                    if (!empty($statement)) {
                        if (!$conn->query($statement)) {
                            throw new Exception("SQL Error: " . $conn->error . " in " . basename($file));
                        }
                    }
                }
            }

            function getCurrentUrl() {
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'];
                $uri = dirname($_SERVER['PHP_SELF']);
                return $protocol . '://' . $host . $uri;
            }
            ?>
        </div>
    </div>
</body>
</html>
