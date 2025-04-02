<?php
require_once 'functions.php';

// Check if a POST request is made to delete the secret
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    deleteSecretById($id);
    exit; // Stop further execution
}

// Get the secret if an ID is provided
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$secret = getSecretById($id);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Secret</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>View Your Secret</h2>

    <?php if ($secret): ?>
        <p><button id="revealButton" onclick="revealSecret()">Reveal Secret</button></p>
        <div id="secretDiv" style="display:none;">
            <p>Secret: <?php echo htmlspecialchars($secret['encrypted_text']); ?></p>
            <p><a href="index.php">Create a new secret</a></p>
        </div>
    <?php else: ?>
        <p>Secret not found or already viewed.</p>
        <p><a href="index.php">Create a new secret</a></p>
    <?php endif; ?>

    <script>
        function revealSecret() {
            // Hide the button
            document.getElementById("revealButton").style.display = "none";

            // Show the secret div
            document.getElementById("secretDiv").style.display = "block";

            // Send an AJAX request to delete the secret after revealing it
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "view_secret.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send("id=<?php echo $id; ?>");
        }
    </script>

<?php include 'php/footer.php'; ?>
</body>
</html>
