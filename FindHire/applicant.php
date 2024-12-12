<?php
require 'db.php';
require 'header.php'; 

if ($_SESSION['role'] !== 'applicant') {
    header("Location: login.php");
    exit;
}

// Fetch available job posts
$stmt = $conn->prepare("
    SELECT job_posts.*, users.username AS hr_name 
    FROM job_posts 
    JOIN users ON job_posts.hr_id = users.id 
    ORDER BY job_posts.created_at DESC
");
$stmt->execute();
$job_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h2 class="text-center text-white mb-4">Available Jobs</h2>
    <div class="list-group">
        <?php if (empty($job_posts)): ?>
            <p class="text-muted">No job posts available at the moment.</p>
        <?php else: ?>
            <?php foreach ($job_posts as $job): ?>
                <div class="list-group-item bg-success text-white mb-3 border-0 rounded-3 shadow-sm">
                    <h5 class="font-weight-bold"><?php echo htmlspecialchars($job['title']); ?></h5>
                    <p class="font-italic"><?php echo htmlspecialchars($job['description']); ?></p>
                    <small class="d-block text-muted">Posted by: <?php echo htmlspecialchars($job['hr_name']); ?></small>
                    <div class="mt-3">
                        <a href="apply.php?job_id=<?php echo $job['id']; ?>" class="btn btn-light btn-sm mr-2">Apply</a>
                        <a href="send-message.php?receiver_id=<?php echo $job['hr_id']; ?>&job_post_id=<?php echo $job['id']; ?>" class="btn btn-warning btn-sm">Message HR</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
?>
