<?php
include 'session_manager.php';  // Ensure session is started
include 'CSP.php';
require_once 'db.php';

// Instantiate the Database class and establish a connection
$db = new Database();
$conn = $db->connect();

// Delete the existing loyalty numbers for all users (excluding admin)
$stmt = $conn->prepare("UPDATE TblUsers SET loyalty_number = NULL WHERE role != 'admin'");
$stmt->execute();  // This clears the loyalty numbers

// Prepare the update query for non-admin users with no loyalty number
$stmt = $conn->prepare("SELECT id FROM TblUsers WHERE role != 'admin'");
$stmt->execute();  // Get all non-admin users
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Iterate through users and update each one with a unique loyalty number
foreach ($users as $user) {
    // Generate a unique 4-digit loyalty number for each user
    $loyalty_number = rand(1000, 9999);

    // Ensure that the generated loyalty number is unique by checking if it already exists
    while (checkLoyaltyNumberExists($loyalty_number, $conn)) {
        // If the loyalty number already exists, generate a new one
        $loyalty_number = rand(1000, 9999);
    }

    // Update query to set the loyalty number
    $update_stmt = $conn->prepare("UPDATE TblUsers SET loyalty_number = :loyalty_number WHERE id = :id");
    $update_stmt->bindParam(':loyalty_number', $loyalty_number, PDO::PARAM_INT);
    $update_stmt->bindParam(':id', $user['id'], PDO::PARAM_INT);

    // Execute the update query for each user
    $update_stmt->execute();
}

echo "Successfully updated loyalty numbers for " . count($users) . " users.";

// Function to check if a loyalty number already exists in the database
function checkLoyaltyNumberExists($loyalty_number, $conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM TblUsers WHERE loyalty_number = :loyalty_number");
    $stmt->bindParam(':loyalty_number', $loyalty_number, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchColumn() > 0;
}
?>

