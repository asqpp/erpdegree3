<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credit Note - <?php echo $credit_note->credit_note_number; ?></title>
    <style>
        @media print {
            .no-print { display: none; }
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 14px;
        }
        .note-container {
            max-width: 900px;
            margin: 0 auto;
            border: 2px solid #4f46e5;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .note-title {
            font-size: 20px;
            font-weight: bold;
            color: #4f46e5;
            margin-top: 10px;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .info-box {
            width: 48%;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #4f46e5;
            padding: 10px;
            text-align: left;
        }
        .items-table th {
            background-color: #eef2ff;
            font-weight: bold;
        }
        .totals-section {
            width: 350px;
            margin-left: auto;
            border: 2px solid #4f46e5;
            padding: 15px;
            background-color: #f5f3ff;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .grand-total {
            border-top: 2px solid #4f46e5;
            margin-top: 10px;
            padding-top: 10px;
            font-size: 18px;
            font-weight: bold;
        }
        .narration-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9fafb;
            border-left: 3px solid #4f46e5;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ccc;
            font-size: 12px;
            color: #666;
        }
        .print-button {
            background-color: #4f46e5;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .print-button:hover {
            background-color: #4338ca;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-draft {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-posted {
            background-color: #d1fae5;
            color: #065f46;
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center;">
        <button onclick="window.print()" class="print-button">
            🖨️ Print Credit Note
        </button>
        <button onclick="window.close()" class="print-button" style="background-color: #6b7280;">
            ✖ Close
        </button>
    </div>

    <div class="note-container">
        <div class="header">
            <div class="company-name"><?php echo $company_settings->company_name ?? 'Insurance Company Ltd'; ?></div>
            <div><?php echo $company_settings->address ?? 'Dubai, UAE'; ?></div>
            <div>TRN: <?php echo $company_settings->tax_registration_number ?? 'Not Set'; ?></div>
            <div class="note-title">CREDIT NOTE</div>
            <div style="margin-top: 10px;">
                <?php if($credit_note->status == 'draft'): ?>
                    <span class="status-badge status-draft">DRAFT</span>
                <?php else: ?>
                    <span class="status-badge status-posted">POSTED</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="info-section">
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Note Number:</span>
                    <span><?php echo $credit_note->credit_note_number; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date:</span>
                    <span><?php echo date('d/m/Y', strtotime($credit_note->credit_note_date)); ?></span>
                </div>
                <?php if($credit_note->reference_number): ?>
                <div class="info-row">
                    <span class="info-label">Reference:</span>
                    <span><?php echo $credit_note->reference_number; ?></span>
                </div>
                <?php endif; ?>
            </div>

            <div class="info-box">
                <div style="font-weight: bold; margin-bottom: 10px;">Customer Details:</div>
                <div><?php echo $credit_note->customer_name; ?></div>
                <?php if(isset($customer)): ?>
                    <?php if($customer->contact_person): ?>
                        <div>Contact: <?php echo $customer->contact_person; ?></div>
                    <?php endif; ?>
                    <?php if($customer->phone): ?>
                        <div>Phone: <?php echo $customer->phone; ?></div>
                    <?php endif; ?>
                    <?php if($customer->email): ?>
                        <div>Email: <?php echo $customer->email; ?></div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Account</th>
                    <th>Description</th>
                    <th style="width: 150px; text-align: right;">Amount (AED)</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach($credit_note_items as $item): ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo $item->account_name; ?></td>
                    <td><?php echo $item->description ?: '-'; ?></td>
                    <td style="text-align: right;"><?php echo number_format($item->amount, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>AED <?php echo number_format($credit_note->subtotal, 2); ?></span>
            </div>
            <div class="total-row">
                <span>VAT (<?php echo $credit_note->vat_percentage; ?>%):</span>
                <span>AED <?php echo number_format($credit_note->vat_amount, 2); ?></span>
            </div>
            <div class="total-row grand-total">
                <span>Grand Total:</span>
                <span>AED <?php echo number_format($credit_note->total_amount, 2); ?></span>
            </div>
        </div>

        <?php if($credit_note->narration): ?>
        <div class="narration-section">
            <strong>Narration:</strong>
            <p style="margin: 10px 0 0 0;"><?php echo nl2br($credit_note->narration); ?></p>
        </div>
        <?php endif; ?>

        <div class="footer">
            <div style="margin-bottom: 5px;">
                <strong>Created by:</strong> <?php echo $credit_note->created_by_name; ?> on <?php echo date('d/m/Y H:i', strtotime($credit_note->created_at)); ?>
            </div>
            <?php if($credit_note->posted_at): ?>
                <div style="margin-bottom: 5px;">
                    <strong>Posted on:</strong> <?php echo date('d/m/Y H:i', strtotime($credit_note->posted_at)); ?>
                </div>
            <?php endif; ?>
            <?php if($credit_note->journal_entry_id): ?>
                <div>
                    <strong>Journal Entry:</strong> JE-<?php echo str_pad($credit_note->journal_entry_id, 6, '0', STR_PAD_LEFT); ?>
                </div>
            <?php endif; ?>
            <div style="margin-top: 15px; text-align: center;">
                Generated on <?php echo date('d/m/Y H:i:s'); ?>
            </div>
        </div>
    </div>
</body>
</html>
