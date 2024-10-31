<?php
include('connect.php'); // Ensure this path is correct

// Check if the button was clicked to create a new post
if (isset($_POST['btnSubmitPost'])) {
    $content = $_POST['content'];
    $userID = 1; // Example user ID; replace this with actual user ID from session or auth
    $dateTime = date('Y-m-d H:i:s'); // Current timestamp

    $postQuery = "INSERT INTO posts (userID, content, dateTime, privacy, isDeleted) VALUES ('$userID', '$content', '$dateTime', 'public', 0)";
    executeQuery($postQuery);
}

// Fetch posts
$query = "
    SELECT p.*, u.username 
    FROM posts p 
    JOIN users u ON p.userID = u.userID 
    WHERE p.isDeleted = 0 
    ORDER BY p.dateTime DESC
";
$result = executeQuery($query);

// Aq si user 1
$userID = 1; 
$messageQuery = "
    SELECT m.*, u.username 
    FROM messages m 
    JOIN users u ON m.senderID = u.userID 
    WHERE m.senderID != '$userID' 
    ORDER BY m.dateTime DESC
";
$messageResult = executeQuery($messageQuery);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chat Matte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Left Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <h3>Chat Matte</h3>
                <a href="#" class="nav-link-active">Home</a>
                <a href="#">Explore</a>
                <a href="#">Messages</a>
                <a href="#">Notifications</a>
                <a href="#">Settings</a>
            </div>

            <!-- Middle Post Section -->
            <div class="col-md-6 col-lg-7">
                <div class="post-section">
                    <h4>Create a Post</h4>
                    <form method="post">
                        <textarea class="form-control" name="content" rows="2" placeholder="What's on your mind?" required></textarea>
                        <button class="btn btn-primary mt-2" name="btnSubmitPost">Post</button>
                    </form>

                    <hr>

                    <h4>Recent Posts</h4>
                    <!-- Display posts -->
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($post = mysqli_fetch_assoc($result)): ?>
                            <div class="post">
                                <p><strong><?php echo htmlspecialchars($post['username']); ?></strong></p>
                                <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                                <p><small><?php echo htmlspecialchars($post['dateTime']); ?></small></p>
                                <hr>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No posts available.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Message Section -->
            <div class="col-md-3 col-lg-3">
                <div class="message-section">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">Primary</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Archive</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Requests</a>
                        </li>
                    </ul>
                    <div class="mt-3">
                        <h5>Messages</h5>
                        <!-- Display messages -->
                        <?php if (mysqli_num_rows($messageResult) > 0): ?>
                            <?php while ($message = mysqli_fetch_assoc($messageResult)): ?>
                                <div class="message <?php echo $message['isRead'] ? 'message-read' : 'message-unread'; ?>">
                                    <p><strong><?php echo htmlspecialchars($message['username']); ?>:</strong> <?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                                    <p><small><?php echo htmlspecialchars($message['dateTime']); ?></small></p>
                                </div>
                                <hr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>No messages available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
