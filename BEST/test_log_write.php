<?php
$logFile = __DIR__ . '/debug_log.txt';
echo "Trying to write to: $logFile\n";
$result = file_put_contents($logFile, "Test log entry " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
if ($result === false) {
    echo "Failed to write to file.\n";
    print_r(error_get_last());
} else {
    echo "Successfully wrote $result bytes.\n";
}
?>
