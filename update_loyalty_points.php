<?php
include 'session_manager.php'; // Ensure the session is started
include 'CSP.php'; // Content Security Policy header
require_once 'db.php'; // Database connection file

// starts the Database class and establish a connection
$db = new Database();
$conn = $db->connect();

// gets all users who have a loyalty number and excludes admins
$query = "
    SELECT id, loyalty_number, loyalty_points
    FROM TblUsers
    WHERE loyalty_number != '' AND role != 'admin'
";
$stmt = $conn->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// goes through each user to calculate and update loyalty points
foreach ($users as $user) {
    // gets all completed orders for the user
    $query = "
        SELECT SUM(o.total_price) AS total_spent
        FROM TblOrders o
        WHERE o.user_id = :user_id AND o.status = 'completed'
    ";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user['id']);
    $stmt->execute();
    $orderData = $stmt->fetch(PDO::FETCH_ASSOC);

    // calculates loyalty points: 1 point for every £4 spent
    if ($orderData['total_spent']) {
        $loyaltyPoints = floor($orderData['total_spent'] / 4); // 1 point for every £4
    } else {
        $loyaltyPoints = 0;
    }

    // updates the user's loyalty points in the database
    $updateQuery = "
        UPDATE TblUsers
        SET loyalty_points = :loyalty_points
        WHERE id = :user_id
    ";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bindParam(':loyalty_points', $loyaltyPoints, PDO::PARAM_INT);
    $updateStmt->bindParam(':user_id', $user['id'], PDO::PARAM_INT);
    $updateStmt->execute();

    echo "Updated loyalty points for user ID: " . $user['id'] . " to " . $loyaltyPoints . " points.<br>";
}
?>
