<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION["role"] != "ens") {
    header("Location: authentification.php");
}

include_once "../connexion.php";
$titre = $descri = $personC = $date_debut = $date_fin = $id_matiere = $type = '';


unset($_SESSION['test']);

    $stmt = $conn->prepare("INSERT INTO `soumission` (`titre_sous`, `description_sous`, `person_contact`, `id_ens`, `date_debut`, `date_fin`, `valide`, `status`, `id_matiere`, `id_type_sous`) VALUES (?, ?, ?, (SELECT id_ens FROM enseignant WHERE email = ?), ?, ?, 0, 0, ?, ?)");
    if ($stmt) {
      
        $stmt->bind_param("ssssssss",  $_SESSION['titre'],  $_SESSION['description_sous'], $personC,  $_SESSION['personC'],  $_SESSION['date_debut'],  $_SESSION['date_fin'],$_SESSION['id_matiere'], $_SESSION['type']);

     
        if ($stmt->execute()) {
            $_SESSION['ajout_reussi'] = true;
            $id_sous = $conn->insert_id;
        } else {
            $_SESSION['ajout_reussi'] = false;
            $_SESSION['erreur_message'] = "Erreur lors de l'insertion dans la base de données: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['ajout_reussi'] = false;
        $_SESSION['erreur_message'] = "Erreur de préparation de la requête SQL: " . $conn->error;
    }
    header("Location: soumission_en_ligne.php");

?>