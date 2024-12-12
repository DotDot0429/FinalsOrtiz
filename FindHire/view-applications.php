<?php
require 'db.php';
require 'header.php'; 

if ($_SESSION['role'] !== 'hr') {
    header("Location: login.php");
    exit;
}

// Sanitize job_id from the URL
$job_id = filter_input(INPUT_GET, 'job_id', FILTER_SANITIZE_NUMBER_INT);

if (!$job_id) {
    echo "<div class='container mt-5'><p class='text-danger'>Job ID not provided or invalid.</p></div>";
    require 'footer.php';
    exit;
}

// Fetch job post details
$stmt = $conn->prepare("SELECT * FROM job_posts WHERE id = ? AND hr_id = ?");
$stmt->execute([$job_id, $_SESSION['user_id']]);
$job = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    echo "<div class='container mt-5'><p class='text-danger'>Job post not found or unauthorized access.</p></div>";
    require 'footer.php';
    exit;
}

$stmt = $conn->prepare("
    SELECT applications.*, users.username AS applicant_name 
    FROM applications 
    JOIN users ON applications.applicant_id = users.id 
    WHERE applications.job_post_id = ?
");
$stmt->execute([$job_id]);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h2>Applications for "<?php echo htmlspecialchars($job['title']); ?>"</h2>
    <div class="list-group mt-3">
        <?php if (empty($applications)): ?>
            <p class="text-muted">No applications received yet.</p>
        <?php else: ?>
            <?php foreach ($applications as $app): ?>
                <div class="list-group-item">
                    <h5><?php echo htmlspecialchars($app['applicant_name']); ?></h5>
                    <p><?php echo htmlspecialchars($app['message']); ?></p>
                    <small>Resume: <a href="<?php echo htmlspecialchars($app['resume']); ?>" target="_blank">View Resume</a></small>
                    <small class="d-block">Status: <?php echo ucfirst(htmlspecialchars($app['status'])); ?></small>
                    <?php if ($app['status'] === 'pending'): ?>
                        <div class="mt-2">
                            <a href="process-application.php?application_id=<?php echo $app['id']; ?>&action=accept" class="btn btn-sm btn-success">Accept</a>
                            <a href="process-application.php?application_id=<?php echo $app['id']; ?>&action=reject" class="btn btn-sm btn-danger">Reject</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
?>
