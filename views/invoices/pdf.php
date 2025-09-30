<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $invoice['id']; ?> - SEL Diagnostic Center</title>
    <style>
        @media print {
            .no-print {
                display: none;
            }
            body {
                margin: 0;
                padding: 20px;
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #667eea;
        }

        .company-info h1 {
            color: #667eea;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .company-info p {
            color: #666;
            line-height: 1.6;
            margin: 3px 0;
        }

        .invoice-meta {
            text-align: right;
        }

        .invoice-meta h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .invoice-meta p {
            color: #666;
            margin: 5px 0;
        }

        .invoice-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }

        .detail-section h3 {
            color: #333;
            font-size: 16px;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #f0f0f0;
        }

        .detail-section p {
            color: #666;
            margin: 5px 0;
            line-height: 1.6;
        }

        .detail-section strong {
            color: #333;
            display: inline-block;
            width: 120px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        thead {
            background: #667eea;
            color: white;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }

        th:last-child,
        td:last-child {
            text-align: right;
        }

        tbody tr {
            border-bottom: 1px solid #f0f0f0;
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

        td {
            padding: 15px;
            color: #666;
        }

        .test-code {
            color: #999;
            font-size: 12px;
            display: block;
            margin-top: 3px;
        }

        .totals {
            margin-left: auto;
            width: 300px;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .totals-row.subtotal {
            font-weight: 600;
            color: #333;
        }

        .totals-row.discount {
            color: #28a745;
        }

        .totals-row.grand-total {
            background: #667eea;
            color: white;
            font-weight: 700;
            font-size: 18px;
            border: none;
            margin-top: 10px;
        }

        .payment-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }

        .payment-info h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .payment-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .payment-item {
            text-align: center;
        }

        .payment-item .label {
            color: #999;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .payment-item .value {
            color: #333;
            font-weight: 600;
            font-size: 16px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-paid {
            background: #d4edda;
            color: #155724;
        }

        .status-partial {
            background: #fff3cd;
            color: #856404;
        }

        .status-pending {
            background: #f8d7da;
            color: #721c24;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
            text-align: center;
        }

        .footer p {
            color: #999;
            font-size: 12px;
            margin: 5px 0;
        }

        .notes {
            background: #fffbea;
            padding: 15px;
            border-left: 4px solid #ffc107;
            margin-bottom: 30px;
        }

        .notes h4 {
            color: #333;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .notes p {
            color: #666;
            font-size: 13px;
            line-height: 1.5;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }

        .print-button:hover {
            background: #5568d3;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">🖨️ Print Invoice</button>

    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="company-info">
                <h1>SEL Diagnostic Center</h1>
                <p>📍 123 Medical Road, Dhaka-1205, Bangladesh</p>
                <p>📞 +880 1XXX-XXXXXX</p>
                <p>✉️ info@seldiagnostics.com</p>
                <p>🌐 www.seldiagnostics.com</p>
            </div>
            <div class="invoice-meta">
                <h2>INVOICE</h2>
                <p><strong>Invoice #:</strong> <?php echo str_pad($invoice['id'], 6, '0', STR_PAD_LEFT); ?></p>
                <p><strong>Date:</strong> <?php echo date('d M Y', strtotime($invoice['created_at'])); ?></p>
                <p><strong>Time:</strong> <?php echo date('h:i A', strtotime($invoice['created_at'])); ?></p>
            </div>
        </div>

        <!-- Patient & Doctor Info -->
        <div class="invoice-details">
            <div class="detail-section">
                <h3>Patient Information</h3>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($invoice['patient_name']); ?></p>
                <p><strong>Age:</strong> <?php echo htmlspecialchars($invoice['patient_age']); ?> years</p>
                <p><strong>Gender:</strong> <?php echo htmlspecialchars($invoice['patient_gender']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($invoice['patient_phone']); ?></p>
                <?php if (!empty($invoice['patient_email'])): ?>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($invoice['patient_email']); ?></p>
                <?php endif; ?>
                <?php if (!empty($invoice['patient_address'])): ?>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($invoice['patient_address']); ?></p>
                <?php endif; ?>
            </div>

            <?php if (!empty($invoice['doctor_name'])): ?>
                <div class="detail-section">
                    <h3>Referred By</h3>
                    <p><strong>Doctor:</strong> <?php echo htmlspecialchars($invoice['doctor_name']); ?></p>
                    <?php if (!empty($invoice['qualifications'])): ?>
                        <p><strong>Qualifications:</strong> <?php echo htmlspecialchars($invoice['qualifications']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($invoice['workplace'])): ?>
                        <p><strong>Workplace:</strong> <?php echo htmlspecialchars($invoice['workplace']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($invoice['doctor_phone'])): ?>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($invoice['doctor_phone']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Notes -->
        <?php if (!empty($invoice['notes'])): ?>
            <div class="notes">
                <h4>Notes:</h4>
                <p><?php echo htmlspecialchars($invoice['notes']); ?></p>
            </div>
        <?php endif; ?>

        <!-- Tests Table -->
        <table>
            <thead>
                <tr>
                    <th>Test Name</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoice_tests as $test): ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($test['test_name']); ?>
                            <span class="test-code"><?php echo htmlspecialchars($test['test_code']); ?></span>
                        </td>
                        <td>৳<?php echo number_format($test['price'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <div class="totals-row subtotal">
                <span>Subtotal:</span>
                <span>৳<?php echo number_format($invoice['total_amount'], 2); ?></span>
            </div>
            <?php if ($invoice['discount_amount'] > 0): ?>
                <div class="totals-row discount">
                    <span>Discount:</span>
                    <span>-৳<?php echo number_format($invoice['discount_amount'], 2); ?></span>
                </div>
            <?php endif; ?>
            <div class="totals-row grand-total">
                <span>Grand Total:</span>
                <span>৳<?php echo number_format($invoice['total_amount'] - $invoice['discount_amount'], 2); ?></span>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="payment-info">
            <h3>Payment Information</h3>
            <div class="payment-grid">
                <div class="payment-item">
                    <div class="label">Status</div>
                    <div class="value">
                        <span class="status-badge status-<?php echo $invoice['payment_status']; ?>">
                            <?php echo strtoupper($invoice['payment_status']); ?>
                        </span>
                    </div>
                </div>
                <div class="payment-item">
                    <div class="label">Amount Paid</div>
                    <div class="value">৳<?php echo number_format($invoice['amount_paid'], 2); ?></div>
                </div>
                <div class="payment-item">
                    <div class="label">Balance Due</div>
                    <div class="value">৳<?php echo number_format(($invoice['total_amount'] - $invoice['discount_amount']) - $invoice['amount_paid'], 2); ?></div>
                </div>
            </div>
            <?php if (!empty($invoice['payment_method'])): ?>
                <p style="margin-top: 15px; text-align: center; color: #666;">
                    <strong>Payment Method:</strong> <?php echo strtoupper($invoice['payment_method']); ?>
                </p>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Thank you for choosing SEL Diagnostic Center!</strong></p>
            <p>For any queries, please contact us at +880 1XXX-XXXXXX or info@seldiagnostics.com</p>
            <p>This is a computer-generated invoice and does not require a signature.</p>
            <p style="margin-top: 15px; font-style: italic;">Generated on <?php echo date('d M Y h:i A'); ?></p>
        </div>
    </div>

    <script>
        // Auto-print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>