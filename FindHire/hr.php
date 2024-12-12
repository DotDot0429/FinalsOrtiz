<?php
require 'db.php';
require 'header.php';

if (!isset($_GET['job_id'])) {
    echo "Job ID not provided. Check the URL for 'job_id'.";
    exit;
}


$job_id = $_GET['job_id'];


$stmt = $conn->prepare("SELECT * FROM job_posts WHERE id = ?");
$stmt->execute([$job_id]);
$job = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    echo "Job post not found.";
    exit;
}


$stmt = $conn->prepare("SELECT * FROM applications WHERE job_post_id = ?");
$stmt->execute([$job_id]);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id'])) {
    $application_id = $_POST['application_id'];

    // Update application status to 'hired'
    $update_stmt = $conn->prepare("UPDATE applications SET status = 'hired' WHERE id = ?");
    $update_stmt->execute([$application_id]);

    // Set success message
    $_SESSION['message'] = "Application has been accepted!";
    $_SESSION['message_type'] = "success";

    header("Location: hr-dashboard.php?job_id=$job_id");
    exit;
}
?>

<div class="container mt-5">
    <h2>Job Post: <?php echo htmlspecialchars($job['title']); ?></h2>
    <p><?php echo htmlspecialchars($job['description']); ?></p>

    <h3>Applications</h3>

    <?php if (empty($applications)): ?>
        <p>No applications for this job post yet.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Message</th>
                    <th>Resume</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applications as $application): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($application['applicant_id']); ?></td>
                        <td><?php echo htmlspecialchars($application['message']); ?></td>
                        <td><a href="<?php echo htmlspecialchars($application['resume']); ?>" target="_blank">View Resume</a></td>
                        <td>
                            <?php echo htmlspecialchars($application['status']); ?>
                        </td>
                        <td>
                            <?php if ($application['status'] !== 'hired'): ?>
                                <form action="hr-dashboard.php?job_id=<?php echo $job_id; ?>" method="POST">
                                    <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                                    <button type="submit" class="btn btn-success">Accept Application</button>
                                </form>
                            <?php else: ?>
                                <span class="text-success">Hired</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php
?>