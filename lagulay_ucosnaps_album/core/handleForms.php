<?php  
require_once 'dbConfig.php';
require_once 'models.php';

if (isset($_GET['album_id'])) {
    $albumId = $_GET['album_id'];

    // Set the album_id of all photos to 0 for the given album_id
    $updatePhotos = $pdo->prepare("UPDATE photos SET album_id = 0 WHERE album_id = :album_id");
    $updatePhotos->bindParam(':album_id', $albumId);
    $updatePhotos->execute();

    // Delete album
    $deleteAlbum = $pdo->prepare("DELETE FROM albums WHERE album_id = :album_id");
    $deleteAlbum->bindParam(':album_id', $albumId);

    if ($deleteAlbum->execute()) {
        header("Location: ../albums.php");
        exit;
    } else {
        echo "Error: Unable to delete album.";
    }
}

if (isset($_POST['assignPhotosBtn'])) {
    // Get album_id and photo_ids from the form
    $albumId = $_POST['album_id'];
    $photoIds = $_POST['photo_ids'];

    if (!empty($albumId) && !empty($photoIds)) {
        // Update the album_id for the selected photos
        $stmt = $pdo->prepare("UPDATE photos SET album_id = :album_id WHERE photo_id IN (" . implode(',', array_map('intval', $photoIds)) . ")");
        $stmt->bindParam(':album_id', $albumId);

        if ($stmt->execute()) {
            header("Location: ../albums.php?");
            exit;
        } else {
            echo "Error: Unable to assign photos to album.";
        }
    } else {
        echo "Error: Please select an album and at least one photo.";
    }
}

if (isset($_POST['insertAlbumBtn'])) {
    // Check if the album name is provided
    if (!empty($_POST['aname'])) {
        // Sanitize the input
        $albumName = htmlspecialchars($_POST['aname']);
        
        // Assume user_id is retrieved from the session (you should already have a login system in place)
        session_start();
        $userId = $_SESSION['user_id'];

        // Prepare and execute the SQL query to insert a new album
        $stmt = $pdo->prepare("INSERT INTO albums (album_name, user_id) VALUES (:album_name, :user_id)");
        $stmt->bindParam(':album_name', $albumName);
        $stmt->bindParam(':user_id', $userId);

        if ($stmt->execute()) {
            // Redirect or give success feedback
            header("Location: ../albums.php");
            exit;
        } else {
            echo "Error: Could not create album.";
        }
    } else {
        echo "Error: Album name cannot be empty.";
    }
}
if (isset($_POST['insertNewUserBtn'])) {
	$username = trim($_POST['username']);
	$first_name = trim($_POST['first_name']);
	$last_name = trim($_POST['last_name']);
	$password = trim($_POST['password']);
	$confirm_password = trim($_POST['confirm_password']);

	if (!empty($username) && !empty($first_name) && !empty($last_name) && !empty($password) && !empty($confirm_password)) {

		if ($password == $confirm_password) {

			$insertQuery = insertNewUser($pdo, $username, $first_name, $last_name, password_hash($password, PASSWORD_DEFAULT));
			$_SESSION['message'] = $insertQuery['message'];

			if ($insertQuery['status'] == '200') {
				$_SESSION['message'] = $insertQuery['message'];
				$_SESSION['status'] = $insertQuery['status'];
				header("Location: ../login.php");
			}

			else {
				$_SESSION['message'] = $insertQuery['message'];
				$_SESSION['status'] = $insertQuery['status'];
				header("Location: ../register.php");
			}

		}
		else {
			$_SESSION['message'] = "Please make sure both passwords are equal";
			$_SESSION['status'] = '400';
			header("Location: ../register.php");
		}

	}

	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../register.php");
	}
}

if (isset($_POST['loginUserBtn'])) {
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);

	if (!empty($username) && !empty($password)) {

		$loginQuery = checkIfUserExists($pdo, $username);
		$userIDFromDB = $loginQuery['userInfoArray']['user_id'];
		$usernameFromDB = $loginQuery['userInfoArray']['username'];
		$passwordFromDB = $loginQuery['userInfoArray']['password'];

		if (password_verify($password, $passwordFromDB)) {
			$_SESSION['user_id'] = $userIDFromDB;
			$_SESSION['username'] = $usernameFromDB;
			header("Location: ../index.php");
		}

		else {
			$_SESSION['message'] = "Username/password invalid";
			$_SESSION['status'] = "400";
			header("Location: ../login.php");
		}
	}

	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../register.php");
	}

}

if (isset($_GET['logoutUserBtn'])) {
	unset($_SESSION['user_id']);
	unset($_SESSION['username']);
	header("Location: ../login.php");
}


if (isset($_POST['insertPhotoBtn'])) {

	// Get Description
	$description = $_POST['photoDescription'];

	// Get file name
	$fileName = $_FILES['image']['name'];

	// Get temporary file name
	$tempFileName = $_FILES['image']['tmp_name'];

	// Get file extension
	$fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

	// Generate random characters for image name
	$uniqueID = sha1(md5(rand(1,9999999)));

	// Combine image name and file extension
	$imageName = $uniqueID.".".$fileExtension;

	// If we want edit a photo
	if (isset($_POST['photo_id'])) {
		$photo_id = $_POST['photo_id'];
	}
	else {
		$photo_id = "";
	}

	// Save image 'record' to database
	$saveImgToDb = insertPhoto($pdo, $imageName, $_SESSION['username'], $description, $photo_id);

	// Store actual 'image file' to images folder
	if ($saveImgToDb) {

		// Specify path
		$folder = "../images/".$imageName;

		// Move file to the specified path 
		if (move_uploaded_file($tempFileName, $folder)) {
			header("Location: ../index.php");
		}
	}

}

if (isset($_POST['deletePhotoBtn'])) {
	$photo_name = $_POST['photo_name'];
	$photo_id = $_POST['photo_id'];
	$deletePhoto = deletePhoto($pdo, $photo_id);

	if ($deletePhoto) {
		unlink("../images/".$photo_name);
		header("Location: ../index.php");
	}

}