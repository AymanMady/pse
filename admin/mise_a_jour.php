<?php
session_start();
$email = $_SESSION['email'];
if ($_SESSION["role"] != "admin") {
    header("location:../authentification.php");
}
include_once 'nav_bar.php';
include_once "../connexion.php";

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
    
                            <!-- <a href="../test_compresse.php" class="btn btn-gradient-danger btn-icon-text"><i class="mdi mdi-sync btn-icon-prepend"></i> Mise à jour </a> -->
                            </div>
                        </div>
                    </div>
                </div>
            <!-- </div>
        </div>
    </div> -->
<?php 
// if (isset($_SESSION['envoie_reussi']) && $_SESSION['envoie_reussi'] === true) {
//     echo "<script>
//     Swal.fire({
//         title: 'Mise à jour réussi !',
//         text: 'Les données a été envoyer au serveur avec succès.',
//         icon: 'success',
//         confirmButtonColor: '#3099d6',
//         confirmButtonText: 'OK'
//     });
//     </script>";
?>




<?php


function collecterDonnees($conn)
{
    // Définition des requêtes SQL pour chaque table
    $queries = [
        "SELECT * FROM soumission WHERE id_sous = (SELECT MAX(id_sous) FROM soumission)" => "soumission",
        "SELECT reponses.* FROM reponses WHERE id_sous = (SELECT MAX(id_sous) FROM soumission)" => "reponses",
        "SELECT * FROM fichiers_reponses WHERE id_rep IN (SELECT id_rep FROM reponses WHERE id_sous = (SELECT MAX(id_sous) FROM soumission))" => "fichiers_reponses",
        "SELECT * FROM fichiers_soumission WHERE id_sous = (SELECT MAX(id_sous) FROM soumission)" => "fichiers_soumission"
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
            $nom_fichier_json = "$table.json";

            // Écrit les données JSON dans le fichier
            file_put_contents($nom_fichier_json, $donnees_json);

        } else {
            echo "La requête pour la table '$table' a échoué : " . mysqli_error($conn);
        }
    }

    // Affiche les comptes
    
    
    

    ?>


                        <div class="card-body">
                            <h4 class="card-title">Mise à jour :</h4>
                            <div style="display: flex; justify-content: space-between;">
                            <p><?php echo "<b>Nombre de soumissions insérées :</b>  $nombre_soumissions.<br>"; ?></p>
                            <p><?php echo "<b>Nombre de réponses insérées : </b>$nombre_reponses.<br>"; ?></p><br>
                            <p><?php echo "<b>Nombre de fichiers de réponses insérés :</b> $nombre_fichiers_reponses.<br>"; ?></p>
                            <p><?php echo "<b>Nombre de fichiers de soumission insérés :</b> $nombre_fichiers_soumission.<br>"; ?></p>
    
                            <!-- <a href="../test_compresse.php" class="btn btn-gradient-danger btn-icon-text"><i class="mdi mdi-sync btn-icon-prepend"></i> Mise à jour </a> -->
                            </div>

                            <?PHP 
                            if($nombre_soumissions>0 and $nombre_reponses>0 ){
                            //     echo "<script>
                            //     Swal.fire({
                            //         title: 'Confirmation',
                            //         text: 'Voulez-vous vraiment envoyer ces données au serveur ?',
                            //         icon: 'question',
                            //         confirmButtonColor: '#3099d6',
                            //         confirmButtonText: 'OK'
                            //     }).then((result) => {
                            //         if (result.isConfirmed) {
                            //             window.location.href = 'send_data.php'; // Rediriger vers send_data.php si l'utilisateur clique sur 'OK'
                            //         }
                            //     });
                            // </script>";
                                echo '<a href="insert_au_server.php"class="btn btn-gradient-danger btn-icon-text" name="envoi_donner"><i class="mdi mdi-sync btn-icon-prepend"></i>envoyer au serveur en ligne ? </a>';}
                            
                            if(isset($_POST['envoi_donner'])){
                                echo "<script>window.location.href = 'index.php'</script>";
                                
                        }
                            ?>
</div>
    </div>
</div>

<?php
}
if (isset($_POST['collecter'])) {
    collecterDonnees($conn);
}

?>


