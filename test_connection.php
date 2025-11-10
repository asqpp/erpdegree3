<?php
/**
 * Database Connection Test
 * Quick script to verify database connectivity
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'cybor432_erpdegree');
define('DB_PASS', 'tPJ$=]pJ^s)4');
define('DB_NAME', 'cybor432_erpnew');

?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Connection Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success { color: #22c55e; font-size: 20px; margin-bottom: 20px; }
        .error { color: #ef4444; font-size: 20px; margin-bottom: 20px; }
        .info { background: #f0f9ff; padding: 15px; border-radius: 4px; margin: 10px 0; border-left: 4px solid #0ea5e9; }
        .stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-top: 20px; }
        .stat { background: #f8fafc; padding: 15px; border-radius: 4px; }
        .stat-label { color: #64748b; font-size: 14px; }
        .stat-value { color: #0f172a; font-size: 24px; font-weight: bold; margin-top: 5px; }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #0ea5e9;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
        .btn:hover { background: #0284c7; }
    </style>
</head>
<body>
    <div class="card">
        <h1>🔍 Database Connection Test</h1>

        <?php
        echo "<div class='info'>";
        echo "Host: " . DB_HOST . "<br>";
        echo "User: " . DB_USER . "<br>";
        echo "Database: " . DB_NAME;
        echo "</div>";

        // Test connection
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

        if ($conn->connect_error) {
            echo "<div class='error'>❌ Connection Failed!</div>";
            echo "<p>Error: " . $conn->connect_error . "</p>";
            echo "<p>Please check your database credentials in setup.php</p>";
        } else {
            echo "<div class='success'>✅ Connection Successful!</div>";

            // Check if database exists
            $db_selected = $conn->select_db(DB_NAME);

            if ($db_selected) {
                echo "<div class='success'>✅ Database Found: " . DB_NAME . "</div>";

                // Count tables
                $result = $conn->query("SHOW TABLES");
                $table_count = $result->num_rows;

                // Get database size
                $size_query = "SELECT
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) as size_mb
                    FROM information_schema.TABLES
                    WHERE table_schema = '" . DB_NAME . "'";
                $size_result = $conn->query($size_query);
                $size_data = $size_result->fetch_assoc();

                echo "<div class='stats'>";
                echo "<div class='stat'>";
                echo "<div class='stat-label'>Tables</div>";
                echo "<div class='stat-value'>" . $table_count . "</div>";
                echo "</div>";
                echo "<div class='stat'>";
                echo "<div class='stat-label'>Database Size</div>";
                echo "<div class='stat-value'>" . ($size_data['size_mb'] ?: '0.00') . " MB</div>";
                echo "</div>";
                echo "</div>";

                if ($table_count == 0) {
                    echo "<div class='info' style='margin-top: 20px;'>";
                    echo "⚠️ <strong>Database is empty!</strong><br>";
                    echo "Run the setup.php to install 135+ tables.";
                    echo "</div>";
                    echo "<a href='setup.php' class='btn'>Run Setup Now</a>";
                } else if ($table_count < 100) {
                    echo "<div class='info' style='margin-top: 20px;'>";
                    echo "⚠️ <strong>Incomplete Installation!</strong><br>";
                    echo "Expected 135+ tables, found only " . $table_count . " tables.<br>";
                    echo "Please run setup.php again.";
                    echo "</div>";
                    echo "<a href='setup.php' class='btn'>Complete Setup</a>";
                } else {
                    echo "<div class='success' style='margin-top: 20px;'>";
                    echo "🎉 <strong>Installation Complete!</strong><br>";
                    echo "All " . $table_count . " tables are ready.";
                    echo "</div>";
                    echo "<a href='dashboard' class='btn'>Go to Dashboard</a>";
                }

            } else {
                echo "<div class='error'>❌ Database Not Found: " . DB_NAME . "</div>";
                echo "<p>The database '" . DB_NAME . "' does not exist.</p>";
                echo "<div class='info'>";
                echo "Run setup.php to create the database and install all tables.";
                echo "</div>";
                echo "<a href='setup.php' class='btn'>Run Setup</a>";
            }
        }

        $conn->close();
        ?>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0; color: #64748b; font-size: 14px;">
            <strong>Next Steps:</strong>
            <ol style="margin-top: 10px;">
                <li>If database is empty, run <code>setup.php</code> to install</li>
                <li>Build UI with <code>npm install && npm run build</code></li>
                <li>Access your ERP at <code>/dashboard</code></li>
            </ol>
        </div>
    </div>
</body>
</html>
