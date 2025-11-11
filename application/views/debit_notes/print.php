<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debit Note - <?php echo $debit_note->debit_note_number; ?></title>
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
            border: 2px solid #7c3aed;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #7c3aed;
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
            color: #7c3aed;
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
            border: 1px solid #7c3aed;
            padding: 10px;
            text-align: left;
        }
        .items-table th {
            background-color: #ede9fe;
            font-weight: bold;
        }
        .totals-section {
            width: 350px;
            margin-left: auto;
            border: 2px solid #7c3aed;
            padding: 15px;
            background-color: #faf5ff;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .grand-total {
            border-top: 2px solid #7c3aed;
            margin-top: 10px;
            padding-top: 10px;
            font-size: 18px;
            font-weight: bold;
        }
        .narration-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9fafb;
            border-left: 3px solid #7c3aed;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ccc;
            font-size: 12px;
            color: #666;
        }
        .print-button {
            background-color: #7c3aed;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .print-button:hover {
            background-color: #6d28d9;
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
            🖨️ Print Debit Note
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
            <div class="note-title">DEBIT NOTE</div>
            <div style="margin-top: 10px;">
                <?php if($debit_note->status == 'draft'): ?>
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
                    <span><?php echo $debit_note->debit_note_number; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date:</span>
                    <span><?php echo date('d/m/Y', strtotime($debit_note->debit_note_date)); ?></span>
                </div>
                <?php if($debit_note->reference_number): ?>
                <div class="info-row">
                    <span class="info-label">Reference:</span>
                    <span><?php echo $debit_note->reference_number; ?></span>
                </div>
                <?php endif; ?>
            </div>

            <div class="info-box">
                <div style="font-weight: bold; margin-bottom: 10px;">Supplier Details:</div>
                <div><?php echo $debit_note->supplier_name; ?></div>
                <?php if(isset($supplier)): ?>
                    <?php if($supplier->contact_person): ?>
                        <div>Contact: <?php echo $supplier->contact_person; ?></div>
                    <?php endif; ?>
                    <?php if($supplier->phone): ?>
                        <div>Phone: <?php echo $supplier->phone; ?></div>
                    <?php endif; ?>
                    <?php if($supplier->email): ?>
                        <div>Email: <?php echo $supplier->email; ?></div>
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
                <?php $i = 1; foreach($debit_note_items as $item): ?>
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
                <span>AED <?php echo number_format($debit_note->subtotal, 2); ?></span>
            </div>
            <div class="total-row">
                <span>VAT (<?php echo $debit_note->vat_percentage; ?>%):</span>
                <span>AED <?php echo number_format($debit_note->vat_amount, 2); ?></span>
            </div>
            <div class="total-row grand-total">
                <span>Grand Total:</span>
                <span>AED <?php echo number_format($debit_note->total_amount, 2); ?></span>
            </div>
        </div>

        <?php if($debit_note->narration): ?>
        <div class="narration-section">
            <strong>Narration:</strong>
            <p style="margin: 10px 0 0 0;"><?php echo nl2br($debit_note->narration); ?></p>
        </div>
        <?php endif; ?>

        <div class="footer">
            <div style="margin-bottom: 5px;">
                <strong>Created by:</strong> <?php echo $debit_note->created_by_name; ?> on <?php echo date('d/m/Y H:i', strtotime($debit_note->created_at)); ?>
            </div>
            <?php if($debit_note->posted_at): ?>
                <div style="margin-bottom: 5px;">
                    <strong>Posted on:</strong> <?php echo date('d/m/Y H:i', strtotime($debit_note->posted_at)); ?>
                </div>
            <?php endif; ?>
            <?php if($debit_note->journal_entry_id): ?>
                <div>
                    <strong>Journal Entry:</strong> JE-<?php echo str_pad($debit_note->journal_entry_id, 6, '0', STR_PAD_LEFT); ?>
                </div>
            <?php endif; ?>
            <div style="margin-top: 15px; text-align: center;">
                Generated on <?php echo date('d/m/Y H:i:s'); ?>
            </div>
        </div>
    </div>
</body>
</html>
