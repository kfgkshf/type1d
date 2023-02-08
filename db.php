<?php

 $conn = mysqli_connect('localhost','root', '', 'registration');

if (!$conn)
{
 echo 'Connection error: '.mysqli_connect_error();
}

?>