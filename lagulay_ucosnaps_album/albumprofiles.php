<?php require_once 'core/dbConfig.php'; ?>
<?php require_once 'core/models.php'; ?>

<?php  
if (!isset($_SESSION['username'])) {
	header("Location: login.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<link rel="stylesheet" href="styles/styles.css">
</head>
<body>
<?php

$albumId = $_GET['album_id'];


$albumStmt = $pdo->prepare("SELECT album_name FROM albums WHERE album_id = :album_id");
$albumStmt->bindParam(':album_id', $albumId);
$albumStmt->execute();
$album = $albumStmt->fetch(PDO::FETCH_ASSOC);


$photoStmt = $pdo->prepare("SELECT photo_name, description, date_added FROM photos WHERE album_id = :album_id");
$photoStmt->bindParam(':album_id', $albumId);
$photoStmt->execute();
$photos = $photoStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Album: <?php echo htmlspecialchars($album['album_name']); ?></h1>
<div class="photoGallery" style="display: flex; flex-wrap: wrap; gap: 10px;">
    <?php foreach ($photos as $photo) { ?>
        <div class="photoCard" style="border: 1px solid #ccc; padding: 10px; width: 200px;">
            <img src="images/<?php echo $photo['photo_name']; ?>" alt="" style="width: 100%; height: auto;">
            <p><?php echo htmlspecialchars($photo['description']); ?></p>
            <p><i><?php echo htmlspecialchars($photo['date_added']); ?></i></p>
        </div>
    <?php } ?>
</div>


</body>
</html>