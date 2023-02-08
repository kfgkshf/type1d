<?php include('db.php');
session_start();

if(array_key_exists('id', $_COOKIE)) {
    $_SESSION['id'] = $_COOKIE['id'];
    //echo " hellooo " . $_SESSION['userid'];
}

    $user = $_SESSION["userid"];

    $query = "SELECT * FROM menu_names WHERE user = '$user'  ";
    $result = mysqli_query($conn, $query);
    $menu = mysqli_fetch_all($result, MYSQLI_ASSOC);






?>


<!DOCTYPE html>
<html lang="en">
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
    <h1 > Menues </h1>
        <div class ="container" style="background-color:#8cbc54 !important;">
            <div class="row">

                <?php foreach($menu as $menu){ ?>

                    <div class="col s6 md3">
                        <div class="card z-depth-0">
                            <div class="card-content center">
                                <h6><?php echo htmlspecialchars($menu['menu_name']); ?></h6>
                                <div><?php echo htmlspecialchars($menu['food']); ?></div>
                            </div>
                            <div class="card-action right-align">
                                <a class="brand-text" style="color:#8cbc54 !important;" href="menu_moreinfo.php?id=<?php echo $menu['id'] ?>"> More info </a>
                            </div>
                        </div>
                    </div>

                <?php } ?>

                <div class="col s6 md3">
                    <div class="card z-depth-0">
                        <div class="card-content center">
                            <h6> New menu </h6>
                            <div class="card-action right-center">
                                <a href="newmenu.php" style="  display: block; margin-left: auto; margin-right: auto;  width: 50%;"><img  src="add.png" width="30" height="30"> </a>
                            </div>
                    </div>
                </div>

            </div>
        </div>







</body>
</html>

