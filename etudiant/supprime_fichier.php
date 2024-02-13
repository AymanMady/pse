
<?php 
 session_start() ;
 $email = $_SESSION['email'];
 if($_SESSION["role"]!="etudiant"){
     header("location:../authentification.php");
 }

include_once "../connexion.php";
$id_sous = $_GET['id_sous'];
$id_matiere = $_GET['id_matiere'];
$color = $_GET['color'];
$id_semestre = $_GET['id_semestre'];
$id_file=$_GET['id_file'];
$nom_file="SELECT nom_fichiere FROM `fichiers_reponses` WHERE id_fich_rep = $id_file ";
$nom_file1=mysqli_query($conn,$nom_file);
$row_nom_file=mysqli_fetch_assoc($nom_file1);
$nom_file2=$row_nom_file['nom_fichiere'];
$sql="DELETE FROM fichiers_reponses WHERE nom_fichiere = '$nom_file2' ";
$fileName = "../admin/backup_queries.sql";
                $textToFile = $sql . ";\n";
                file_put_contents($fileName, $textToFile, FILE_APPEND);
$resul=mysqli_query($conn,$sql);
if($resul){
    $_SESSION['suppression_reussi'] = true ;
    header("location:reponse_etudiant.php?id_sous=$id_sous&id_matiere=$id_matiere&color=$color&id_semestre=$id_semestre");
}
?>
