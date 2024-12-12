<?php
require 'db.php';
require 'header.php'; // Include the header

if ($_SESSION['role'] !== 'hr') {
    header("Location: login.php");
    exit;
}

$job_id = filter_input(INPUT_GET, 'job_id', FILTER_SANITIZE_NUMBER_INT); // Sanitize job_id

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
    SELECT messages.*, users.username AS applicant_name, users.id AS applicant_id
    FROM messages 
    JOIN users ON messages.sender_id = users.id 
    WHERE messages.job_post_id = ?
    ORDER BY messages.sent_at DESC
");
$stmt->execute([$job_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply'])) {
    $replyContent = trim($_POST['reply']);
    $messageId = $_POST['message_id'];
    $applicantId = $_POST['applicant_id'];


    $replyContent = htmlspecialchars($replyContent, ENT_QUOTES, 'UTF-8');


    $stmt = $conn->prepare("
        INSERT INTO replies (message_id, sender_id, receiver_id, content) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$messageId, $_SESSION['user_id'], $applicantId, $replyContent]);

    // Set success message
    $_SESSION['message'] = "Your reply has been sent to the applicant.";
    $_SESSION['message_type'] = "success";

    // Redirect to the same page to prevent resubmission
    header("Location: view-messages.php?job_id=$job_id");
    exit;
}
?>

<div class="container mt-5">
    <h2>Messages for "<?php echo htmlspecialchars($job['title']); ?>"</h2>

    <!-- Display success or error messages -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> mt-3">
            <?php echo $_SESSION['message']; ?>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>

    <div class="list-group mt-3">
        <?php if (empty($messages)): ?>
            <p class="text-muted">No messages for this job post yet.</p>
        <?php else: ?>
            <?php foreach ($messages as $message): ?>
                <div class="list-group-item">
                    <h5><?php echo htmlspecialchars($message['applicant_name']); ?></h5>
                    <p><?php echo htmlspecialchars($message['content']); ?></p>
                    <small>Sent at: <?php echo htmlspecialchars($message['sent_at']); ?></small>


                    <form method="POST" class="mt-3">
                        <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                        <input type="hidden" name="applicant_id" value="<?php echo $message['applicant_id']; ?>">
                        <div class="mb-3">
                            <label for="reply-<?php echo $message['id']; ?>" class="form-label">Reply</label>
                            <textarea name="reply" id="reply-<?php echo $message['id']; ?>" class="form-control" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Send Reply</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
?>
