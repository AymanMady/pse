<?php
session_start();
$email = $_SESSION['email'];
if ($_SESSION["role"] != "ens") {
    header("location:../authentification.php");
}
$code=$_GET['code'];
$dat=$_GET['dat'];
include_once "../connexion.php";
include "nav_bar.php";

$id_sousmission = $_SESSION['id_soumission']; // Make sure $id_sousmission is defined

// Function to collect data and write JSON files
function collecterDonnees($conn, $chemin_donnees, $id_sousmission)
{
    // Define the path where JSON files should be created
    $chemin_donnees = rtrim($chemin_donnees, '/') . '/'; // Ensure trailing slash

    // Définition des requêtes SQL pour chaque table
    $queries = [
        "SELECT * FROM soumission WHERE id_sous = $id_sousmission" => "soumission",
        "SELECT reponses.* FROM reponses WHERE id_sous = $id_sousmission" => "reponses",
        "SELECT * FROM fichiers_reponses WHERE id_rep IN (SELECT id_rep FROM reponses WHERE id_sous = $id_sousmission)" => "fichiers_reponses",
        "SELECT * FROM fichiers_soumission WHERE id_sous = $id_sousmission" => "fichiers_soumission"
    ];

    // Initialise les compteurs
    $nombre_soumissions = 0;
    $nombre_reponses = 0;
    $nombre_fichiers_reponses = 0;
    $nombre_fichiers_soumission = 0;

    // Parcours de chaque requête et exécution
    foreach ($queries as $requete => $table) {
        $resultat_requete = mysqli_query($conn, $requete);

        if ($resultat_requete) {
            $donnees = array(); // Initialise un tableau pour stocker les données récupérées
            while ($ligne = mysqli_fetch_assoc($resultat_requete)) {
                // Ajoute chaque ligne au tableau
                $donnees[] = $ligne;
            }

            // Incrémente les compteurs en fonction du nom de la table
            switch ($table) {
                case 'soumission':
                    $nombre_soumissions = count($donnees);
                    break;
                case 'reponses':
                    $nombre_reponses = count($donnees);
                    break;
                case 'fichiers_reponses':
                    $nombre_fichiers_reponses = count($donnees);
                    break;
                case 'fichiers_soumission':
                    $nombre_fichiers_soumission = count($donnees);
                    break;
            }

            // Convertit les données en format JSON
            $donnees_json = json_encode($donnees, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            // Génère le nom du fichier JSON en fonction du nom de la table
            $nom_fichier_json = $chemin_donnees . "$table.json";

            // Écrit les données JSON dans le fichier
            file_put_contents($nom_fichier_json, $donnees_json);

        } else {
            echo "La requête pour la table '$table' a échoué : " . mysqli_error($conn);
        }
    }

    // Affiche les comptes
    echo "<p>Nombre de soumissions insérées : $nombre_soumissions</p>";
    echo "<p>Nombre de réponses insérées : $nombre_reponses</p>";
    echo "<p>Nombre de fichiers de réponses insérés : $nombre_fichiers_reponses</p>";
    echo "<p>Nombre de fichiers de soumission insérés : $nombre_fichiers_soumission</p>";

if($nombre_reponses>0){
$code=$_GET['code'];
$dat=$_GET['dat'];
    ?>
    <a href="../envoie_server.php?code=<?=$code?>&dat=<?=$dat?>" class="btn btn-gradient-primary"> envoyer au serveur en ligne</a>
    <?php
}
}
?>

<title>Mise à jour</title>

<div class="main-panel">
    <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Mise à jour :</h4>
                            <div style="display: flex; justify-content: space-between;">
                            <form method="post">
                                <button type="submit"class="btn btn-gradient-danger btn-icon-text" name="collecter"><i class="mdi mdi-sync btn-icon-prepend"></i>Collecter</button>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<?php 
if (isset($_POST['collecter'])) {

    // Récupérer le chemin des données depuis le formulaire
    $chemin_donnees = '../files/'.$code.'/soumission_'.$dat;
    // Collecter les données
    collecterDonnees($conn, $chemin_donnees, $id_sousmission);
}

?>
</div>
</div>
