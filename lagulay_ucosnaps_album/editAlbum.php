<?php
// Connect to the database
require_once 'core/dbConfig.php';

// Fetch album details if album_id is set
if (isset($_GET['album_id'])) {
    $albumId = $_GET['album_id'];

    // Fetch album data
    $stmt = $pdo->prepare("SELECT album_name FROM albums WHERE album_id = :album_id");
    $stmt->bindParam(':album_id', $albumId);
    $stmt->execute();
    $album = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update album name
    $albumName = $_POST['album_name'];
    $stmt = $pdo->prepare("UPDATE albums SET album_name = :album_name WHERE album_id = :album_id");
    $stmt->bindParam(':album_name', $albumName);
    $stmt->bindParam(':album_id', $albumId);
    
    if ($stmt->execute()) {
        header("Location: albums.php");
        exit;
    } else {
        echo "Error: Unable to update album.";
    }
}
?>
<form method="POST">
    <label for="album_name">Edit Album Name:</label>
    <input type="text" name="album_name" value="<?php echo htmlspecialchars($album['album_name']); ?>" required>
    <button type="submit">Update</button>
</form>
