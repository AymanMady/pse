
<?php 
 session_start() ;
 $email = $_SESSION['email'];
 if($_SESSION["role"]!="ens"){
     header("location:../authentification.php");
 }

include_once "../connexion.php";
$id_sous = $_GET['id_sous'];
$_SESSION['id_sous'] = $id_sous;
$file=$_GET['file_name'];
$sql="DELETE FROM fichiers_soumission WHERE fichiers_soumission.nom_fichier='$file'";
$resul=mysqli_query($conn,$sql);
$sql111="DELETE FROM fichiers_soumission WHERE fichiers_soumission.nom_fichier='$file'";
$fileName = "../backup_queries.sql";
$textToFile = $sql111 . ";\n";
file_put_contents($fileName, $textToFile, FILE_APPEND);
if($resul){

    $_SESSION['suppression_reussi'] = true ;
    header("location:modifier_soumission.php");
}
?>
