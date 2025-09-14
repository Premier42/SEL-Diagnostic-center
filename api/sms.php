<?php
// SMS API endpoint

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../utils/sms-service.php';
require_once '../utils/audit-logger.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    $smsService = new SMSService();

    switch ($method) {
        case 'GET':
            handleGetSMS($smsService);
            break;
        case 'POST':
            handleSendSMS($smsService, $input);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function handleGetSMS($smsService) {
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'stats':
            $stats = $smsService->getSMSStats(
                $_GET['date_from'] ?? null,
                $_GET['date_to'] ?? null
            );
            echo json_encode($stats);
            break;

        case 'logs':
            $limit = (int)($_GET['limit'] ?? 50);
            $logs = $smsService->getRecentSMSLogs($limit);
            echo json_encode($logs);
            break;

        case 'check_limit':
            $phone = $_GET['phone'] ?? '';
            if (!$phone) {
                http_response_code(400);
                echo json_encode(['error' => 'Phone number is required']);
                return;
            }

            $hasReachedLimit = $smsService->checkDailyLimit($phone);
            echo json_encode(['has_reached_limit' => $hasReachedLimit]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
    }
}

function handleSendSMS($smsService, $input) {
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        return;
    }

    $type = $input['type'] ?? 'custom';
    $phone = $input['phone'] ?? '';

    if (!$phone) {
        http_response_code(400);
        echo json_encode(['error' => 'Phone number is required']);
        return;
    }

    // Format phone number to E.164
    $formattedPhone = $smsService->formatPhoneNumber($phone);

    // Check daily limit
    if ($smsService->checkDailyLimit($formattedPhone)) {
        http_response_code(429);
        echo json_encode([
            'error' => 'Daily SMS limit reached for this phone number',
            'code' => 'DAILY_LIMIT_EXCEEDED'
        ]);
        return;
    }

    $result = null;

    switch ($type) {
        case 'report_ready':
            $patientName = $input['patient_name'] ?? '';
            $invoiceNumber = $input['invoice_number'] ?? '';

            if (!$patientName || !$invoiceNumber) {
                http_response_code(400);
                echo json_encode(['error' => 'Patient name and invoice number are required']);
                return;
            }

            $result = $smsService->sendReportReadyNotification($patientName, $formattedPhone, $invoiceNumber);
            break;

        case 'payment_reminder':
            $patientName = $input['patient_name'] ?? '';
            $invoiceNumber = $input['invoice_number'] ?? '';
            $amount = $input['amount'] ?? '';

            if (!$patientName || !$invoiceNumber || !$amount) {
                http_response_code(400);
                echo json_encode(['error' => 'Patient name, invoice number, and amount are required']);
                return;
            }

            $result = $smsService->sendPaymentReminder($patientName, $formattedPhone, $invoiceNumber, $amount);
            break;

        case 'appointment_reminder':
            $patientName = $input['patient_name'] ?? '';
            $appointmentDate = $input['appointment_date'] ?? '';

            if (!$patientName || !$appointmentDate) {
                http_response_code(400);
                echo json_encode(['error' => 'Patient name and appointment date are required']);
                return;
            }

            $result = $smsService->sendAppointmentReminder($patientName, $formattedPhone, $appointmentDate);
            break;

        case 'custom':
            $message = $input['message'] ?? '';

            if (!$message) {
                http_response_code(400);
                echo json_encode(['error' => 'Message is required']);
                return;
            }

            $result = $smsService->sendSMS($formattedPhone, $message);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid SMS type']);
            return;
    }

    // Log the SMS attempt in audit logs
    if ($result) {
        $auditLogger = getAuditLogger();
        $auditLogger->log('SMS_SENT', 'sms_logs', 0, null, [
            'type' => $type,
            'phone' => $formattedPhone,
            'success' => $result['success'] ?? false,
            'textbelt_response' => $result
        ]);
    }

    echo json_encode($result);
}
?>