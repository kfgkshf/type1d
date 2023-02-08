<?php

include('db.php');
session_start();


if(isset($_POST['delete'])){

    $id_to_delete = mysqli_real_escape_string($conn, $_POST['id_to_delete']);

    $sql = "DELETE FROM menu_names WHERE id = $id_to_delete";

    if(mysqli_query($conn, $sql)){
        header('Location: menues.php');
    } else {
        echo 'query error: '. mysqli_error($conn);
    }

}
if(isset($_GET['id'])){

    // escape sql chars
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // make sql
    $sql = "SELECT * FROM menu_names WHERE id = $id";

    // get the query result
    $result = mysqli_query($conn, $sql);

    // fetch result in array format
    $menu = mysqli_fetch_assoc($result);

    mysqli_free_result($result);
    mysqli_close($conn);

}


// check GET request id param


?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel = "stylesheet" href="apstyle.css">
</head>
<body>
<header >
    <div class="topnav">
        <a href="registration.php">Home</a>
        <a class="active"  href="menues.php">Menus</a>
        <a href="#menu">Planner</a>
        <div class="topnav-right">
            <a href="profile.php"> <img style="vertical-align:middle" src="profile.png" width="25" height="20">
                <?php echo $_SESSION['userid'] ; ?> </a>
            <a href="index.php"> <img style="vertical-align:middle" src="logout.png" width="30" height="20">
                Logout </a>
        </div>
    </div>
</header>

<img style="display: block; margin-left: auto; margin-right: auto; margin-top:auto; margin-top:30px" src="logo-no-background.png" width="200" height="100">


<?php include('headerstyle.php'); ?>

<div class="container center">
    <?php if($menu): ?>
        <h4><?php echo $menu['menu_name']; ?></h4>
        <p>Created by <?php echo $menu['user']; ?></p>
        <h5>Items:</h5>
        <p><?php echo $menu['food']; ?></p>
        <h5>Carbs:</h5>
        <p><?php echo $menu['carbs']; ?></p>
        <a class="brand-text" href="updatemenu.php?id=<?php echo $menu['id'] ?>"> <input type="submit" name="update" style="background-color:#8cbc54 !important;" value="Update" class="btn brand z-depth-0"> </a>

        <form action="menu_moreinfo.php" method="POST">

            <input type="submit" name="delete" value="Delete" style="background-color:#8cbc54 !important;" onclick="return confirm('Are you sure?')" class="btn brand z-depth-0">
            <input type="hidden" name="id_to_delete"  value="<?php echo $menu['id']; ?>">
        </form>

    <?php else: ?>
        <h5>No such menu exists.</h5>
    <?php endif ?>

</div>
</body>
</html>