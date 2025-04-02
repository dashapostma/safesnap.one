<?php
// Include functions file
require_once 'functions.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Secret Sharing</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>Create a Secret</h2>
    <form method="POST">
        <textarea name="secret" required></textarea>
        <br>
        <button type="submit">Generate Link</button>
    </form>

    <?php include 'footer.php'; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['secret'])) {
    $secret = $_POST['secret'];
    $id = insertSecret($secret);
    if ($id) {
        // Redirect to secret link page after inserting the secret
        header("Location: secret_link.php?id=" . urlencode($id));
        exit;
    } else {
        echo "Error: Failed to save the secret.";
    }
}
?>

<?php include 'php/footer.php'; ?>

</body>
</html>
