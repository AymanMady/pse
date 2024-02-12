<?php
session_start();

// Vérifier si l'utilisateur est authentifié et a le rôle 'ens'
if (!isset($_SESSION['email']) || $_SESSION["role"] != "ens") {
    header("Location: authentification.php");
    exit(); // Assurez-vous de quitter le script après une redirection pour ne pas exécuter le reste du code
}

include_once "../connexion.php"; // Assurez-vous que ce chemin d'accès est correct

// Initialisation des variables
$titre = $description_sous = $personC = $date_debut = $date_fin = $id_matiere = $type = '';

// Suppression de la variable de session 'test' si elle existe
unset($_SESSION['test']);

// Échappement des valeurs pour sécuriser la requête
$titre = mysqli_real_escape_string($conn, $_SESSION['titre']);
$description_sous = mysqli_real_escape_string($conn, $_SESSION['description_sous']);
$personC = mysqli_real_escape_string($conn, $_SESSION['personC']); // Assurez-vous que cette session contient l'email ou le contact
$date_debut = mysqli_real_escape_string($conn, $_SESSION['date_debut']);
$date_fin = mysqli_real_escape_string($conn, $_SESSION['date_fin']);
$id_matiere = mysqli_real_escape_string($conn, $_SESSION['id_matiere']);
$type = mysqli_real_escape_string($conn, $_SESSION['type']);

// Construction de la requête SQL avec les valeurs échappées
$query = "INSERT INTO `soumission` (`titre_sous`, `description_sous`, `person_contact`, `id_ens`, `date_debut`, `date_fin`, `valide`, `status`, `id_matiere`, `id_type_sous`) VALUES ('$titre', '$description_sous', '$personC', (SELECT id_ens FROM enseignant WHERE email = '$personC'), '$date_debut', '$date_fin', 0, 0, '$id_matiere', '$type')";

// Exécution de la requête
if (mysqli_query($conn, $query)) {
    $query1 = "INSERT INTO `soumission` (`titre_sous`, `description_sous`, `person_contact`, `id_ens`, `date_debut`, `date_fin`, `valide`, `status`, `id_matiere`, `id_type_sous`) VALUES ('$titre', '$description_sous', '$personC', (SELECT id_ens FROM enseignant WHERE email = '$personC'), '$date_debut', '$date_fin', 0, 0, '$id_matiere', '$type')";
    $fileName = "../backup_queries.sql";
    $textToFile =$query1 . ";\n";
    file_put_contents($fileName, $textToFile, FILE_APPEND);
    $_SESSION['ajout_reussi'] = true;
    $id_sous = mysqli_insert_id($conn); // Récupère l'ID de la dernière ligne insérée
} else {
    $_SESSION['ajout_reussi'] = false;
    $_SESSION['erreur_message'] = "Erreur lors de l'insertion dans la base de données: " . mysqli_error($conn);
}

// Redirection vers la page de soumission en ligne
header("Location: soumission_en_ligne.php");
exit();
?>
