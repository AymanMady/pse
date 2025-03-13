<?php
session_start();
$email = $_SESSION['email'];
$id_matiere = $_GET['id_matiere'];
$color = $_GET['color'];
if ($_SESSION["role"] != "etudiant") {
    header("location:../authentification.php");
}


include "nav_bar.php";


?>

<title>Detaille de la soumission</title>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="../JS/sweetalert2.js"></script>
<link rel="stylesheet" href="CSS/cronometre.css">

<?php
include_once "../connexion.php";
if (!empty($_GET['id_sous'])) {
    $id_sous = $_GET['id_sous'];
} else {
    $id_sous = $_SESSION['id_sous'];
}
// biblioteque de message email

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';


$id_semestre = $_GET['id_semestre'];


// ferification si touts les eturdient sont rapondu a le somission 



$req_num_rep = mysqli_query($conn, "select count(*) as num_rep from reponses where id_sous = $id_sous ");

$req_num_insc = mysqli_query($conn, "select count(*) as num_insc from  inscription,matiere,soumission where inscription.id_matiere=matiere.id_matiere and matiere.id_matiere=soumission.id_matiere and  id_sous = $id_sous");
if ($req_num_rep && $req_num_insc) {

    $row_num_rep = mysqli_fetch_assoc($req_num_rep);
    $row_num_insc = mysqli_fetch_assoc($req_num_insc);

    $ens_email = mysqli_query($conn, "select email from enseignant WHERE enseignant.id_ens=(SELECT soumission.id_ens FROM soumission WHERE id_sous =$id_sous)");
    $email_ens = mysqli_fetch_assoc($ens_email);
    if ($row_num_insc['num_insc'] == $row_num_rep['num_rep']) {
        $is_somission_valid = mysqli_fetch_assoc(mysqli_query($conn, "SELECT valide FROM `soumission` WHERE id_sous = $id_sous"));
        if ($is_somission_valid != 0) {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'nodenodeemail@gmail.com';
            $mail->Password = 'dczxmfqzwjqjeuzp';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            $mail->setFrom('nodenodeemail@gmail.com');
            // $mail->addAddress("$email_ens");
            $mail->addAddress("22086@supnum.mr");
            $mail->isHTML(true);
            $mail->Subject = "reponse des etudients";
            $mail->Body = "tout les etudiants ont repondu a les soumission ";
            $mail->send();
            if ($mail->send()) {
                mysqli_query($conn, "UPDATE `soumission` SET `valide`=1 WHERE id_sous =$id_sous");
            }
        }
    }
}


$req_detail = "SELECT * FROM soumission  WHERE id_sous = $id_sous and (status=0 or status=1)  ";
$req = mysqli_query($conn, $req_detail);
mysqli_num_rows($req);
$row = mysqli_fetch_assoc($req);

# Rêquete pour récupere la date de fin
$sq_date = "select date_fin from soumission where id_sous = '$id_sous' ";
$req_date = mysqli_query($conn, $sq_date);
$row_date = mysqli_fetch_assoc($req_date);

