<?php 
include 'db.php';
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit; 
}
$user_id = $_SESSION['user_id'];
$stmtUser = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmtUser->execute([$user_id]);
$student = $stmtUser->fetch();
$stmtGrades = $pdo->prepare("SELECT * FROM grades WHERE user_id = ?");
$stmtGrades->execute([$user_id]);
$courses = $stmtGrades->fetchAll();
$stmtObj = $pdo->prepare("SELECT * FROM objections WHERE user_id = ? ORDER BY submitted_at DESC");
$stmtObj->execute([$user_id]);
$objections = $stmtObj->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
    </script>
    <style>
        .card-header { 
            font-weight: bold; 
            color: white; 
        }
    </style>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
</head>
<body class="bg-body-tertiary"> 
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">ITU Mathavuz</a>
        <div class="d-flex align-items-center">
            <span class="navbar-text me-3 text-white d-none d-md-block">
                Hello, <?php echo htmlspecialchars($student['full_name']); ?>
            </span>
            <a href="logout.php" class="btn btn-danger btn-sm">Sign Out</a>
            <button class="btn btn-outline-light btn-sm ms-2" id="themeToggle">
                🌗 Theme
            </button>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary">Student Profile</div>
                <div class="card-body">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($student['full_name']); ?></p>
                    <p><strong>Student ID:</strong> <?php echo htmlspecialchars($student['student_id']); ?></p>
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($student['username']); ?></p>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-header bg-secondary">My Objections History</div>
                <div class="card-body p-0">
                    <?php if (count($objections) > 0): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($objections as $obj): ?>
                                <li class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <strong class="mb-1 text-primary">
                                            <?php echo htmlspecialchars(!empty($obj['course_name']) ? $obj['course_name'] : 'General'); ?></strong>
                                        <small class="text-muted">
                                            <?php echo date('M d', strtotime($obj['submitted_at'])); ?>
                                        </small>
                                    </div>
                                    <p class="mb-1 small"><?php echo htmlspecialchars($obj['message']); ?></p>
                                    <span class="badge bg-warning text-dark" style="font-size: 0.7em;">Pending Review</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="p-3 text-muted small">No objections filed.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary">Enrolled Courses & Grades</div>
                <div class="card-body">
                    <?php if (count($courses) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover text-center align-middle">
                                <thead>
                                    <tr>
                                        <th class="text-start">Course Name</th>
                                        <th>Midterm</th>
                                        <th>Quiz</th>
                                        <th>Final</th>
                                        <th>Overall</th>
                                        <th>Attendance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td class="text-start fw-bold">
                                            <?php echo htmlspecialchars($course['course_name']); ?>
                                        </td>
                                        <td><?php echo $course['midterm']; ?></td>
                                        <td><?php echo $course['quiz']; ?></td>
                                        <td><?php echo $course['final']; ?></td>
                                        <td class="fw-bold text-primary">
                                            <?php echo $course['overall']; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $att = $course['attendance_percent'];
                                            $badgeClass = ($att < 70) ? 'bg-danger' : 'bg-success';
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?>">
                                                %<?php echo $att; ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            You are not enrolled in any courses yet.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mt-3 text-end">
                <a href="objection.php" class="btn btn-warning btn-lg shadow">
                    Submit New Grade Objection
                </a>
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