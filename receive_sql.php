<?php
include 'connexion.php'; // Fichier de configuration avec les infos de la base de données

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_FILES['file'])) {
    $file = $_FILES['file']['tmp_name'];

  
    
    if ($conn->connect_error) {
        die("Échec de la connexion : " . $conn->connect_error);
    }

    // Supprimer toutes les tables existantes
    if ($result = $conn->query("SHOW TABLES")) {
        while ($row = $result->fetch_array(MYSQLI_NUM)) {
            $conn->query('DROP TABLE IF EXISTS '.$row[0]);
        }
    }

    // Importer le nouveau fichier .sql
    $command = "mysql --host={$servername} --user={$username} --password={$password} {$dbname} < {$file}";
    system($command, $output);
    if ($output == 0) {
        echo "Importation réussie.";
    } else {
        echo "Erreur lors de l'importation.";
    }

    // Nettoyer : supprimer le fichier temporaire
    unlink($file);
} else {
    echo "Aucun fichier reçu.";
}
?>
