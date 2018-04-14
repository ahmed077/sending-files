<?php
ob_start();
session_start();
$title = "profile";
$active = "prifile";
require_once 'partials/connect.php';
require_once 'partials/functions.php';
if (isset($_SESSION['user_id']) && checkDB('user','id',$_SESSION['user_id'])) {
    require_once 'partials/header.html';
?>
<?php
$query = $con->prepare("SELECT * FROM user WHERE id=?");
$query->execute(array($_SESSION['user_id']));
$userdata = $query->fetch(PDO::FETCH_ASSOC);
$query2 = $con->prepare("SELECT * FROM music WHERE user_id=?");
$query2->execute(array($_SESSION['user_id']));
if ($query2->rowCount() > 0) {
    $musicList = $query2->fetchAll(PDO::FETCH_ASSOC);
} else {
    $musicList = [];
}
if ($_SERVER['REQUEST_METHOD']==='POST') {
	$name=filter_var($_POST['name'],FILTER_SANITIZE_STRING);
	$password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
	if (empty($password)) {
		$query = $con->prepare("UPDATE user SET `name`=? WHERE id=?");
		$query->execute(array($name, $_SESSION['user_id']));
	} else {
		$password = sha1($password);
		$query = $con->prepare("UPDATE user SET `name`=?,`password`=? WHERE id=?");
		$query->execute(array($name,$password,$_SESSION['user_id']));
	}
	header("location:profile.php");
	exit();
}
if (isset($_GET['action']) && $_GET['action'] === 'edit') { ?>
	<div class="container pb-5">
	<h1 class="bgFont"> Profile Info :</h1>
	<br>
	<div class="card col-12">
		<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" id="edit-profile">
			<ul class="list-group list-group-flush">
				<li class="list-group-item">
					<div class="row align-items-center">
						<div class="col-4 font-weight-bold">
							Name
						</div>
						<div class="col-8">
							<input class="form-control" type="text" value='<?php echo $userdata['name'];?>' name="name" placeholder="Enter Your Name">
						</div>
					</div>
				</li>
				<li class="list-group-item">
					<div class="row align-items-center">
						<div class="col-4 font-weight-bold">
							Password
						</div>
						<div class="col-8">
							<input class="form-control" type="password" name="password" placeholder="Enter Your Password ~ Leave Empty to Not Edit">
						</div>
					</div>
				</li>
				<li class="list-group-item">
					<div class="row align-items-center">
						<div class="col-4 font-weight-bold">
							Confirm Password
						</div>
						<div class="col-8">
							<input class="form-control" type="password" name="confirmpassword" placeholder="Re-enter Your Password">
						</div>
					</div>
				</li>
				<li class="list-group-item justify-content-end d-flex">
				<input type="submit" class="btn btn-primary">
				</li>
			</ul>
		</form>
	</div>
	<script>
		var editForm = document.getElementById("edit-profile");
		editForm.onsubmit = function (e) {
			// e.preventDefault();
			if (editForm.password.value !== editForm.confirmpassword.value) {
				alert("Password and Confirmation Does Not Match");
				return false;
			}
		}
	</script>
<?php } else {?>
<div class="container pb-5">
    <h1 class="bgFont"> Profile Info :</h1>
    <br>
    <div class="card col-12">
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <div class="row">
                    <div class="col-4 font-weight-bold">
                    Name
                    </div>
                    <div class="col-8">
                        <?php echo $userdata['name'];?>
                    </div>
                </div>                 
            </li>
            <li class="list-group-item">
                <div class="row">
                    <div class="col-4 font-weight-bold">
                    Email
                    </div>
                    <div class="col-8">
                        <?php echo $userdata['email'];?>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row">
                    <div class="col-4 font-weight-bold">
                        Favorite Songs
                    </div>
                    <div class="col-8">
                        <?php
                            if (empty($musicList)) {
                                echo "No Music Added Yet.";
                            } else { ?>
                                <div class="row justify-content-between">
                                <?php foreach ($musicList as $music) {?>
                                    <div class="row flex-column justify-content-center text-center">
                                        <a href="music.php?id=<?php echo $music['id'];?>" class="mr-4">
                                            <span class="btn btn-primary"><?php echo $music['name'];?></span>
                                        </a>
                                        <a href="#" data-track="<?php echo $music['track'];?>" class="play">
                                            <span>Play</span>
                                        </a>
                                    </div>
                                <?php } ?>
                                </div>
                        <?php } ?>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
<div class="row no-gutters justify-content-center">
    <audio controls="controls" id="audioPlayer">
      <source id="mp3Source" src="track.mp3" type="audio/mpeg" />
    Your browser does not support the audio element.
    </audio>
</div>
        
<?php }
} else {
    header("location:index.php");
    exit();
}
require_once 'partials/footer.html';
ob_end_flush();
?>