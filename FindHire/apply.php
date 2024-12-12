<?php
require 'db.php';
require 'header.php'; 

if ($_SESSION['role'] !== 'applicant') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_id = $_POST['job_id'];
    $applicant_id = $_SESSION['user_id'];
    $message = trim($_POST['message']);
    $resume = $_FILES['resume'];

    // Ensure the uploads folder exists
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);  
    }

    
    $allowed_file_types = ['application/pdf'];
    if (!in_array($resume['type'], $allowed_file_types)) {
        echo "Invalid file type. Please upload a PDF file.";
        exit;
    }

   
    $resumePath = $upload_dir . uniqid() . '-' . basename($resume['name']);

    
    if (move_uploaded_file($resume['tmp_name'], $resumePath)) {
        // Insert application into the database
        try {
            $stmt = $conn->prepare("INSERT INTO applications (job_post_id, applicant_id, resume, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$job_id, $applicant_id, $resumePath, $message]);

            header("Location: applicant-dashboard.php?success=1");
            exit;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

$job_id = $_GET['job_id'];
?>

<div class="container mt-5">
    <h2>Apply</h2>
    <form action="apply.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($job_id); ?>">
        <div class="mb-3">
            <label for="message" class="form-label">Why do you want this job?</label>
            <textarea name="message" id="message" class="form-control" rows="5" required></textarea>
        </div>
        <div class="mb-3">
            <label for="resume" class="form-label">Upload Resume</label>
            <input type="file" name="resume" id="resume" class="form-control" accept=".pdf" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<?php
?>