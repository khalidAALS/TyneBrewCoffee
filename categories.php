<?php
// Database connection
require_once 'db.php';
include 'CSP.php';

$db = new Database();
$conn = $db->connect();

// Fetch category images and details
$query = "SELECT category, image_url, description FROM TblProducts GROUP BY category";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your CSS -->
</head>
<body>
    <header>
        <h1>Tyne Brew Coffee - Categories</h1>
        <?php if (isset($_SESSION['username'])): ?>
    <div class="welcome-message">
        <p>Hello, welcome back <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</p>
    </div>
<?php endif; ?>

    </header>
    <main class="category-container">
        <?php
        // Dynamically display categories
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="category-item">';
                echo '<img src="' . $row['image_url'] . '" alt="' . $row['category'] . '">';
                echo '<h2>' . $row['category'] . '</h2>';
                echo '<p>' . $row['description'] . '</p>';
                echo '<a href="category.php?category=' . urlencode($row['category']) . '" class="btn">Explore</a>';
                echo '</div>';
            }
        } else {
            echo '<p>No categories found.</p>';
        }
        ?>
    </main>
</body>
</html>
