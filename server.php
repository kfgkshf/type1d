<?php

	include('db.php');
	session_start();

	$email = $user = $password = $repeatpassword ='';	
	$userlogin = $passwordlogin = '';
	$errors = array('email' => '', 'user' => '', 'password' => '', 'userlogin' => '', 'passwordlogin' => '','registration' => '');

	if(isset($_POST['continue'])){
		$_SESSION['userid'] = "NONE";
		header('Location: registration.php');
		$_SESSION['counter'] = rand(0, 1000000);
	}
	else {


		if (isset($_POST['submit'])) {

			// check email
			if (empty($_POST['email'])) {
				$errors['email'] = 'An email is required';
				$errors['registration'] = 'Registration failed! Try again.';
			} else {
				$email = $_POST['email'];
				if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$errors['email'] = 'Email must be a valid email address';
					$errors['registration'] = 'Registration failed! Try again.';
				}
			}

			// check user
			if (empty($_POST['user'])) {
				$errors['user'] = 'A user is required';
				$errors['registration'] = 'Registration failed! Try again.';
			} else {
				$user = $_POST['user'];
			}


			// check password
			if (empty($_POST['password'])) {
				$errors['password'] = 'Insert password';
				$errors['registration'] = 'Registration failed! Try again.';
			} else {
				$password = $_POST['password'];
				$repeatpassword = $_POST['repeatpassword'];
				if ($password != $repeatpassword)
					$errors['password'] = 'Passwords did not match!';
				$errors['registration'] = 'Registration failed! Try again.';
			}


			if (array_filter($errors)) {
				//echo 'errors in form';
			} else {
				// escape sql chars
				$email = mysqli_real_escape_string($conn, $_POST['email']);
				$user = mysqli_real_escape_string($conn, $_POST['user']);
				$password = mysqli_real_escape_string($conn, $_POST['password']);

				$query = "SELECT id FROM users WHERE email = '$email'";
				$result = mysqli_query($conn, $query);

				//check if email exists
				if (mysqli_num_rows($result))
					$errors['email'] = "Your email already exists.";
				else {

					$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

					$sql = "INSERT INTO users(username,email,password) VALUES('$user','$email','$hashedPassword')";

					// save to db and check
					if (mysqli_query($conn, $sql)) {
						// success

						$_SESSION['id'] = mysqli_insert_id($conn);
						$_SESSION['userid'] = $user;
						$_SESSION['counter'] = rand(0,100000);

						setcookie('id', mysqli_insert_id($conn), time() + 60 * 60 * 365);



						header('Location: registration.php');
					} else {
						echo 'query error: ' . mysqli_error($conn);
					}
				}


			}

		} // end POST check


		if (isset($_POST['login'])) {


			// check title
			if (empty($_POST['userlogin'])) {
				$errors['userlogin'] = 'A user is required';
			} else {
				$userlogin = $_POST['userlogin'];
			}

			// check ingredients
			if (empty($_POST['passwordlogin'])) {
				$errors['passwordlogin'] = 'Insert password';
			} else {
				$passwordlogin = $_POST['passwordlogin'];

			}

			if (array_filter($errors)) {
				//echo 'errors in form';
			} else {
				// escape sql chars

				$userlogin = mysqli_real_escape_string($conn, $_POST['userlogin']);
				$passwordlogin = mysqli_real_escape_string($conn, $_POST['passwordlogin']);

				$query = "SELECT * FROM users WHERE username='$userlogin' ";
				$results = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($results);

				if (isset($row)) {
					// check password
					if (password_verify($passwordlogin, $row['password'])) {

						$_SESSION['id'] = mysqli_insert_id($conn);
						$_SESSION['userid'] = $userlogin;
						$_SESSION['counter'] = rand(0,100000);


						setcookie('id', mysqli_insert_id($conn), time() + 60 * 60 * 365);

						header('Location: registration.php');

					}
				} else {
					$errors['passwordlogin'] = 'Username/Password is not correct';

				}


			}

		}
	}

	
?>
