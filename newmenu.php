<?php include('db.php');
session_start();

if(array_key_exists('id', $_COOKIE)) {
    $_SESSION['id'] = $_COOKIE['id'];
}


$errors = array( 'searchfoodmenu' => '', 'namefoodmenu'=>'');
$menu='';
$res='0';
$cg = "carbohydrates";
$sofar="So far you have:";


//delete function
if(isset($_POST['delete'])) {

    $sql = "DELETE FROM `menu_food_new_menu` ";
    if (mysqli_query($conn, $sql)) {
        // success

    } else {
        echo 'query error: ' . mysqli_error($conn);
    }
    $sofar="";
}

//delete item function
if(isset($_POST['deleteitem'])) {


    $q1 = mysqli_real_escape_string($conn, $_POST['searchfoodmenu']);
    $sql = "DELETE FROM `menu_food_new_menu` WHERE food = '$q1'";
    if (mysqli_query($conn, $sql)) {
        // success

    } else {
        echo 'query error: ' . mysqli_error($conn);
    }


}
//create menu function

if(isset($_POST['add'])) {

    //get the input values
    $q1 = mysqli_real_escape_string($conn, $_POST['searchfoodmenu']);
    $g1 = mysqli_real_escape_string($conn, $_POST['gramsfoodmenu']);

    //if the inputs are empty
    if (empty($_POST['gramsfoodmenu'])) {
        $g1 = "100";
    }
    if (empty($_POST['searchfoodmenu'])) {
        $errors['searchfoodmenu'] = 'Please input a food word.';
    }


    //get the carbs of the new item, api functionality
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://edamam-food-and-grocery-database.p.rapidapi.com/parser?ingr=" . $q1,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "X-RapidAPI-Host: edamam-food-and-grocery-database.p.rapidapi.com",
            "X-RapidAPI-Key: 38115694d8msh0815d23ecb2fc42p12a50ajsn0afd1f1a6a1f"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {

        if (strlen($response) < 50 ) {
            $errors['searchfood'] = 'Please input a correct food word.';
        } else {

            if (!is_numeric($g1)) {
                $errors['searchfood'] = 'Please input a number.';
            }
            else
            {

                $strArray = explode('"',$response);
                $response = trim($strArray[70],",:");
            }

        }
    }

    //check if food exists
    $query = "SELECT * FROM menu_food_new_menu WHERE food = '$q1'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result))
        $errors['searchfoodmenu']="Your item already exists.";

    //add new item if there are no errors
    if(array_filter($errors)){
        //echo 'errors in form';
    } else {
        $res = (int) $response / 100 * (int)$g1;
        $q1= strtolower($q1);

        $sql = "INSERT INTO menu_food_new_menu(food,carbs,grams) VALUES('$q1','$res', '$g1') ";
        if (mysqli_query($conn, $sql)) {
            // success

        } else {
            echo 'query error: ' . mysqli_error($conn);
        }
    }
}
//save menu
if(isset($_POST['save'])) {

    //check if there is a name
    if (empty($_POST['namefoodmenu'])) {
        $errors['namefoodmenu'] = 'Error: A name is required';
    }else{
        $name = mysqli_real_escape_string($conn, $_POST['namefoodmenu']);
        $user = $_SESSION["userid"];

        //check if menu name already exists
        $query = "SELECT * FROM menu_names WHERE menu_name = '$name' AND user='$user'";
        $result = mysqli_query($conn, $query);

        if(mysqli_num_rows($result))
            $errors['namefoodmenu']="Error: This name already exists.";

    }
    //save the menu if there are no errors
    if(array_filter($errors)){
        //echo 'errors in form';
    } else {

        $sql = "SELECT * FROM menu_food_new_menu";
        $array = $conn->query($sql);


        if ($array->num_rows > 0) {
            // output data of each row
                if (mysqli_query($conn, $sql)) {
                    $errors['namefoodmenu']="Your menu was saved successfully.";
                    $sofar="";
                    //update menu_name


                    $sql = "SELECT food, carbs, grams FROM menu_food_new_menu";
                    $array = $conn->query($sql);

                    $res = '0';
                    $menu = " ";
                        // output data of each row
                    while ($row = $array->fetch_assoc()) {
                            $menu = $menu . " " . $row['grams'] . "g " . $row['food'] . ",";
                            $res = (int)$row['carbs'] + (int)$res;

                    }
                    //configure both tables
                    $sql = "INSERT INTO menu_names(menu_name,user,food,carbs) VALUES('$name','$user', '$menu','$res') ";
                    $result = mysqli_query($conn, $sql);


                    $sql = "SELECT * FROM menu_names WHERE menu_name = '$name'";
                    $array = $conn->query($sql);

                    while ($row = $array->fetch_assoc()) {
                        $menu_no = $row['id'];
                    }

                    $sql = "SELECT food, carbs, grams FROM menu_food_new_menu";
                    $array = $conn->query($sql);

                    // output data of each row
                    while ($row = $array->fetch_assoc()) {
                        $food = $row['food'];
                        $grams = $row['grams'];
                        $carbs = $row['carbs'];
                        $sql = "INSERT INTO menu_food(food,grams,carbs,menu_no) VALUES('$food','$grams', '$carbs','$menu_no') ";
                        $result1 = mysqli_query($conn, $sql);

                    }




                    //delete the table
                    $sql = "DELETE FROM `menu_food_new_menu` ";
                    if (mysqli_query($conn, $sql)) {
                        // success

                    } else {
                        echo 'query error: ' . mysqli_error($conn);
                    }


                } else {
                    echo 'query error: ' . mysqli_error($conn);
                }

            }
        else {
            $errors['searchfoodmenu']="Please add some items to the menu before saving it.";
        }

    }
}

