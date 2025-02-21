<?php
include 'includes/config.php';

// Sprawdź uprawnienia admina
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== 1) {
    header("Location: ../login.php");
    exit();
}

// Zarządzanie użytkownikami
if (isset($_POST['delete_user'])) {
    $userId = $_POST['user_id'];
    $stmt = $pdo->prepare("DELETE FROM d4films_users WHERE id = ?");
    $stmt->execute([$userId]);
}

if (isset($_POST['toggle_admin'])) {
    $userId = $_POST['user_id'];
    $stmt = $pdo->prepare("UPDATE d4films_users SET is_admin = NOT is_admin WHERE id = ?");
    $stmt->execute([$userId]);
}

// Zarządzanie filmami
if (isset($_POST['delete_movie'])) {
    $movieId = $_POST['movie_id'];
    $stmt = $pdo->prepare("DELETE FROM d4films_movies WHERE id = ?");
    $stmt->execute([$movieId]);
}

if (isset($_POST['add_movie'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $file_path = $_POST['file_path'];
    $is_premium = isset($_POST['is_premium']) ? 1 : 0;

    $stmt = $pdo->prepare("INSERT INTO d4films_movies (title, description, file_path, is_premium) VALUES (?, ?, ?, ?)");
    $stmt->execute([$title, $description, $file_path, $is_premium]);
}

// Pobierz dane
$users = $pdo->query("SELECT * FROM d4films_users")->fetchAll();
$movies = $pdo->query("SELECT * FROM d4films_movies")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoviesSite</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container admin-panel">
        <h1>Admin Panel <span class="admin-badge">ADMIN</span></h1>
        
        <section class="users-management">
            <h2>Manage users</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Premium</th>
                        <th>Admin</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= $user['is_premium'] ? '✅' : '❌' ?></td>
                        <td><?= $user['is_admin'] ? '✅' : '❌' ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <button type="submit" name="delete_user" class="danger">Delete</button>
                                <button type="submit" name="toggle_admin">Toggle admin</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="movies-management">
            <h2>Manage movies</h2>
            <div class="add-movie-form">
                <h3>Add new movie</h3>
                <form method="POST">
                    <input type="text" name="title" placeholder="Title" required>
                    <textarea name="description" placeholder="Description"></textarea>
                    <input type="text" name="file_path" placeholder="Video File Name with .mp4" required>
                    <label>
                        <input type="checkbox" name="is_premium"> is Premium Movie?
                    </label>
                    <button type="submit" name="add_movie">Add</button>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Premium</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movies as $movie): ?>
                    <tr>
                        <td><?= $movie['id'] ?></td>
                        <td><?= htmlspecialchars($movie['title']) ?></td>
                        <td><?= $movie['is_premium'] ? '✅' : '❌' ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="movie_id" value="<?= $movie['id'] ?>">
                                <button type="submit" name="delete_movie" class="danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>