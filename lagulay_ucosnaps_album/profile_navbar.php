<div class="navbar" style="text-align: center; margin-bottom: 50px;">
	<h1>Welcome to UCOSnaps, <span style="color: blue;"><?php echo $_SESSION['username']; ?></span></h1>
	<a href="index.php">Home</a>
	<a href="profile.php?username=<?php echo $_SESSION['username']; ?>">Your Profile</a>

	<?php 
	if (!isset($_GET['username']) || $_GET['username'] == $_SESSION['username']) { ?>
		<a href="albums.php?username=<?php echo $_SESSION['username']; ?>">Albums</a>
	<?php } ?>

	<a href="core/handleForms.php?logoutUserBtn=1">Logout</a>
</div>
