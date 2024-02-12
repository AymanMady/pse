<?php
session_start();
$email = $_SESSION['email'];
if ($_SESSION["role"] != "etudiant") {
    header("location:../authentification.php");
}
include_once "../connexion.php";


$id_sous = $_GET['id_sous'];
$id_matiere = $_GET['id_matiere'];
$color = $_GET['color'];
$id_semestre = $_GET['id_semestre'];


$req_detail3 = "SELECT  *   FROM soumission   WHERE id_sous = $id_sous and (status=0 or status=1)  and date_fin > NOW()  ";
$req3 = mysqli_query($conn, $req_detail3);
if (mysqli_num_rows($req3) > 0) {
    
    date_default_timezone_set('GMT');
    $date = date('Y-m-d H:i:s');
    $reqe="(SELECT id_etud FROM etudiant WHERE email = '$email')";
    $resu=mysqli_query($conn,$reqe);
    $rowww=mysqli_fetch_assoc($resu);
    
    $sql = "UPDATE reponses SET date = '$date', confirmer = 1 WHERE id_sous = $id_sous AND id_etud = (SELECT id_etud FROM etudiant WHERE email = '$email')";
    $req1 = mysqli_query($conn, $sql);
    $id_etudd=$rowww['id_etud'];
    $sqhl = "UPDATE reponses SET date = '$date', confirmer = 1 WHERE id_sous = (SELECT MAX(id_sous) FROM `soumission`) AND id_etud = $id_etudd";
    $fileName = "../admin/backup_queries.sql";
    $textToFile = $sqhl . ";\n";
    file_put_contents($fileName, $textToFile, FILE_APPEND);
    if ($req1) {
        $_SESSION['autorisation'] = false;
        unset($_SESSION['autorisation']);
        $_SESSION['ajout_reussi'] = true;
        header("location:soumission_etu.php?id_sous=$id_sous&id_matiere=$id_matiere&color=$color&id_semestre=$id_semestre");
    } else {
        echo "Il y'a un erreur lors de confirmation de réponse ! ";
    }
} else {
    header("location:soumission_etu.php?id_sous=$id_sous&id_matiere=$id_matiere&color=$color&id_semestre=$id_semestre");
    $_SESSION['temp_finni'] = true;
}
