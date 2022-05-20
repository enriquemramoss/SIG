<?php
header("Content-Type: text/html;charset=utf-8");
function conectar()
{
  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "sig";
  $conn = new mysqli($servername, $username, $password, $dbname);
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  } 
  //echo "Connected successfully<br/>";
  return $conn;
}
?>