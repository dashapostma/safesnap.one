<?php
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$secretLink = "http://" . $_SERVER['HTTP_HOST'] . "/safesnap.one/beta/view_secret.php?id=$id";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Secret Link</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>Your Secret Link</h2>
    <input type="text" value="<?php echo htmlspecialchars($secretLink); ?>" id="secretLink" readonly>
    <button onclick="copyLink()">Copy</button>
    <p><a href="index.php">Create a new secret</a></p>

    <script>
    function copyLink() {
        var copyText = document.getElementById("secretLink");
        copyText.select();
        document.execCommand("copy");
        alert("Link copied to clipboard!");
    }
    </script>

<?php include 'php/footer.php'; ?>
</body>
</html>
