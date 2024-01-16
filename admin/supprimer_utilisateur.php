<?php
  include_once "../connexion.php";

  session_start() ;
$email = $_SESSION['email'];
if($_SESSION["role"] != "admin"){
    header("location:authentification.php");
}

  $id_user= $_GET['id_user'];
  $req = mysqli_query($conn , "DELETE FROM utilisateur WHERE id_user = $id_user");
  header("Location:utilisateurs.php");
  $_SESSION['supp_reussi'] = true;
?>