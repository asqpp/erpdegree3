<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $receipt->voucher_type == 'receipt' ? 'Receipt' : 'Payment'; ?> Voucher - <?php echo $receipt->voucher_number; ?></title>
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
        .voucher-container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #333;
            padding: 20px;
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
        .voucher-title {
            font-size: 18px;
            font-weight: bold;
            color: <?php echo $receipt->voucher_type == 'receipt' ? '#059669' : '#dc2626'; ?>;
            margin-top: 10px;
        }
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 150px;
            padding: 5px;
        }
        .info-value {
            display: table-cell;
            padding: 5px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .total-section {
            text-align: right;
            margin-top: 20px;
            font-size: 16px;
        }
        .total-amount {
            font-size: 20px;
            font-weight: bold;
            color: <?php echo $receipt->voucher_type == 'receipt' ? '#059669' : '#dc2626'; ?>;
        }
        .signatures {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
        }
        .print-button {
            background-color: #3b82f6;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .print-button:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center;">
        <button onclick="window.print()" class="print-button">
            🖨️ Print Voucher
        </button>
        <button onclick="window.close()" class="print-button" style="background-color: #6b7280;">
            ✖ Close
        </button>
    </div>

    <div class="voucher-container">
        <div class="header">
            <div class="company-name"><?php echo $company_settings->company_name ?? 'Insurance Company Ltd'; ?></div>
            <div><?php echo $company_settings->address ?? 'Dubai, UAE'; ?></div>
            <div>TRN: <?php echo $company_settings->tax_registration_number ?? 'Not Set'; ?></div>
            <div class="voucher-title">
                <?php echo $receipt->voucher_type == 'receipt' ? 'RECEIPT VOUCHER' : 'PAYMENT VOUCHER'; ?>
            </div>
        </div>

        <div class="info-section">
            <div class="info-row">
                <div class="info-label">Voucher Number:</div>
                <div class="info-value"><?php echo $receipt->voucher_number; ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Date:</div>
                <div class="info-value"><?php echo date('d/m/Y', strtotime($receipt->voucher_date)); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Party Name:</div>
                <div class="info-value"><?php echo $receipt->party_name; ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Payment Method:</div>
                <div class="info-value"><?php echo ucfirst($receipt->payment_method); ?></div>
            </div>
            <?php if($receipt->cheque_number): ?>
            <div class="info-row">
                <div class="info-label">Cheque Number:</div>
                <div class="info-value"><?php echo $receipt->cheque_number; ?></div>
            </div>
            <?php endif; ?>
            <?php if($receipt->bank_name): ?>
            <div class="info-row">
                <div class="info-label">Bank:</div>
                <div class="info-value"><?php echo $receipt->bank_name; ?></div>
            </div>
            <?php endif; ?>
        </div>

        <?php if(isset($receipt_items) && count($receipt_items) > 0): ?>
        <table class="items-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Account</th>
                    <th>Description</th>
                    <th style="text-align: right;">Amount (AED)</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach($receipt_items as $item): ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo $item->account_name; ?></td>
                    <td><?php echo $item->description ?? '-'; ?></td>
                    <td style="text-align: right;"><?php echo number_format($item->amount, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <?php if($receipt->narration): ?>
        <div style="margin: 20px 0;">
            <strong>Narration:</strong>
            <p style="margin-top: 5px;"><?php echo nl2br($receipt->narration); ?></p>
        </div>
        <?php endif; ?>

        <div class="total-section">
            <div>Total Amount:</div>
            <div class="total-amount">AED <?php echo number_format($receipt->total_amount, 2); ?></div>
        </div>

        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line">Prepared By</div>
                <div><?php echo $receipt->created_by_name ?? ''; ?></div>
            </div>
            <div class="signature-box">
                <div class="signature-line">Approved By</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">Received By</div>
            </div>
        </div>

        <div style="margin-top: 30px; text-align: center; font-size: 12px; color: #666;">
            Generated on <?php echo date('d/m/Y H:i:s'); ?>
        </div>
    </div>

    <script>
        // Auto-print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
