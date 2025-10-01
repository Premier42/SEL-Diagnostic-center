<?php
require_once __DIR__ . '/../bootstrap.php';

echo "<h2>Session Debug</h2>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>Session Name: " . session_name() . "</p>";
echo "<p>Session Save Path: " . session_save_path() . "</p>";

// Set a test value if not exists
if (!isset($_SESSION['test_count'])) {
    $_SESSION['test_count'] = 0;
}
$_SESSION['test_count']++;

echo "<h3>Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<p>Test Counter: " . $_SESSION['test_count'] . "</p>";
echo "<p><a href='test_session.php'>Refresh to increment</a></p>";

// Check if session directory is writable
$session_path = session_save_path() ?: '/tmp';
echo "<p>Session directory writable: " . (is_writable($session_path) ? 'YES' : 'NO') . "</p>";
?>