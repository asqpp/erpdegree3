<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Report - <?php echo date('d/m/Y'); ?></title>
    <style>
        @media print {
            .no-print { display: none; }
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .report-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
            margin-top: 10px;
        }
        .summary-section {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        .summary-box {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
        }
        .summary-box .label {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }
        .summary-box .value {
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
        }
        .backups-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .backups-table th,
        .backups-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        .backups-table th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .status-completed {
            color: #059669;
            font-weight: bold;
        }
        .status-failed {
            color: #dc2626;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 11px;
            color: #666;
        }
        .print-button {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .print-button:hover {
            background-color: #1d4ed8;
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center;">
        <button onclick="window.print()" class="print-button">
            🖨️ Print Report
        </button>
        <button onclick="window.close()" class="print-button" style="background-color: #6b7280;">
            ✖ Close
        </button>
    </div>

    <div class="report-container">
        <div class="header">
            <div class="company-name"><?php echo $company_settings->company_name ?? 'Insurance Company Ltd'; ?></div>
            <div><?php echo $company_settings->address ?? 'Dubai, UAE'; ?></div>
            <div class="report-title">DATABASE BACKUP REPORT</div>
            <div style="margin-top: 10px;">Generated on <?php echo date('d/m/Y H:i:s'); ?></div>
        </div>

        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-box">
                <div class="label">Total Backups</div>
                <div class="value"><?php echo number_format($statistics['total_backups'] ?? 0); ?></div>
            </div>
            <div class="summary-box">
                <div class="label">Total Size</div>
                <div class="value" style="font-size: 16px;"><?php echo $statistics['total_size'] ?? '0 MB'; ?></div>
            </div>
            <div class="summary-box">
                <div class="label">Last Backup</div>
                <div class="value" style="font-size: 14px;">
                    <?php echo isset($statistics['last_backup']) ? date('d/m/Y', strtotime($statistics['last_backup'])) : 'Never'; ?>
                </div>
            </div>
            <div class="summary-box">
                <div class="label">Auto Backup Status</div>
                <div class="value" style="font-size: 16px; color: <?php echo (isset($statistics['auto_enabled']) && $statistics['auto_enabled']) ? '#059669' : '#dc2626'; ?>;">
                    <?php echo (isset($statistics['auto_enabled']) && $statistics['auto_enabled']) ? 'Enabled' : 'Disabled'; ?>
                </div>
            </div>
        </div>

        <!-- Backups Table -->
        <h3 style="margin: 30px 0 15px 0; font-size: 16px;">Backup History</h3>
        <table class="backups-table">
            <thead>
                <tr>
                    <th style="width: 35%;">Backup Name</th>
                    <th style="width: 15%;">Date</th>
                    <th style="width: 12%;">Type</th>
                    <th style="width: 10%;">Size</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 18%;">Created By</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($backups) && count($backups) > 0): ?>
                    <?php foreach($backups as $backup): ?>
                    <tr>
                        <td>
                            <strong><?php echo $backup->backup_name; ?></strong><br>
                            <span style="font-size: 10px; color: #666;"><?php echo basename($backup->backup_file_path); ?></span>
                        </td>
                        <td>
                            <?php echo date('d/m/Y', strtotime($backup->backup_date)); ?><br>
                            <span style="font-size: 10px; color: #666;"><?php echo date('H:i:s', strtotime($backup->backup_date)); ?></span>
                        </td>
                        <td><?php echo ucfirst($backup->backup_type); ?></td>
                        <td>
                            <?php
                            $size_mb = $backup->backup_size / (1024 * 1024);
                            echo $size_mb < 1 ? number_format($backup->backup_size / 1024, 2) . ' KB' : number_format($size_mb, 2) . ' MB';
                            ?>
                        </td>
                        <td class="status-<?php echo $backup->status; ?>">
                            <?php echo ucfirst($backup->status); ?>
                        </td>
                        <td><?php echo $backup->created_by_name ?? 'System'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px;">No backups available</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Backup Configuration -->
        <h3 style="margin: 30px 0 15px 0; font-size: 16px;">Backup Configuration</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold; width: 250px;">Automatic Backups:</td>
                <td style="padding: 8px; border: 1px solid #ddd;">
                    <?php echo (isset($backup_settings->backup_enabled) && $backup_settings->backup_enabled) ? 'Enabled' : 'Disabled'; ?>
                </td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Backup Frequency:</td>
                <td style="padding: 8px; border: 1px solid #ddd;"><?php echo isset($backup_settings->backup_frequency) ? ucfirst($backup_settings->backup_frequency) : 'Not Set'; ?></td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Retention Period:</td>
                <td style="padding: 8px; border: 1px solid #ddd;"><?php echo $backup_settings->backup_retention_days ?? 30; ?> days</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Backup Path:</td>
                <td style="padding: 8px; border: 1px solid #ddd; font-family: monospace; font-size: 11px;"><?php echo $backup_settings->backup_path ?? './backups/'; ?></td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Compression:</td>
                <td style="padding: 8px; border: 1px solid #ddd;">
                    <?php echo (isset($backup_settings->backup_compression) && $backup_settings->backup_compression) ? 'Enabled (gzip)' : 'Disabled'; ?>
                </td>
            </tr>
        </table>

        <!-- Recommendations -->
        <div style="margin-top: 30px; padding: 15px; background-color: #fef3c7; border-left: 4px solid #f59e0b;">
            <h4 style="margin: 0 0 10px 0; font-size: 14px;"><i>⚠️</i> Backup Best Practices:</h4>
            <ul style="margin: 0; padding-left: 20px; font-size: 11px; line-height: 1.6;">
                <li>Test backup restoration procedures regularly</li>
                <li>Store backups in multiple locations (local + cloud)</li>
                <li>Verify backup file integrity after creation</li>
                <li>Maintain at least 30 days of backup history</li>
                <li>Document backup and restore procedures</li>
                <li>Monitor backup success/failure notifications</li>
            </ul>
        </div>

        <div class="footer">
            <p>This report contains <?php echo count($backups ?? []); ?> backup records</p>
            <p>&copy; <?php echo date('Y'); ?> <?php echo $company_settings->company_name ?? 'Insurance Company Ltd'; ?>. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
