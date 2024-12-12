<?php
require 'db.php'; 


$user_id = $_SESSION['user_id']; 

$stmt = $conn->prepare("SELECT * FROM messages WHERE user_id = ? ORDER BY sent_at DESC");
$stmt->bindParam(1, $user_id, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle if no messages are found
if (!$messages) {
    $noMessages = "No messages available.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - FindHire</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Messages</h2>

        <?php if (isset($noMessages)): ?>
            <p class="text-muted"><?php echo $noMessages; ?></p>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($messages as $message): ?>
                    <div class="list-group-item">
                        <strong><?php echo htmlspecialchars($message['content']); ?></strong>
                        <small class="text-muted d-block"><?php echo htmlspecialchars($message['sent_at']); ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
