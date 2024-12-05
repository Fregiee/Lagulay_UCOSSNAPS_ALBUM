<?php 
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$userId = $_SESSION['user_id'];


$allAlbums = $pdo->query("SELECT * FROM albums")->fetchAll(PDO::FETCH_ASSOC);


$photosStmt = $pdo->prepare("SELECT photo_id, photo_name FROM photos WHERE username = :username AND (album_id IS NULL OR album_id = 0)");
$photosStmt->bindParam(':username', $username);
$photosStmt->execute();
$photos = $photosStmt->fetchAll(PDO::FETCH_ASSOC);


$userAlbumsStmt = $pdo->prepare("SELECT album_id, album_name FROM albums WHERE user_id = :user_id");
$userAlbumsStmt->bindParam(':user_id', $userId);
$userAlbumsStmt->execute();
$userAlbums = $userAlbumsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Albums</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <?php include 'profile_navbar.php'; ?>

    
    <form action="core/handleForms.php" method="POST" enctype="multipart/form-data" style="margin-bottom: 20px;">
        <label for="aname">Album Name:</label><br>
        <input type="text" name="aname" required><br>
        <input type="submit" name="insertAlbumBtn" value="Create Album">
    </form>

    
    <div class="container" style="display: flex; justify-content: center;">
        <div class="allAlbums" style="background-color: ghostwhite; border-style: solid; border-color: gray; width: 25%; text-align: center;">
            <h1>All Albums</h1>
            <ul style="list-style-type: none; padding: 0;">
                <?php foreach ($allAlbums as $album) { ?>
                    <li style="margin-top: 10px; display: flex; align-items: center; justify-content: space-between;">
                        <a href="albumprofiles.php?album_id=<?php echo $album['album_id']; ?>" style="flex: 1;">
                            <?php echo htmlspecialchars($album['album_name']); ?>
                        </a>
                        <a href="editalbum.php?album_id=<?php echo $album['album_id']; ?>" style="color: blue; margin-left: 10px;">Edit</a>
                        <a href="core/handleForms.php?album_id=<?php echo $album['album_id']; ?>" style="color: red; margin-left: 10px;" 
                           onclick="return confirm('Are you sure you want to delete this album? This action cannot be undone.')">Delete</a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>

    
    <form action="core/handleForms.php" method="POST" style="margin-top: 20px;">
        <h3>Select Album:</h3>
        <select name="album_id" required>
            <option value="">Select an Album</option>
            <?php foreach ($userAlbums as $album) { ?>
                <option value="<?php echo $album['album_id']; ?>">
                    <?php echo htmlspecialchars($album['album_name']); ?>
                </option>
            <?php } ?>
        </select>

        <h3>Select Photos to Add:</h3>
        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
            <?php foreach ($photos as $photo) { ?>
                <div style="text-align: center;">
                    <input type="checkbox" name="photo_ids[]" value="<?php echo $photo['photo_id']; ?>">
                    <img src="images/<?php echo htmlspecialchars($photo['photo_name']); ?>" alt="" style="width: 100px; height: auto;">
                </div>
            <?php } ?>
        </div>

        <button type="submit" name="assignPhotosBtn" style="margin-top: 10px;">Add Photos to Album</button>
    </form>
</body>
</html>
