<?php
include('connect.php');

// Check if the button was clicked to create a new post
if (isset($_POST['btnSubmitPost'])) {
    $content = $_POST['content'];
    $userID = 1;
    $dateTime = $_POST['dateTime'];  // Get the dateTime from the form submission
    $image = null;

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $imageSize = $_FILES['image']['size'];
        $imageType = $_FILES['image']['type'];

        // Define allowed file types and max size
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024;

        // Check if the file type and size are valid
        if (in_array($imageType, $allowedTypes) && $imageSize <= $maxSize) {
            // Generate a unique file name to avoid overwriting existing files
            $imageFileName = uniqid('post_image_', true) . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
            $uploadDir = 'assets/uploads/';
            $imagePath = $uploadDir . $imageFileName;

            // Move the uploaded file to the server directory
            if (move_uploaded_file($imageTmpName, $imagePath)) {
                $image = $imagePath;
            } else {
                echo "Failed to upload image.";
            }
        } else {
            echo "Invalid file type or file size exceeds limit.";
        }
    }

    // Insert post into the database 
    $postQuery = "INSERT INTO posts (userID, content, dateTime, privacy, isDeleted, attachment, cityID, provinceID) 
                  VALUES ('$userID', '$content', '$dateTime', 'public', 0, '$image', '1', '1')";
    executeQuery($postQuery);

    // Redirect to the same page to show updated posts
    header("Location: " . $_SERVER['PHP_SELF']);
    exit(); 
}

// Fetch posts with city and province names
$query = "
    SELECT p.*, u.username, c.cityName, pv.provinceName 
    FROM posts p
    JOIN users u ON p.userID = u.userID 
    JOIN cities c ON p.cityID = c.cityID
    JOIN provinces pv ON pv.provinceID = p.provinceID
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
            <div class="col-4 col-md-3 col-lg-2 sidebar">
                <h3>Chat Matte</h3>
                <a href="#" class="nav-link-active">Home</a>
                <a href="#">Explore</a>
                <a href="#">Messages</a>
                <a href="#">Notifications</a>
                <a href="#">Settings</a>
            </div>

            <!-- Middle Post Section -->
            <div class="col-8 col-md-6 col-lg-7">
                <div class="post-section">
                    <h4>Create a Post</h4>
                    <form method="post" enctype="multipart/form-data">
                        <textarea class="form-control" name="content" rows="2" placeholder="What's on your mind?" required></textarea>

                        <!-- File input for attaching an image -->
                        <div class="mt-2">
                            <label for="imageUpload" class="form-label">Attach a Picture (optional)</label>
                            <input type="file" class="form-control" name="image" id="imageUpload" accept="image/*">
                        </div>

                        <!-- Hidden input to store the user's local dateTime -->
                        <input type="hidden" name="dateTime" id="dateTime">

                        <button class="btn btn-primary mt-2" name="btnSubmitPost" type="submit">Post</button>
                    </form>

                    <hr>

                    <h4>Recent Posts</h4>
                    <!-- Display posts -->
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($post = mysqli_fetch_assoc($result)) {
                            ?>
                            <div class="postC card mb-3" style="background-color: #333;">
                                <div class="card-body text-white">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-4 col-md-6 col-lg-9">
                                                <h5 class="mb-0 text-white"><?php echo htmlspecialchars($post['username']); ?></h5>
                                            </div>
                                            <div class="col-8 col-md-6 col-lg-3">
                                                <small class="text-white-muted m-0" style="color: rgba(220, 220, 220, 0.7);">
                                                    <?php echo date('M d, Y', strtotime($post['dateTime'])); ?> | 
                                                    <?php echo date('h:i A', strtotime($post['dateTime'])); ?>
                                                </small>
                                            </div>
                                        </div>
                                        <div class="row py-3">
                                            <div class="col-12">
                                                <p class="text-white-muted" style="color: rgba(220, 220, 220, 0.7);">
                                                    <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($post['cityName']); ?>, <?php echo htmlspecialchars($post['provinceName']); ?>
                                                </p>
                                            </div>
                                            <div class="col-12 pt-3">
                                                <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if (!empty($post['attachment'])): ?>
                                        <img src="./<?php echo htmlspecialchars($post['attachment']); ?>" class="img-fluid rounded" alt="Post Image">
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<p>No posts available.</p>';
                    }
                    ?>
                </div>
            </div>

            <!-- Right Message Section -->
            <div class="col-12 col-md-3 col-lg-3">
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
                    <?php
                        if (mysqli_num_rows($messageResult) > 0) {
                            while ($message = mysqli_fetch_assoc($messageResult)) {
                                ?>
                                    <div class="card my-3 <?php echo $message['isRead'] ? 'message-read' : 'message-unread'; ?>">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($message['username']); ?></h5>
                                            <p class="card-text"><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                                            <small><?php echo htmlspecialchars($message['dateTime']); ?></small>
                                        </div>
                                    </div>
                                <?php
                            }
                        } else {
                            echo '<p>No messages available.</p>';
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const localDateTime = new Date();
        localDateTime.setHours(localDateTime.getHours() + 7);

        const utcPlus8DateTime = localDateTime.toISOString();

        // Set the UTC+8 dateTime value into the hidden input field
        document.getElementById('dateTime').value = utcPlus8DateTime;
    </script>
</body>

</html>
