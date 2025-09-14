<?php
// SMS Service using Textbelt API

class SMSService {
    private $apiUrl = 'https://textbelt.com/text';
    private $apiKey = 'textbelt'; // Free tier key

    /**
     * Send SMS using Textbelt API
     */
    public function sendSMS($phone, $message) {
        try {
            // Validate phone number format (should be E.164 format)
            if (!$this->isValidPhoneNumber($phone)) {
                return [
                    'success' => false,
                    'error' => 'Invalid phone number format. Use E.164 format (+8801XXXXXXXXX)'
                ];
            }

            // Prepare POST data
            $postData = [
                'phone' => $phone,
                'message' => $message,
                'key' => $this->apiKey
            ];

            // Initialize cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/x-www-form-urlencoded',
                'User-Agent: Pathology Lab System/1.0'
            ]);

            // Execute request
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                return [
                    'success' => false,
                    'error' => 'cURL error: ' . $error
                ];
            }

            if ($httpCode !== 200) {
                return [
                    'success' => false,
                    'error' => 'HTTP error: ' . $httpCode
                ];
            }

            // Parse JSON response
            $result = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return [
                    'success' => false,
                    'error' => 'Invalid JSON response'
                ];
            }

            // Log the SMS attempt
            $this->logSMSAttempt($phone, $message, $result);

            return $result;

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Exception: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send test report ready notification
     */
    public function sendReportReadyNotification($patientName, $phone, $invoiceNumber) {
        $message = "Dear {$patientName}, your test report #{$invoiceNumber} is ready for collection at our laboratory. Thank you for choosing our services.";
        return $this->sendSMS($phone, $message);
    }

    /**
     * Send appointment reminder
     */
    public function sendAppointmentReminder($patientName, $phone, $appointmentDate) {
        $message = "Dear {$patientName}, this is a reminder for your appointment on {$appointmentDate} at our pathology laboratory. Please arrive 15 minutes early.";
        return $this->sendSMS($phone, $message);
    }

    /**
     * Send payment reminder
     */
    public function sendPaymentReminder($patientName, $phone, $invoiceNumber, $amount) {
        $message = "Dear {$patientName}, payment of ৳{$amount} for invoice #{$invoiceNumber} is pending. Please visit our laboratory for payment. Thank you.";
        return $this->sendSMS($phone, $message);
    }

    /**
     * Send custom notification
     */
    public function sendCustomNotification($patientName, $phone, $customMessage) {
        $message = "Dear {$patientName}, {$customMessage} - Pathology Laboratory";
        return $this->sendSMS($phone, $message);
    }

    /**
     * Validate phone number format (E.164)
     */
    private function isValidPhoneNumber($phone) {
        // Check if it starts with +880 (Bangladesh country code)
        if (!preg_match('/^\+8801[0-9]{9}$/', $phone)) {
            return false;
        }
        return true;
    }

    /**
     * Format Bangladesh phone number to E.164
     */
    public function formatPhoneNumber($phone) {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Handle different input formats
        if (strlen($phone) === 11 && substr($phone, 0, 2) === '01') {
            // Format: 01XXXXXXXXX -> +8801XXXXXXXXX
            return '+880' . $phone;
        } elseif (strlen($phone) === 13 && substr($phone, 0, 3) === '880') {
            // Format: 8801XXXXXXXXX -> +8801XXXXXXXXX
            return '+' . $phone;
        } elseif (strlen($phone) === 14 && substr($phone, 0, 4) === '+880') {
            // Already in correct format
            return $phone;
        }

        // Return original if can't format
        return $phone;
    }

    /**
     * Log SMS attempts for audit trail
     */
    private function logSMSAttempt($phone, $message, $result) {
        try {
            require_once '../config/database.php';
            $pdo = getDBConnection();

            $stmt = $pdo->prepare("
                INSERT INTO sms_logs (
                    phone_number, message, success, response_data,
                    sent_at, created_at
                ) VALUES (?, ?, ?, ?, NOW(), NOW())
            ");

            $stmt->execute([
                $phone,
                $message,
                $result['success'] ? 1 : 0,
                json_encode($result)
            ]);

        } catch (Exception $e) {
            // Log error but don't fail the SMS operation
            error_log("Failed to log SMS attempt: " . $e->getMessage());
        }
    }

    /**
     * Get SMS statistics
     */
    public function getSMSStats($dateFrom = null, $dateTo = null) {
        try {
            require_once '../config/database.php';
            $pdo = getDBConnection();

            $whereConditions = ['1=1'];
            $params = [];

            if ($dateFrom) {
                $whereConditions[] = "DATE(sent_at) >= ?";
                $params[] = $dateFrom;
            }

            if ($dateTo) {
                $whereConditions[] = "DATE(sent_at) <= ?";
                $params[] = $dateTo;
            }

            $whereClause = implode(' AND ', $whereConditions);

            $stmt = $pdo->prepare("
                SELECT
                    COUNT(*) as total_sent,
                    COUNT(CASE WHEN success = 1 THEN 1 END) as successful,
                    COUNT(CASE WHEN success = 0 THEN 1 END) as failed,
                    COUNT(DISTINCT phone_number) as unique_recipients
                FROM sms_logs
                WHERE $whereClause
            ");

            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            return [
                'total_sent' => 0,
                'successful' => 0,
                'failed' => 0,
                'unique_recipients' => 0
            ];
        }
    }

    /**
     * Get recent SMS logs
     */
    public function getRecentSMSLogs($limit = 50) {
        try {
            require_once '../config/database.php';
            $pdo = getDBConnection();

            $stmt = $pdo->prepare("
                SELECT * FROM sms_logs
                ORDER BY sent_at DESC
                LIMIT ?
            ");

            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Check if phone number has reached daily limit
     */
    public function checkDailyLimit($phone) {
        try {
            require_once '../config/database.php';
            $pdo = getDBConnection();

            $stmt = $pdo->prepare("
                SELECT COUNT(*) as count
                FROM sms_logs
                WHERE phone_number = ?
                AND DATE(sent_at) = CURDATE()
                AND success = 1
            ");

            $stmt->execute([$phone]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Textbelt free tier allows ~1 SMS per day per phone
            return $result['count'] >= 1;

        } catch (Exception $e) {
            return false; // Allow sending if can't check
        }
    }
}

// Global SMS service instance
function getSMSService() {
    static $smsService = null;
    if ($smsService === null) {
        $smsService = new SMSService();
    }
    return $smsService;
}

// Convenience functions
function sendSMS($phone, $message) {
    return getSMSService()->sendSMS($phone, $message);
}

function sendReportReadyNotification($patientName, $phone, $invoiceNumber) {
    return getSMSService()->sendReportReadyNotification($patientName, $phone, $invoiceNumber);
}

function sendPaymentReminder($patientName, $phone, $invoiceNumber, $amount) {
    return getSMSService()->sendPaymentReminder($patientName, $phone, $invoiceNumber, $amount);
}

function formatPhoneNumber($phone) {
    return getSMSService()->formatPhoneNumber($phone);
}
?>