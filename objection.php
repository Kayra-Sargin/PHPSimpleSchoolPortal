<?php 
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
$message = "";
$messageType = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_selected = trim($_POST['course_name']);
    $objection_text = trim($_POST['objection_text']);
    if (empty($course_selected) || empty($objection_text)) {
        $message = "Please select a course and write your objection.";
        $messageType = "danger";
    } else {
        try {
            $sql = "INSERT INTO objections (user_id, course_name, message) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user_id, $course_selected, $objection_text]);

            $message = "Objection submitted successfully! The administration will review it.";
            $messageType = "success";
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
            $messageType = "danger";
        }
    }
}
$stmt = $pdo->prepare("SELECT course_name FROM grades WHERE user_id = ?");
$stmt->execute([$user_id]);
$courses = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Objection</title>
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
        <a class="navbar-brand btn btn-outline-secondary" href="dashboard.php"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
        <button class="btn btn-outline-light btn-sm ms-2" id="themeToggle">
            🌗 Theme
        </button>
    </div>
</nav>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">Submit Grade Objection</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                            <?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <p class="text-muted small">
                        Please select the course and detail your reason for the objection. 
                        Your request will be logged for review.
                    </p>
                    <form method="POST" action="objection.php">
                        <div class="mb-3">
                            <label for="courseSelect" class="form-label fw-bold">Select Course</label>
                            <select class="form-select" id="courseSelect" name="course_name" required>
                                <option value="" selected disabled>Choose a Course</option>
                                <?php foreach($courses as $course): ?>
                                    <option value="<?php echo htmlspecialchars($course); ?>">
                                        <?php echo htmlspecialchars($course); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Only courses you are enrolled in appear here.</div>
                        </div>
                        <div class="mb-3">
                            <label for="objectionText" class="form-label fw-bold">Reason for Objection</label>
                            <textarea class="form-control" id="objectionText" name="objection_text" rows="5" 
                                      placeholder="Explain why you think the grade is incorrect..." required></textarea>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Submit Objection</button>
                            <a href="dashboard.php" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
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