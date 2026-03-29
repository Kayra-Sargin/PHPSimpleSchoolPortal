<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mathavuz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
    </script>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
</head>
<body class="bg-body-tertiary"> 
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">ITU Mathavuz</a>
        <div>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php" class="btn btn-light btn-sm">Dashboard</a>
                <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-light btn-sm">Login</a>
            <?php endif; ?>
            <button class="btn btn-outline-light btn-sm ms-2" id="themeToggle">
                🌗 Theme
            </button>
        </div>
    </div>
</nav>
<div class="container">
    <h2 class="mb-3">Announcements</h2>
    <div class="list-group">
        <?php
        $stmt = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC");
        while ($row = $stmt->fetch()):
            $bgClass = $row['is_urgent'] ? 'list-group-item-danger' : 'list-group-item-light';
            $badge = $row['is_urgent'] ? '<span class="badge bg-danger">URGENT</span>' : '';
        ?>
            <div class="list-group-item <?php echo $bgClass; ?>">
                <div class="d-flex w-100 justify-content-between">
                    <h3 class="h5 mb-1"><?php echo htmlspecialchars($row['title']); ?> <?php echo $badge; ?></h3>
                    <small><?php echo date('M d, Y', strtotime($row['created_at'])); ?></small>
                </div>
                <p class="mb-1"><?php echo htmlspecialchars($row['content']); ?></p>
            </div>
        <?php endwhile; ?>
    </div>
</div>
<script>
    const toggleBtn = document.getElementById('themeToggle');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        });
    }
</script>
</body>
</html>