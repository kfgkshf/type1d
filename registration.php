
<?php

include('db.php');
session_start();

if(array_key_exists('id', $_COOKIE))
{
    $_SESSION['id']=$_COOKIE['id'];
    //echo " hellooo ".$_SESSION['userid'];
}

$image = "";
$predefined = "100g of Apples have 14 carbohydrates.";
$stack = array();
$stackcarbs = array();
$try = "";
$a = $a1 =  "";
$b = $b1 = "";
$grams = $grams1 = "";
$carbs = $carbs1 = "";
$recommandations=" ";
$text="";
$errors = array('searchfood' => '', 'searchfood1' => '', 'carbs' => '');
$g = $g1= "100";
$res=$res1='0';
$q= $q1 = "";
$counter = $_SESSION["counter"];

$sql = "DELETE FROM `food` WHERE menu_no <> $counter";
if (mysqli_query($conn, $sql)) {
    // success

} else {
    echo 'query error: ' . mysqli_error($conn);
}



//delete function
if(isset($_POST['delete'])) {

    $sql = "DELETE FROM `food` ";
    if (mysqli_query($conn, $sql)) {
        // success

    } else {
        echo 'query error: ' . mysqli_error($conn);
    }
}


//search function for food

if(isset($_POST['search'])) {

    $q = mysqli_real_escape_string($conn, $_POST['searchfood']);
    $g = mysqli_real_escape_string($conn, $_POST['gramsfood']);

    if (empty($_POST['gramsfood'])) {
        $g = "100";
    }
    if (empty($_POST['searchfood'])) {
        $errors['searchfood'] = 'Please input a food word.';
        $a = $b =  $q = $carbs = "";


    }else {

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://edamam-food-and-grocery-database.p.rapidapi.com/parser?ingr=" . $q,
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
            $a = $b = $response = $g = $q = $carbs = "";
        } else {

            if (strlen($response) < 100) {
                $errors['searchfood'] = 'Please write a correct food word.';
                $a = $b = $response = $g = $q = $carbs = "";
            } else {

                if (!is_numeric($g)) {
                    $errors['searchfood'] = 'Please input a number.';
                    $a = $b = $response = $g = $q = $carbs = "";
                } else {

                    $strArray = explode('"', $response);
                    $response = trim($strArray[70], ",:}");
                    $carbs = (int)$response * (int)$g / 100;
                    $a = "g of";
                    $b = "total of carbohydrates is";
                    $grams = $g;
                    $image = $strArray[83];


                    while (($key = array_search("nutrients", $strArray)) != NULL)

                    {
                        $try = "Try one of the following: ";
                        if (!in_array($strArray[$key-2], $stack)){
                            array_push($stack,  $strArray[$key-2]);
                            $carbss = substr($strArray[$key+9], 0, 5);
                            $carbss = trim($carbss, ",:}");
                            if (!$carbss) $carbss = "Not found.";
                            array_push($stackcarbs, $carbss);
                        }


                        unset($strArray[$key]);

                    }
                    $predefined = "";
                }

            }
        }
    }
}

//bar code
if(isset($_POST['search_code'])) {

    $q1 = mysqli_real_escape_string($conn, $_POST['searchfood1']);

    if (empty($_POST['searchfood1'])) {
        $errors['searchfood1'] = 'Please input a food word.';
        $a1 = $b1 =  $q1 = $carbs1 = "";


    }else {

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://edamam-food-and-grocery-database.p.rapidapi.com/parser?upc=" . $q1,
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

        $response1 = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
            $a1 = $b1 = $response1 = $q1 = $carbs1 = "";
        } else {

            if (strlen($response1) < 100) {
                $errors['searchfood1'] = 'Could not find barcode.';
                $a1 = $b1 = $response1 = $q1 = $carbs1 = "";
            } else {

                if (!is_numeric($g)) {
                    $errors['searchfood1'] = 'Please input a number.';
                    $a1 = $b1 = $response1 = $q1 = $carbs1 = "";
                } else {

                    $strArray1 = explode('"', $response1);
                    $key = array_search("CHOCDF", $strArray1);
                    $response1 = trim($strArray1[$key+1], ",:");
                    $key = array_search("label", $strArray1);
                    $q1 = trim($strArray1[$key+2], ",:");
                    $carbs1 = (int)$response1;
                    $a1 = "g of";
                    $b1 = "total of carbohydrates is";
                    $predefined = "";
                    $key = array_search("image", $strArray1);
                    $image = trim($strArray1[$key+2], ",:");

                }

            }
        }
    }
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
        <a class="active" href="registration.php">Home</a>
        <a href="menues.php">Menus</a>
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

<h2> Introduce the name of the food you are having and see the level of carbohydrates.

</h2>
<h3> <?php echo $grams; ?> <?php echo $a; ?> <?php echo $q; ?> <?php echo $b; ?> <?php echo $carbs ?> <br>
     <?php echo $q1; ?> <?php echo $b1; ?> <?php echo $carbs1 ?>
    <?php
    function check_image_exists( $url ){
        if ($url){
        $url = trim($url);
        $info = @getimagesize($url);
        return ( (bool) $info );}
        return 0;
    }
    //Usage
    $test_orig_image = $image;
    if( check_image_exists( $test_orig_image ) ){
        echo "<img src=$image  width='200' height='100'>";
    }
    ?>


</h3>

<div class="container center">

<div  class="container-left">


    <form id="search" method="POST"  class="input-group">

            <input autocomplete="off" type="text" name="gramsfood" class="input-field" placeholder="100g" >
			<input autocomplete="off" type="text" name="searchfood" class="input-field" placeholder="Food name" >


        <input type="submit" name="search" value="Search" class="btn brand z-depth-0" style="background-color:#8cbc54 !important;">
        Search </button>

        <h2>
            <div>


                <div class="red-text" > <p style="color:indianred;"><b> <?php echo $errors['searchfood']; ?></b></p></div>
            </div>
        </h2>

    </form>
</div>


<div  class="container-right">


    <form id="search" method="POST"  class="input-group">


        <input autocomplete="off" type="text" name="searchfood1" class="input-field" placeholder="Barcode" >


        <input type="submit" name="search_code" value="Search" class="btn brand z-depth-0" style="background-color:#8cbc54 !important;">
        Search </button>

        <h2>
            <div>


                <div class="red-text" > <p style="color:indianred;"><b> <?php echo $errors['searchfood1']; ?></b></p></div>
            </div>
        </h2>

    </form>
</div>
</div>


<div class ="container" style="position: relative; bottom: 0; align-content: center; margin-top: 250px;" >
    <h2><?php echo $try?> </h2>
    <div class="row" >
        <?php $i=0; ?>
        <?php foreach($stack as $stack){ ?>

            <div class="col s6 md3" >
                <div class="card z-depth-0" style="margin-color:#8cbc54 !important;  border-style: solid !important;">
                    <div class="card-content center">
                        <h6><?php echo htmlspecialchars($stack); ?></h6>
                        <h6><?php echo "Carbs: ".$stackcarbs[$i]; ?></h6>

                        <?php $i=$i+1;?>

                    </div>
                </div>
            </div>

        <?php } ?>
    </div></div>

</body>
</html>
