<?php
include 'includes/config.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['upgrade'])) {
    $stmt = $pdo->prepare("UPDATE d4films_users SET is_premium = 1 WHERE id = ?");
    if ($stmt->execute([$_SESSION['user_id']])) {
        $_SESSION['is_premium'] = 1;
        $success = "The account has been upgraded to Premium!";
    }
}
?>

<div class="container">
    <h1>Premium</h1>
    
    <?php if(isset($success)): ?>
        <div class="success"><?= $success ?></div>
    <?php endif; ?>

    <div class="premium-content">
        <?php if($_SESSION['is_premium']): ?>
            <h2>You already have a Premium account! 🎉</h2>
            <ul class="premium-benefits">
                <li>✅ Access to all premium videos</li>
                <li>✅ No ads</li>
                <li>✅ Early access to what's new</li>
            </ul>
        <?php else: ?>
            <div class="upgrade-box">
                <h2>Become a Premium member</h2>
                <ul class="premium-benefits">
                    <li>🔒 Access to exclusive videos</li>
                    <li>🔒 No ads</li>
                    <li>🔒 Additional features</li>
                </ul>
                <form method="POST">
                    <button type="submit" name="upgrade">Active premium</button>
                </form>
                <small>hehe</small>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>