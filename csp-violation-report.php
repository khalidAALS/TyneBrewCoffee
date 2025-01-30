<?php
// CSP Violation Reporting Endpoint
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw input (CSP report)
    $violation = file_get_contents("php://input");

    // Define the log file path
    $logFile = __DIR__ . '/csp-violations.log';

    // Log the CSP violation
    if (!empty($violation)) {
        $logEntry = date("Y-m-d H:i:s") . " - CSP Violation: " . $violation . PHP_EOL;
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    // Respond with a success status
    http_response_code(204); // No Content
    exit();
}
?>
