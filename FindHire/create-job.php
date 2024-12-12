<?php
require 'db.php';
require 'header.php'; 

// Check if the user is HR
if ($_SESSION['role'] !== 'hr') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hr_id = $_SESSION['user_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    
    $title = htmlspecialchars($title);
    $description = htmlspecialchars($description);

    try {
        // Insert the job post into the database
        $stmt = $conn->prepare("INSERT INTO job_posts (hr_id, title, description) VALUES (?, ?, ?)");
        $stmt->execute([$hr_id, $title, $description]);

        // Set success message
        $_SESSION['message'] = "Job post created successfully!";
        $_SESSION['message_type'] = "success";

        
        header("Location: hr-dashboard.php?job_created=1");
        exit;
    } catch (PDOException $e) {
        // Handle any database errors
        $_SESSION['message'] = "Error: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
}
?>

<div class="container mt-5">
    <h2>Create Job Post</h2>
    
    <?php

    if (isset($_SESSION['message'])) {
        $message_type = $_SESSION['message_type'] ?? 'info';
        echo "<div class='alert alert-$message_type' role='alert'>" . $_SESSION['message'] . "</div>";
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
    ?>

    <form action="create-job.php" method="POST">
        <div class="mb-3">
            <label for="title" class="form-label">Job Title</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Job Description</label>
            <textarea name="description" id="description" class="form-control" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Create Job Post</button>
    </form>
</div>

<?php

?>
