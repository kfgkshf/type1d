<?php

	include('server.php');



?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome</title>
  <link rel = "stylesheet" href="style2.css">
</head>
<body>
  <div class="box">


        <img style="display: block; margin-left: auto; margin-right: auto; " src="logo-no-background.png" width="200" height="100">


    <form id="login" action="index.php" method="POST"  class="input-group">

        <div class="red-text" > <p style="color:indianred;"><b> <?php echo $errors['registration']; ?></b> </p></div>
			<input autocomplete="off" type="text" name="userlogin" class="input-field" placeholder="User Id" value="<?php echo htmlspecialchars($userlogin) ?>" required>
			<div class="red-text"><?php echo $errors['userlogin']; ?></div>
	
			<input autocomplete="off" type="password" name="passwordlogin" class="input-field" placeholder="User Password" value="<?php echo htmlspecialchars($passwordlogin) ?>" required>
        <div class="red-text"><p style="color:indianred;"><b><?php echo $errors['passwordlogin']; ?></b></p></div>


	<button type="submit" name="login" value="Login" class="submit-button"> Login </button>
        <hr style = "width:180%">
        <p > New user? <button type="button" style=" background-color: transparent; border:none"onclick="register()"> <div class="heading"><b>Press here</b></div></button> </p>
       </form>


    <form id="register" action="index.php" method="POST" class="input-group">


			<input autocomplete="off" type="text" name="email" class="input-field2" placeholder="User Email" required value="<?php echo htmlspecialchars($email) ?>"required>
        <div class="red-text"><p style="color:indianred;"><b><?php echo $errors['email']; ?></b></p></div>

			<input autocomplete="off" type="text" name="user" class="input-field2" placeholder="User Id" value="<?php echo htmlspecialchars($user) ?>" required>
        <div class="red-text"><p style="color:indianred;"><b><?php echo $errors['user']; ?></b></p></div>
	
			<input autocomplete="off" type="password" name="password" class="input-field2" placeholder="User Password" value="<?php echo htmlspecialchars($password) ?>" required>


			<input autocomplete="off" type="password" name="repeatpassword" class="input-field2" placeholder="Repeat Password" value="<?php echo htmlspecialchars($repeatpassword) ?>" required>
        <div class="red-text"><p style="color:indianred;"><b><?php echo $errors['password']; ?></b></p></div>



        <button type="submit" name="submit" value="Submit" class="submit-button2"> Register </button>
        <p> <button type="button" style=" background-color: transparent;  border:none"onclick="login()"> <div class="heading"><b>Go back to the login page</b></div></button> </p>

    </form>
  </div>


<script>
  function register(){
    document.getElementById("login").style.left = "-400px";
    document.getElementById("register").style.left = "50px";
    document.getElementById("button").style.left = "110px";
  }
  function login(){
    document.getElementById("login").style.left = "50px";
    document.getElementById("register").style.left = "450px";
    document.getElementById("button").style.left = "0px";
  }
</script>



</body>
</html>