?>
<div class="content-wrapper">
    <div class="content" style="height:70px;">

        <div class="page-header" style="height:100px;">
            <div style="display:flex;justify-content:space-bettwen;width:100%;height:100px;">
                <div style="width:100%;">
                    <h3 class="page-title">
                        <span class="page-title-icon bg-gradient-primary text-white me-2">
                            <i class="mdi mdi-home"></i>
                        </span> <a href="choix_semestre.php">Accueil</a> / <a href="index_etudiant.php?id_semestre=<?php echo  $id_semestre ?>"><?php echo "S" . $id_semestre ?></a> / <a href="soumission_etu_par_matiere.php?id_semestre=<?php echo  $id_semestre ?>""><?php echo $_SESSION['nom_mat'] ?></a>  / <a href=" #"><?php echo $row['titre_sous']; ?></a>
                    </h3>

                </div>


                <div class="col-md-2 " style="width:300px ;height:100px">
                    <div class="card">
                        <div class="card-body " style="padding:30px">
                            <?php if (strtotime($row_date['date_fin']) - time() <= 600) { ?>
                                <div class="countdown">
                                    <div class="box">
                                        <span class="num btn-gradient-danger" id="days"></span>
                                        <span class="btn-gradient-danger text">Jours</span>
                                    </div>
                                    <div class="box">
                                        <span class="num btn-gradient-danger" id="hours"></span>
                                        <span class="btn-gradient-danger text">Heurs</span>
                                    </div>
                                    <div class="box">
                                        <span class="num btn-gradient-danger" id="minutes"></span>
                                        <span class="btn-gradient-danger text">Minutes</span>
                                    </div>
                                    <div class="box">
                                        <span class="num btn-gradient-danger" id="seconds"></span>
                                        <span class="btn-gradient-danger text">Secondes</span>
                                    </div>
                                </div>
                            <?php
                            } else {
                            ?>
                                <div class="countdown">
                                    <div class="box">
                                        <span class="num btn-gradient-info" id="days"></span>
                                        <span class="btn-gradient-info text">Jours</span>
                                    </div>
                                    <div class="box">
                                        <span class="num btn-gradient-info" id="hours"></span>
                                        <span class="btn-gradient-info text">Heurs</span>
                                    </div>
                                    <div class="box">
                                        <span class="num btn-gradient-info" id="minutes"></span>
                                        <span class="btn-gradient-info text">Minutes</span>
                                    </div>
                                    <div class="box">
                                        <span class="num btn-gradient-info" id="seconds"></span>
                                        <span class="btn-gradient-info text">Secondes</span>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="row">
            <h3 class="page-title">Dètails sur la soumission <?php echo $row['titre_sous']; ?> </h3><br><br>
            <?php

            if (isset($_SESSION['temp_fin']) && ($_SESSION['temp_fin'] === true)) {
                echo "<div class='alert alert-danger' id='success-alert' >
                L'heure spécifiée pour l'examen est déjà écoulée.
                                            </div>";

                // Supprimer l'indicateur de succès de la session
                unset($_SESSION['temp_fin']);
            }
            if (isset($_SESSION['temp_finni']) && ($_SESSION['temp_finni'] === true)) {
                echo "<div class='alert alert-danger' id='success-alert' >
                                L'enregistrement précédent n'a pas été pris en compte car le temps imparti était écoulé.
                                </div>";

                // Supprimer l'indicateur de succès de la session
                unset($_SESSION['temp_finni']);
            }
            if (isset($_SESSION['modification_fin']) && ($_SESSION['modification_fin'] === true)) {
                echo "<div class='alert alert-danger' id='success-alert' >
                            L'envoi du message a échoué car le temps a expiré.
                            </div>";

                // Supprimer l'indicateur de succès de la session
                unset($_SESSION['modification_fin']);
            }
            ?>

            <div class="col-md-6 grid-margin">
                <div class="card">
                    <div class="card-body mb-4">
                        <h2 class="card-title">L'annonce jointe à la soumission:</h2>
                        <?php

                        if (strtotime(gmdate("Y-m-d H:i:s")) >= strtotime($row['date_fin'])) {
                            echo ' <div class="alert alert-danger mt-3" id="success-alert">
                                <strong>La date spécifiée pour cette soumission a été terminé.</strong>
                                </div>';
                        }
                        ?>

                        <h4>
                            <p><?php echo "<strong>Titre : </strong>" . $row['titre_sous']; ?></p>
                            <div style="overflow: auto; height: 70px;">
                                <?php echo "<strong>Description :&nbsp; </strong>" . $row['description_sous']; ?>
                            </div>
                            <br>
                            <p><?php echo "<strong>Date de  début : </strong>" . $row['date_debut']; ?></p>
                            <p><?php echo "<strong>Date de  fin : </strong>" . $row['date_fin']; ?></p>
                            <p><?php echo "<strong>Pour plus des informations : </strong>" . $row['person_contact']; ?></p>
                        </h4>
                        <p class="card-title mt-4">Les fichier(s) de la soumission: </p>
                        <?php
                        $sql2 = "select * from fichiers_soumission where id_sous='$id_sous' ";
                        $req2 = mysqli_query($conn, $sql2);
                        if (mysqli_num_rows($req2) == 0) {
                        ?>
                            <?php
                            echo "Il n'y a pas des fichier ajouter !";
                            ?>
                            <?php
                        } else {
                            while ($row2 = mysqli_fetch_assoc($req2)) {
                                $file_name = $row2['nom_fichier'];
                            ?>
                                <blockquote class="blockquote blockquote-info" style="border-radius:10px;">
                                    <p><strong><?= $file_name ?> </strong></p>

                                    <?php
                                    $test = explode(".", $file_name);
                                    if ($test[1] == "pdf") {
                                    ?>
                                        <a class="btn btn-inverse-info btn-sm " href="open_file.php?file_name=<?= $file_name ?>&id_sous=<?= $id_sous ?>" style="text-decoration: none;">Visualiser</a>
                                    <?php
                                    } else {
                                    ?>
                                        <a class="btn btn-inverse-info btn-sm" title="Les fichiers d'extension pdf sont les seuls que vous pouvez visualiser 😒😒.">Visualiser</a>
                                    <?php
                                    }

                                    ?>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a class="btn btn-inverse-info btn-sm " href="telecharger_fichier.php?file_name=<?= $file_name ?>&id_sous=<?= $id_sous ?>" style="text-decoration: none;">Télécharger</a>
                                </blockquote>
                                <br>
                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <p class="card-title">Votre Réponse :</p>
                        <?php
                        $sql2 = "SELECT * FROM fichiers_reponses, reponses, etudiant WHERE fichiers_reponses.id_rep = reponses.id_rep AND reponses.id_etud = etudiant.id_etud AND email = '$email' AND reponses.id_sous = '$id_sous';";
                        $req2 = mysqli_query($conn, $sql2);
                        if (mysqli_num_rows($req2) == 0) {
                        ?>
                            <?php
                            echo "Il n'y a pas de fichier ajouté !";
                            ?>
                            <?php
                        } else {
                            while ($row2 = mysqli_fetch_assoc($req2)) {
                            ?>
                                <?php
                                $file_name = $row2['nom_fichiere'];
                                $id_rep = $row2['id_rep'];
                                ?>
                                <blockquote class="blockquote blockquote-info" style="border-radius:10px;">

                                    <p><strong><?= $row2['nom_fichiere'] ?> </strong></p>
                                    <?php
                                    $test = explode(".", $file_name);
                                    if ($test[1] == "pdf") {
                                    ?>
                                        &nbsp;<a class="btn btn-inverse-info btn-sm" href="open_file.php?file_name=<?= $file_name ?>&id_rep=<?= $id_rep ?>">Visualiser</a>
                                    <?php
                                    } else {
                                    ?>
                                        <a class="btn btn-inverse-info btn-sm" title="Les fichiers d'extension pdf sont les seuls que vous pouvez visualiser 😒😒.">Visualiser</a>
                                    <?php
                                    }
                                    ?>
                                    <a class="btn btn-inverse-info btn-sm ms-4" href="telecharger_fichier.php?file_name=<?= $file_name ?>&id_rep=<?= $id_rep ?>">Télécharger</a>
                                </blockquote>
                                <br>
                        <?php
                            }
                        }
                        ?>
                        </ul>
                    </div>
                </div>
            </div>
            <?php
            $req_detail = "SELECT `status`, `date_fin`   FROM soumission   WHERE id_sous = $id_sous  ";
            $req = mysqli_query($conn, $req_detail);
            $row_status = mysqli_fetch_assoc($req);

            date_default_timezone_set('GMT');
            $date = date('Y-m-d H:i:s');


            // verifier que la date ne pas encore terminer
            $req_detail3 = "SELECT  *   FROM soumission   WHERE id_sous = $id_sous and (status=0 or status=1)  and date_fin > '$date'  ";
            $req3 = mysqli_query($conn, $req_detail3);


            $sql = "select * from reponses where id_sous = '$id_sous' and id_etud = (select id_etud from etudiant where email = '$email') ";
            $req = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($req);
            $req_detail2 = "SELECT  `autoriser`,`id_type_sous`  FROM soumission , demande  WHERE soumission.id_sous = $id_sous and (status=0 or status=1)  and soumission.id_sous = demande.id_sous and demande.id_etud = (select id_etud from etudiant where email = '$email') and autoriser = 1 ";
            $req2 = mysqli_query($conn, $req_detail2);
            $row2 = mysqli_fetch_assoc($req2);
            $row6 = mysqli_fetch_assoc($req3);



            if (mysqli_num_rows($req3) > 0 and  $row_status['status'] != 1 and  $row_status['status'] != 2) {
                if (mysqli_num_rows($req2) == 0 or $row2['autoriser'] == 0) {
                    if (mysqli_num_rows($req) == 0) {
                        $_SESSION['autorisation'] = true;
                        ?>
                        <p>
                            <a href="automatisation.php?id_sous=<?= $id_sous ?>&id_matiere=<?php echo $id_matiere ?>&color=<?php echo $color ?>&id_semestre=<?php echo $id_semestre ?>" class="btn btn-primary">Rendre le travail</a>
                        </p>
                    <?php
                    } else {
                    ?>
                        <?php
                        if ($row['confirmer'] ==  1 ) {
                            if($row6['id_type_sous']==3){
                            ?> <p>
                                <a href="demande_modifier.php?id_sous=<?= $id_sous ?>&id_matiere=<?php echo $id_matiere ?>&color=<?php echo $color ?>&id_semestre=<?php echo $id_semestre ?>" class="btn btn-primary">Demande de faire une modification</a>
                            </p>
                            <?php
                            }
                            else{
                                echo "";
                            }
                        ?>  
                        <?php
                        } else {
                            $_SESSION['autorisation'] = true;
                        ?>
                            <p>
                                <a href="reponse_etudiant.php?id_sous=<?= $id_sous ?>&id_matiere=<?php echo $id_matiere ?>&color=<?php echo $color ?>&id_semestre=<?php echo $id_semestre ?>" class="btn btn-primary">Modifier le travail</a>
                            </p>
                        <?php
                        }
                    }
                } else {
                    if (mysqli_num_rows($req) == 0) {
                        $_SESSION['autorisation'] = true;
                        ?>
                        <p>
                            <a href="automatisation.php?id_sous=<?= $id_sous ?>&id_matiere=<?php echo $id_matiere ?>&color=<?php echo $color ?>&id_semestre=<?php echo $id_semestre ?>" class="btn btn-primary">Rendre le travail</a>
                        </p>
                    <?php
                    } else {
                    ?>
                        <?php
                        if ($row['confirmer'] ==  1) {
                        ?>
                            <p>
                                <a href="demande_modifier.php?id_sous=<?= $id_sous ?>&id_matiere=<?php echo $id_matiere ?>&color=<?php echo $color ?>&id_semestre=<?php echo $id_semestre ?>" class="btn btn-primary">Demande de faire une modification</a>
                            </p>
                        <?php
                        } else {
                            $_SESSION['autorisation'] = true;
                        ?>
                            <p>
                                <a href="reponse_etudiant.php?id_sous=<?= $id_sous ?>&id_matiere=<?php echo $id_matiere ?>&color=<?php echo $color ?>&id_semestre=<?php echo $id_semestre ?>" class="btn btn-primary">Modifier le travail</a>
                            </p>
                        <?php
                        }
                    }
                }
            }


            ?>
        </div>
        <?php
        if (isset($_SESSION['ajout_reussi']) && $_SESSION['ajout_reussi'] === true) {
            echo "<script>
                    Swal.fire({
                        title: 'Ajout réussi !',
                        text: 'La réponse a été ajouté avec succès.',
                        icon: 'success',
                        confirmButtonColor: '#3099d6',
                        confirmButtonText: 'OK'
                    });
                    </script>";
            // Supprimer l'indicateur de succès de la session
            unset($_SESSION['ajout_reussi']);
        }
        ?>
        <?php
        if (isset($_SESSION['demande_reussi']) && $_SESSION['demande_reussi'] === true) {
            echo "<script>
                    Swal.fire({
                        title: 'Démande réussi !',
                        text: 'La démande a été envoyer avec succès.',
                        icon: 'success',
                        confirmButtonColor: '#3099d6',
                        confirmButtonText: 'OK'
                    });
                    </script>";

            // Supprimer l'indicateur de succès de la session
            unset($_SESSION['demande_reussi']);
        }
        ?>
    </div>
</div>
</div>
</div>
</div>


<?php
$sql = "select date_fin from soumission where id_sous = '$id_sous' ";
$req = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($req);
$endDate = date("M d, Y H:i:s", strtotime($row['date_fin']));
?>

<script>
    // Définir la date de fin du compte à rebours (format : "Mois Jour, Année Heures:Minutes:Secondes")
    const endDate = new Date("<?php echo $endDate; ?>").getTime();

    // Mettre à jour le compte à rebours chaque seconde
    const countdownInterval = setInterval(function() {
        // Obtenir la date et l'heure actuelles
        const now = new Date().getTime();

        // Calculer la différence entre la date de fin et la date actuelle
        const timeRemaining = endDate - now;

        // Calculer les jours, heures, minutes et secondes restants
        const days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);

        // Afficher le compte à rebours dans les éléments HTML avec les IDs correspondants
        document.getElementById("days").innerHTML = formatTime(days);
        document.getElementById("hours").innerHTML = formatTime(hours);
        document.getElementById("minutes").innerHTML = formatTime(minutes);
        document.getElementById("seconds").innerHTML = formatTime(seconds);

        // Vérifier si le compte à rebours a atteint zéro
        if (timeRemaining < 0) {
            clearInterval(countdownInterval); // Arrêter le compte à rebours lorsque le temps est écoulé
            document.getElementById("days").innerHTML = "00";
            document.getElementById("hours").innerHTML = "00";
            document.getElementById("minutes").innerHTML = "00";
            document.getElementById("seconds").innerHTML = "00";
        }
    }, 1000);

    // Fonction pour formater le temps avec un zéro en ajoutant un zéro devant les chiffres inférieurs à 10
    function formatTime(time) {
        return time < 10 ? "0" + time : time;
    }
</script>