$sql = "SELECT food, carbs, grams FROM menu_food_new_menu";
$array = $conn->query($sql);

$res = '0';
$menu = " ";

if ($array->num_rows > 0) {
    // output data of each row
    while ($row = $array->fetch_assoc()) {
        $menu = $menu . " " . $row['grams'] . "g " . $row['food'] . ",";
        $res = (int)$row['carbs'] + (int)$res;

    }
} else {
    //echo "0 results";
    $menu = " ";
    $res = " ";
    $cg = "";
    $sofar="";
}



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

<h2> Create a menu! <br> </h2>

<div class = "container center">

    <div id="added" " >  <?php echo $menu; ?>  </div>


    <div id="results" > <?php echo $sofar; ?> <?php echo $res; ?> <?php echo $cg; ?>  </div>
    <div class="red-text"><p style="color:indianred;"><b><?php echo $errors['searchfoodmenu']; ?></b></p></div>
    <div class="red-text"><p style="color:indianred;"><b><?php echo $errors['namefoodmenu']; ?></b></p></div>
</div>

<div class="container center">

        <div class="container-left">

            <form id="search" method="POST"  class="input-group">

                <h6> Add a new item to the menu!</h6>

                <input autocomplete="off" type="text" color= "black" name="gramsfoodmenu" class="input-field" placeholder="How many grams?" >
                <input autocomplete="off" type="text" name="searchfoodmenu" class="input-field" placeholder="Add some more food!" >
                <div class="red-text"><p style="color:indianred;"><b><?php echo $errors['searchfoodmenu']; ?></b></p></div>


                <input type="submit" name="add" style="background-color:#8cbc54 !important;" value="Add" class="btn brand z-depth-0">
                <p><?php echo $errors['searchfoodmenu']; ?></p>
            </form>
        </div>


        <div class="container-right">

            <form id="search" method="POST"  class="input-group">

                <h6> Delete an existing item from the menu!</h6>

                <input autocomplete="off" type="text" name="searchfoodmenu" class="input-field" placeholder="Name of the item" >


                <input type="submit" style="background-color:#8cbc54 !important;" name="deleteitem" value="Delete" class="btn brand z-depth-0">

            </form>
        </div>
<div class = "container-bottom">
    <form id="search" method="POST"  class="input-group">

        <input autocomplete="off" type="text" name="namefoodmenu" class="input-field" placeholder="Give your menu a name!" >

        <button type="submit" name="save" value="Save" style="background-color:#8cbc54 !important;" class="btn brand z-depth-0" onclick="Function()"> Save menu </button>

    </form>
</div>
</div>





</body>
</html>

