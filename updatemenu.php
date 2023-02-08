<?php

include('db.php');
session_start();
$errors = array( 'searchfoodmenu' => '', 'carbs' => '');



//add a new item to the menu
if(isset($_POST['add'])) {


    //get the input values
    $q1 = mysqli_real_escape_string($conn, $_POST['searchfoodmenu']);
    $g1 = mysqli_real_escape_string($conn, $_POST['gramsfoodmenu']);


    if (empty($_POST['gramsfoodmenu'])) {
        $g1 = "100";
    }
    if (empty($_POST['searchfoodmenu'])) {
        $errors['searchfoodmenu'] = 'Please input a food word.';
    }

    //level of carbs, api functionality
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
    //checking if the item already exists
    $menu_no = $_SESSION['menu_no'];

    $query = "SELECT * FROM menu_food WHERE food = '$q1' AND menu_no = '$menu_no'";
    $result = mysqli_query($conn, $query);

    //check if food exists
    if(mysqli_num_rows($result))
        $errors['searchfoodmenu']="Your item already exists.";


    //add item if there are no errors
    if(array_filter($errors)){
        //echo 'errors in form';
    } else {
        $res = (int) $response / 100 * (int)$g1;
        $q1= strtolower($q1);
        $id_to_add = mysqli_real_escape_string($conn, $_POST['id_to_add']);


        $sql = "SELECT food, carbs FROM menu_names WHERE id=$id_to_add";
        $results = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($results);

        if (isset($row)) {
            if ($row['food'] == "Menu is empty!"){
                $menu = $g1."g ".$q1.", ";
                $carbs = (int)$res;}

            else{
            $menu = $row['food']." ".$g1."g ".$q1.", ";
            $carbs = $row["carbs"] +(int)$res;}
        }

        mysqli_free_result($results);


        //save the new updated menu in the database
        $sql1 = "UPDATE menu_names SET food = '$menu', carbs = '$carbs' WHERE id=$id_to_add";

        if (mysqli_query($conn, $sql1)) {
            //success
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }


        //update the other table

        $sql = "SELECT food, carbs,menu_no,grams FROM menu_food";
        $results = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($results);
        echo $menu_no;
        $sql = "INSERT INTO menu_food(menu_no,food,grams,carbs) VALUES('$menu_no','$q1', '$g1','$res') ";


        if (mysqli_query($conn, $sql)) {
            //success
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }
        mysqli_free_result($results);



    }
}


//delete item function
if(isset($_POST['delete'])) {




    $q1 = mysqli_real_escape_string($conn, $_POST['searchfoodmenu']);
    $menu_no = $_SESSION['menu_no'];

    $sql = "DELETE FROM `menu_food` WHERE food = '$q1' AND menu_no= '$menu_no' ";;
    if (mysqli_query($conn, $sql)) {
        // success

    } else {
        echo 'query error: ' . mysqli_error($conn);
    }

    $sql = "SELECT menu_no,grams, food, carbs FROM menu_food WHERE menu_no = '$menu_no'";
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
        $menu = "Menu is empty!";
    }



    $sql1 = "UPDATE menu_names SET food = '$menu', carbs = '$res' WHERE id='$menu_no'";

    if (mysqli_query($conn, $sql1)) {
        //success
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }

}



// check GET request id param
if(isset($_GET['id'])){

    // escape sql chars
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    $_SESSION['menu_no'] = $id;

    // make sql
    $sql = "SELECT * FROM menu_names WHERE id = $id";


    // get the query result
    $result = mysqli_query($conn, $sql);

    // fetch result in array format
    $menu = mysqli_fetch_assoc($result);

    mysqli_free_result($result);
    mysqli_close($conn);

}

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


<?php include('headerstyle.php'); ?>

<div class="container center">
    <?php if($menu): ?>
        <h4><?php echo $menu['menu_name']; ?></h4>
        <h5>Items:</h5>
        <p><?php echo $menu['food']; ?></p>
        <h5>Carbs:</h5>
        <p><?php echo $menu['carbs']; ?></p>
        <h5><?php echo $errors['searchfoodmenu']; ?></h5>

        <div class="container-left">

    <form id="search" method="POST"  class="input-group">

        <h6> Add a new item to the menu!</h6>

        <input autocomplete="off" type="text" color= "black" name="gramsfoodmenu" class="input-field" placeholder="How many grams?" >
        <input autocomplete="off" type="text" name="searchfoodmenu" class="input-field" placeholder="Add some more food!" >
        <div class="red-text"><p style="color:indianred;"><b><?php echo $errors['searchfoodmenu']; ?></b></p></div>

        <input type="hidden" name="id_to_add" value="<?php echo $menu['id']; ?>">


        <input type="submit" name="add" value="Add" style="background-color:#8cbc54 !important;" class="btn brand z-depth-0">
    </form>
    </div>


        <div class="container-right">

            <form id="search" method="POST"  class="input-group">

                <h6> Delete an existing item from the menu!</h6>

                <input autocomplete="off" type="text" name="searchfoodmenu" class="input-field" placeholder="Name of the item" >


                <input type="hidden" name="id_to_add" value="<?php echo $menu['id']; ?>">


                <input type="submit" name="delete" value="Delete" style="background-color:#8cbc54 !important;" class="btn brand z-depth-0">

            </form>
        </div>





        <?php else: ?>
        <h5>No such menu exists.</h5>
    <?php endif ?>
</div>
</body>
</html>