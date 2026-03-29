<?php 
include 'db.php'; 
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $fullname = $_POST['full_name'];
    $studentid = $_POST['student_id'];
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->rowCount() > 0) {
        $message = "<div class='alert alert-danger'>Username already taken!</div>";
    } else {
        $hashed_password = md5($password);
        $sql = "INSERT INTO users (username, password, full_name, student_id) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$username, $hashed_password, $fullname, $studentid])) {
            header("Location: login.php?registered=1");
            exit;
        } else {
            $message = "<div class='alert alert-danger'>Error registering user.</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
    </script>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
</head>
<body class="d-flex align-items-center py-4 bg-body-tertiary" style="height: 100vh;">
    <main class="form-signin w-100 m-auto" style="max-width: 400px; padding: 15px;">
        <form method="POST">
            <h1 class="h3 mb-3 fw-normal text-center">Create Account</h1>
            <?php echo $message; ?>
            <div class="form-floating mb-2">
                <input type="text" name="full_name" class="form-control" id="floatingName" placeholder="John Doe" required>
                <label for="floatingName">Full Name</label>
            </div>
            <div class="form-floating mb-2">
                <input type="text" name="student_id" class="form-control" id="floatingID" placeholder="010203040" required>
                <label for="floatingID">Student ID</label>
            </div>
            <div class="form-floating mb-2">
                <input type="text" name="username" class="form-control" id="floatingInput" placeholder="Username" required>
                <label for="floatingInput">Username</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                <label for="floatingPassword">Password</label>
            </div>
            <button class="btn btn-primary w-100 py-2" type="submit">Sign Up</button>
            <div class="mt-3 text-center">
                <a href="login.php">Already have an account? Sign in</a>
            </div>
        </form>
    </main>
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