<?php
session_start(); // Start the session

header('Content-Type: application/json');

// Increase execution time limit and input time to handle large files and slow processes
set_time_limit(0);  // 0 means no time limit (will run indefinitely)
ini_set('max_input_time', 600); // 10 minutes for input processing

$servername = "localhost";
$username = "root";
$password = "";
$database = "cti"; // Your actual database name

try {
    // Establish database connection
    $conn = new mysqli($servername, $username, $password, $database);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Ensure the session is started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $directory = __DIR__ . "/data";
    if (!is_dir($directory)) {
        throw new Exception("Data directory does not exist.");
    }

    $txtFiles = glob($directory . "/@wingscloud - url+ep - nov (64).txt");
    if (empty($txtFiles)) {
        throw new Exception("No .txt files found in the data directory.");
    }

    $totalFiles = count($txtFiles);
    $processedFiles = 0;

    $_SESSION['progress'] = 0; // Initialize progress in session

    // Process each file
    foreach ($txtFiles as $file) {
        $handle = fopen($file, "r");
        if ($handle) {
            $lines = [];
            $batchSize = 1000;
            $counter = 0;

            while (($line = fgets($handle)) !== false) {
                $line = trim($line);
                if (!empty($line)) {
                    $lines[] = $line;
                    $counter++;

                    // Insert in batches
                    if ($counter >= $batchSize) {
                        insertBatch($lines, $conn);
                        $lines = [];
                        $counter = 0;
                    }
                }
            }
            fclose($handle);

            if (!empty($lines)) {
                insertBatch($lines, $conn);
            }
        } else {
            throw new Exception("Error opening file: " . $file);
        }

        // Update progress in session
        $processedFiles++;
        $_SESSION['progress'] = round(($processedFiles / $totalFiles) * 100);

        // Log progress for debugging
        error_log("Progress: " . $_SESSION['progress'] . "%");

        // Simulate delay
        sleep(1);
    }

    // Final success
    $_SESSION['progress'] = 100;
    echo json_encode(['success' => true, 'message' => 'All files processed successfully!']);
    exit;

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}

// Function to insert a batch into the database
function insertBatch($lines, $conn) {
    $insertQuery = "INSERT INTO txt_data (content) VALUES ";
    $values = [];
    foreach ($lines as $line) {
        $values[] = "('" . $conn->real_escape_string($line) . "')";
    }
    $insertQuery .= implode(",", $values);
    if (!$conn->query($insertQuery)) {
        throw new Exception("Error executing batch insert: " . $conn->error);
    }
}
