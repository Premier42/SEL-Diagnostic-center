<?php

namespace App\Controllers;

class SmsController extends BaseController
{
    public function index()
    {
        $this->requireAuth();

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            include __DIR__ . '/../../views/errors/403.php';
            return;
        }

        $db = getDB();

        try {
            // Get recent SMS logs
            $stmt = $db->query("
                SELECT *
                FROM sms_logs
                ORDER BY created_at DESC
                LIMIT 50
            ");
            $sms_logs = $stmt->fetchAll();

            // Get SMS statistics
            $statsQuery = "
                SELECT
                    COUNT(*) as total_sent,
                    COUNT(CASE WHEN status = 'sent' THEN 1 END) as successful,
                    COUNT(CASE WHEN status = 'failed' THEN 1 END) as failed,
                    COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today_count
                FROM sms_logs
            ";

            $stmt = $db->query($statsQuery);
            $stats = $stmt->fetch();

            // Get SMS templates
            $stmt = $db->query("
                SELECT *
                FROM sms_templates
                WHERE is_active = 1
                ORDER BY name
            ");
            $templates = $stmt->fetchAll();

            include __DIR__ . '/../../views/sms/index.php';

        } catch (Exception $e) {
            log_activity("SMS dashboard error: " . $e->getMessage(), 'error');

            $error_message = config('app.debug') ? $e->getMessage() : 'Unable to load SMS dashboard.';
            include __DIR__ . '/../../views/errors/500.php';
        }
    }

    public function send()
    {
        $this->requireAuth();

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $this->json(['error' => 'Insufficient permissions'], 403);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/sms');
            return;
        }

        // Validate CSRF token
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            $this->json(['error' => 'Invalid security token'], 400);
            return;
        }

        $db = getDB();

        try {
            // Validate required fields
            $errors = $this->validate($_POST, [
                'phone' => 'required',
                'message' => 'required'
            ]);

            if (!empty($errors)) {
                $this->json(['error' => 'Validation failed', 'details' => $errors], 400);
                return;
            }

            $phone = $this->sanitize($_POST['phone']);
            $message = $this->sanitize($_POST['message']);

            // Send SMS using TextBelt API (free tier)
            $result = $this->sendSMS($phone, $message);

            // Log SMS
            $stmt = $db->prepare("
                INSERT INTO sms_logs (
                    recipient_phone, message, status, provider_response, sent_by, sent_at
                ) VALUES (?, ?, ?, ?, ?, NOW())
            ");

            $stmt->execute([
                $phone,
                $message,
                $result['status'],
                json_encode($result['response']),
                $_SESSION['user_id']
            ]);

            log_activity("Sent SMS to: " . $phone, 'info', [
                'phone' => $phone,
                'status' => $result['status'],
                'user_id' => $_SESSION['user_id']
            ]);

            $this->json(['success' => true, 'message' => 'SMS sent successfully']);

        } catch (Exception $e) {
            log_activity("SMS sending error: " . $e->getMessage(), 'error');
            $this->json(['error' => 'Failed to send SMS'], 500);
        }
    }

    private function sendSMS($phone, $message)
    {
        try {
            // Format phone number for Bangladesh
            $phone = preg_replace('/[^0-9]/', '', $phone);

            // SMS.NET.BD accepts: 880XXXXXXXXXX or 01XXXXXXXXX
            if (substr($phone, 0, 2) === '88') {
                // Already has 880 prefix
                $phone = $phone;
            } elseif (substr($phone, 0, 1) === '0') {
                // Has leading 0, keep as is (01XXXXXXXXX)
                $phone = $phone;
            } else {
                // Missing both, add 880
                $phone = '880' . $phone;
            }

            // Get API key from environment
            $apiKey = $_ENV['SMS_API_KEY'] ?? 'your_api_key_here';

            // Use SMS.NET.BD API (free credits on signup, no card required)
            $url = 'https://api.sms.net.bd/sendsms';
            $data = [
                'api_key' => $apiKey,
                'msg' => $message,
                'to' => $phone
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($response === false || $httpCode !== 200) {
                return [
                    'status' => 'failed',
                    'response' => ['error' => 'HTTP request failed', 'http_code' => $httpCode]
                ];
            }

            $result = json_decode($response, true);

            // SMS.NET.BD returns JSON with status
            return [
                'status' => ($result['status'] ?? '') === 'success' ? 'sent' : 'failed',
                'response' => $result
            ];

        } catch (Exception $e) {
            return [
                'status' => 'failed',
                'response' => ['error' => $e->getMessage()]
            ];
        }
    }
}