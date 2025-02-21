<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoviesSite</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<nav class="navbar">
    <div class="container">
        <div class="left-group">
            <a href="index.php" class="logo">MoviesSite</a>
            <div class="nav-links">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php if($_SESSION['is_admin']): ?>
                        <a href="admin.php">Admin</a>
                    <?php endif; ?>
                    <a href="premium.php">Premium</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
        <?php if(isset($_SESSION['user_id'])): ?>
            <form action="search.php" method="GET" class="search-form">
                <input type="text" name="query" placeholder="Search...">
                <button type="submit">Search</button>
            </form>
        <?php endif; ?>
    </div>
</nav>