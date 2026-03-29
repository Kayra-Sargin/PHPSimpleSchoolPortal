<?php 
include 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['post_announcement'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $urgent = isset($_POST['urgent']) ? 1 : 0;

    if (!empty($title) && !empty($content)) {
        $stmt = $pdo->prepare("INSERT INTO announcements (title, content, is_urgent) VALUES (?, ?, ?)");
        $stmt->execute([$title, $content, $urgent]);
        $message = "<div class='alert alert-success'>Announcement posted successfully!</div>";
    }
}
if (isset($_GET['delete_obj'])) {
    $id = $_GET['delete_obj'];
    $pdo->prepare("DELETE FROM objections WHERE id = ?")->execute([$id]);
    header("Location: admin_dashboard.php");
    exit;
}
$objections = $pdo->query("SELECT o.*, u.full_name, u.student_id FROM objections o JOIN users u ON o.user_id = u.id ORDER BY o.submitted_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
    </script>
</head>
<body class="bg-body-tertiary">
<nav class="navbar navbar-dark bg-danger mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">Mathavuz ADMIN Panel</a>
        <div>
            <span class="navbar-text me-3 text-white">Signed in as Instructor</span>
            <button class="btn btn-outline-light btn-sm me-2" id="themeToggle">🌗 Theme</button>
            <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
        </div>
    </div>
</nav>
<div class="container">
    <?php echo $message; ?>
    <div class="row">
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Post New Announcement</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="post_announcement" value="1">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required placeholder="e.g. Midterm Results">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Content</label>
                            <textarea name="content" class="form-control" rows="4" required placeholder="Details here..."></textarea>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="urgent" id="urgentCheck">
                            <label class="form-check-label text-danger fw-bold" for="urgentCheck">
                                Mark as URGENT
                            </label>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">Publish Announcement</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Student Grade Objections</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-secondary">
                                <tr>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Message</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($objections) > 0): ?>
                                    <?php foreach($objections as $obj): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($obj['full_name']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($obj['student_id']); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-dark">
                                                <?php echo htmlspecialchars(!empty($obj['course_name']) ? $obj['course_name'] : '-'); ?>
                                            </span>
                                        </td>
                                        <td><small><?php echo htmlspecialchars($obj['message']); ?></small></td>
                                        <td>
                                            <a href="admin_dashboard.php?delete_obj=<?php echo $obj['id']; ?>" 
                                               class="btn btn-outline-danger btn-sm"
                                               onclick="return confirm('Mark this objection as resolved (delete)?');">
                                                Resolve
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center p-3">No pending objections.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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