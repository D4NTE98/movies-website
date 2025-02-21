<?php
include 'includes/config.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<div class="container">
    <h1>todo, go to /movies.php</h1>
</div>

<?php include 'includes/footer.php'; ?>