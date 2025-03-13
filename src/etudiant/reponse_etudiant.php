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


if (!isset($_SESSION['autorisation']) && $_SESSION['autorisation'] != true) {
    $_SESSION['id_sous'] = $id_sous;
    header("location:soumission_etu.php?id_sous=$id_sous&id_matiere=$id_matiere&color=$color&id_semestre=$id_semestre");
}

$req_detail = "SELECT * FROM soumission  WHERE id_sous = $id_sous ";
$req = mysqli_query($conn, $req_detail);
$row_titre = mysqli_fetch_assoc($req);
?>



<?php
$sql = "select * from reponses where id_sous = ' $id_sous' and id_etud = (select id_etud from etudiant where email = '$email') ";
$req = mysqli_query($conn, $sql);

if (mysqli_num_rows($req) == 0) {

    function test_input($data)
    {
        $data = htmlspecialchars($data);
        $data = trim($data);
        $data = htmlentities($data);
        $data = stripslashes($data);
        return $data;
    }

    if (isset($_POST['button'])) {

        $req_detail3 = "SELECT  *   FROM soumission   WHERE id_sous = $id_sous and (status=0 or status=1)  and date_fin > NOW()  ";
        $req3 = mysqli_query($conn, $req_detail3);
        if (mysqli_num_rows($req3) > 0) {
            $row_soumission = mysqli_fetch_assoc($req3);
            $debut=$row_soumission['date_debut'];

            $dateTime = new DateTime($debut);
            $debut = $dateTime->format('Y-m-d');

            $descri = test_input($_POST['description_sous']);
            $files = $_FILES['file'];
            if (!empty($descri) or !empty($files)) {
                $sql = "INSERT INTO `reponses`(`description_rep`,date, `id_sous`, `id_etud`) VALUES('$descri',NOW(),'$id_sous',(select id_etud from etudiant where email = '$email')) ";

                $req1 = mysqli_query($conn, $sql);

                $id_rep = mysqli_insert_id($conn);
                foreach ($files['tmp_name'] as $key => $tmp_name) {
                    $file_name = $files['name'][$key];
                    $file_tmp = $files['tmp_name'][$key];
                    $file_size = $files['size'][$key];
                    $file_error = $files['error'][$key];
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                    if ($file_error === 0) {
                        // $new_file_name = uniqid('', true) . '.' . $file_ext;
                        $new_file_name =  $file_name;

                        $sql3 = "SELECT matricule FROM etudiant WHERE etudiant.email = '$email'";
                        $code_matiere_result = mysqli_query($conn, $sql3);
                        $row = mysqli_fetch_assoc($code_matiere_result);
                        $matricule = $row['matricule'];

                        $sql3 = "SELECT code FROM matiere,soumission WHERE matiere.id_matiere = soumission.id_matiere and id_sous = $id_sous ";
                        $code_matiere_result = mysqli_query($conn, $sql3);
                        $row = mysqli_fetch_assoc($code_matiere_result);
                        $code_matire = $row['code'];
                        $matricule_directory = '../files/' . $code_matire . '/' . 'soumission_'.$debut . '/' . 'reponses/' .$matricule;


                        // Créer le dossier s'il n'exist pas
                        if (!is_dir($matricule_directory)) {
                            mkdir($matricule_directory, 0777, true);
                        }

                        // Chemin complet 
                        $destination = $matricule_directory . '/' . $new_file_name;
                        move_uploaded_file($file_tmp, $destination);

                        // Insérer les info dans la base de donnéez
                        $sql2 = "INSERT INTO `fichiers_reponses` (`id_rep`, `nom_fichiere`, `chemin_fichiere`) VALUES ($id_rep, '$file_name', '$destination')";
                        $req2 = mysqli_query($conn, $sql2);
                        if ($req1 and $req2) {

                            $_SESSION['id_sous'] = $id_sous;
                            $_SESSION['ajout_reussi'] = true;
                            $_SESSION['enregistre'] = true;
                            header("location:reponse_etudiant.php?id_sous=$id_sous&id_matiere=$id_matiere&color=$color&id_semestre=$id_semestre");
                        }
                    }
                }
            }
        } else {
            $_SESSION['id_sous'] = $id_sous;
            header("location:soumission_etu.php?$id_sous&$id_matiere&$color&$id_semestre");
            $_SESSION['temp_finni'] = true;
        }
    }


    include "nav_bar.php";

    # Rêquete pour récupere la date de fin
    $sq_date = "select date_fin from soumission where id_sous = '$id_sous' ";
    $req_date = mysqli_query($conn, $sq_date);
    $row_date = mysqli_fetch_assoc($req_date);

?>
    <link rel="stylesheet" href="CSS/cronometre.css">

    <div class="content-wrapper">
        <div class="content" style="height:70px;">

            <div class="page-header" style="height:100px;">
                <div style="display:flex;justify-content:space-bettwen;width:100%;height:100px;">
                    <div style="width:100%;">
                        <h3 class="page-title">
                            <span class="page-title-icon bg-gradient-primary text-white me-2">
                                <i class="mdi mdi-home"></i>
                            </span> <a href="choix_semestre.php">Accueil</a> / <a href="index_etudiant.php?id_semestre=<?php echo  $id_semestre ?>"><?php echo "S" . $id_semestre ?></a> / <a href="soumission_etu_par_matiere.php?id_semestre=<?php echo  $id_semestre ?>"><?php echo $_SESSION['nom_mat'] ?></a> / <a href="soumission_etu.php?id_sous=<?= $id_sous ?>&id_matiere=<?= $id_matiere ?>&color=<? $color ?>&id_semestre=<?= $id_semestre ?>"><?php echo $row_titre['titre_sous']; ?></a> / <a href="#">Réponse</a>
                        </h3>
                    </div>
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
        <div class="content">
            <div class="row">
                <h3 class="page-title"> Mettez votre réponse ici </h3>

                <div class="form-horizontal">
                    <p class="erreur_message">
                        <?php
                        if (isset($message)) {
                            echo $message;
                        }
                        ?>

                    </p>
                </div>


                <div class="col-md-12 grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label>Description : </label>
                                    <div class="col-md-6">
                                        <textarea id="exampleInputUsername1" name="description_sous" id="" class="form-control" cols="30" rows="10"></textarea>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label>Sélectionnez un fichier : </label>
                                    <div class="col-md-6">
                                        <input type="file" id="fichier" name="file[]" class="form-control" multiple required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-6">
                                        <input type="submit" name="button" value="Enregistrer" class="btn btn-primary" />
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
<?php


} else {
    function test_input($data)
    {
        $data = htmlspecialchars($data);
        $data = trim($data);
        $data = htmlentities($data);
        $data = stripcslashes($data);
        return $data;
    }

    if (isset($_POST['button'])) {
        $req_detail3 = "SELECT  *   FROM soumission   WHERE id_sous = $id_sous and (status=0 or status=1)  and date_fin > NOW()  ";
        $req3 = mysqli_query($conn, $req_detail3);
        if (mysqli_num_rows($req3) > 0) {
            $row_soumission = mysqli_fetch_assoc($req3);
            $debut=$row_soumission['date_debut'];
            $dateTime = new DateTime($debut);
            $debut = $dateTime->format('Y-m-d');

            $descri = test_input($_POST['description_sous']);
            $files = $_FILES['file'];
            if (!empty($descri) or !empty($files)) {
                $sql = "UPDATE reponses set description_rep = '$descri' ,  `date` = NOW() where id_sous = $id_sous and id_etud=(select id_etud from etudiant where email = '$email') ";

                $req1 = mysqli_query($conn, $sql);

                $id_rep = mysqli_insert_id($conn);
                foreach ($files['tmp_name'] as $key => $tmp_name) {
                    $file_name = $files['name'][$key];
                    $file_tmp = $files['tmp_name'][$key];
                    $file_size = $files['size'][$key];
                    $file_error = $files['error'][$key];
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                    if ($file_error === 0) {
                        // $new_file_name = uniqid('', true) . '.' . $file_ext;
                        $new_file_name = $file_name;


                        $sql3 = "SELECT matricule FROM etudiant WHERE etudiant.email = '$email'";
                        $code_matiere_result = mysqli_query($conn, $sql3);
                        $row = mysqli_fetch_assoc($code_matiere_result);
                        $matricule = $row['matricule'];

                        $sql3 = "SELECT code FROM matiere,soumission WHERE matiere.id_matiere = soumission.id_matiere and id_sous = $id_sous ";
                        $code_matiere_result = mysqli_query($conn, $sql3);
                        $row = mysqli_fetch_assoc($code_matiere_result);
                        $code_matire = $row['code'];
                        $matricule_directory = '../files/' . $code_matire . '/' . 'soumission_'.$debut . '/' . 'reponses/' .$matricule;


                        // Créer le dossier s'il n'exist pas
                        if (!is_dir($matricule_directory)) {
                            mkdir($matricule_directory, 0777, true);
                        }

                        // Chemin complet 
                        $destination = $matricule_directory . '/' . $new_file_name;
                        move_uploaded_file($file_tmp, $destination);

                        // Insérer les info dans la base de donnéez
                        $sql2 = "INSERT INTO `fichiers_reponses` (`id_rep`, `nom_fichiere`, `chemin_fichiere`) VALUES ((SELECT reponses.id_rep FROM reponses,etudiant WHERE reponses.id_etud=etudiant.id_etud and email='$email' and reponses.id_sous=$id_sous), '$file_name', '$destination')";
                        $req2 = mysqli_query($conn, $sql2);


                        if ($req1 && $req2) {
                            // unset($_SESSION['autorisation']);
                            $_SESSION['ajout_reussi'] = true;
                            header("location:reponse_etudiant.php?id_sous=$id_sous&id_matiere=$id_matiere&color=$color&id_semestre=$id_semestre");
                        } else {
                            mysqli_connect_error();
                        }
                    }
                }
            }
        } else {
            $_SESSION['id_sous'] = $id_sous;
            header("location:soumission_etu.php?id_sous=$id_sous&id_matiere=$id_matiere&color=$color&id_semestre=$id_semestre");
            $_SESSION['temp_finni'] = true;
        }
    }


    include "nav_bar.php";

    $sql = "SELECT * FROM reponses  WHERE  id_sous = '$id_sous' and id_etud = (select id_etud from etudiant where email = '$email')";
    $req1 = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($req1);

    # Rêquete pour récupere la date de fin
    $sq_date = "select date_fin from soumission where id_sous = '$id_sous' ";
    $req_date = mysqli_query($conn, $sq_date);
    $row_date = mysqli_fetch_assoc($req_date);
    ?>
    <link rel="stylesheet" href="CSS/cronometre.css">

    <div class="content-wrapper">
        <div class="content" style="height:70px;">

            <div class="page-header" style="height:100px;">
                <div style="display:flex;justify-content:space-bettwen;width:100%;height:100px;">
                    <div style="width:100%;">
                        <h3 class="page-title">
                            <span class="page-title-icon bg-gradient-primary text-white me-2">
                                <i class="mdi mdi-home"></i>
                            </span> <a href="choix_semestre.php">Accueil</a> / <a href="index_etudiant.php?id_semestre=<?php echo  $id_semestre ?>"><?php echo "S" . $id_semestre ?></a> / <a href="soumission_etu_par_matiere.php?id_semestre=<?php echo  $id_semestre ?>"><?php echo $_SESSION['nom_mat'] ?></a> / <a href="soumission_etu.php?id_sous=<?= $id_sous ?>&id_matiere=<?= $id_matiere ?>&color=<? $color ?>&id_semestre=<?= $id_semestre ?>"><?php echo $row_titre['titre_sous']; ?></a> / <a href="#">Réponse</a>
                        </h3>
                    </div>
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
        <div class="content">
            <div class="row">

                <div class="col-md-12">

                    <div class="col-md-12" style="display: flex; justify-content: space-between;">

                        <div>
                            <h3 class="page-title"> Modifier votre réponse </h3>
                        </div>
                    </div>
                    <br>
                </div>
                <div class="col-md-5 grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="exampleInputUsername1" class="col-md-4">Description : </label>
                                    <textarea id="exampleInputUsername1" name="description_sous" class="form-control" cols="30" rows="10"><?= $row['description_rep'] ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Sélectionnez un fichier : </label>
                                    <input type="file" id="fichier" name="file[]" class="form-control" multiple>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12" style="display: flex; justify-content: space-between;">
                                        <input type="submit" name="button" value="Uploader" class="btn btn-primary" />
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-7 grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <form>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <a href="confirmer.php?id_sous=<?php echo $row['id_sous'] ?>&id_matiere=<?= $id_matiere ?>&color=<?= $color ?>&id_semestre=<?php echo $id_semestre; ?>" id="confirmer" class="btn btn-gradient-info btn-icon-text">
                                                    <i class="mdi mdi-upload btn-icon-prepend"></i> Rendre
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <b>
                                                    <blockquote class="text-danger ">
                                                        Une fois que vous aurez rendu votre travail, vous ne pourrez pas le modifier
                                                    </blockquote>
                                                </b>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <p class="card-title">Votre Réponse : </p>
                            <?php
                            $sql2 = "SELECT * FROM fichiers_reponses, reponses, etudiant WHERE fichiers_reponses.id_rep = reponses.id_rep AND reponses.id_etud = etudiant.id_etud AND email = '$email' AND reponses.id_sous = '$id_sous';";
                            $req2 = mysqli_query($conn, $sql2);
                            if (mysqli_num_rows($req2) == 0) {
                            ?>
                                <?php
                                echo "Il n'y a pas de fichier ajouté !";
                                ?>
                                <ul style="list-style: none;">
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
                                            <a class="btn btn-inverse-danger btn-sm ms-4" href="supprime_fichier.php?id_file=<?= $row2['id_fich_rep'] ?>&id_sous=<?= $id_sous ?>&id_matiere=<?= $id_matiere ?>&color=<?= $color ?>&id_semestre=<?= $id_semestre ?>" id="supprimer">Supprimer</a>
                                        </blockquote>
                                        <br>
                                <?php
                                    }
                                }
                                ?>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
    </div>
    <?php
    if (isset($_SESSION['suppression_reussi']) && $_SESSION['suppression_reussi'] === true) {
        echo "<script>
            Swal.fire({
                title: 'Suppression réussie !',
                text: 'Le fichier a été supprimé avec succès 🎉🎉',
                icon: 'success',
                confirmButtonColor: '#3099d6',
                confirmButtonText: 'OK'
            });
            </script>";

        // Supprimer l'indicateur de succès de la session
        unset($_SESSION['suppression_reussi']);
    }

    if (isset($_SESSION['enregistre']) && $_SESSION['enregistre'] === true) {
        echo '<script>
        Swal.fire({
            position: "top-start",
            icon: "success",
            text: "Votre travail a été enregistré avec succès 🎉🎉",
            showConfirmButton: false,
            timer: 2000
          });
        </script>';

        // Supprimer l'indicateur de succès de la session
        unset($_SESSION['enregistre']);
    }
    ?>

    <script>
        var liensConfirmer = document.querySelectorAll("#confirmer");

        // Parcourir chaque lien d'archivage et ajouter un écouteur d'événements
        liensConfirmer.forEach(function(lien) {
            lien.addEventListener("click", function(event) {
                event.preventDefault();
                Swal.fire({
                    title: "Voulez-vous vraiment confirmer votre travail ?",
                    text: "Une fois que vous aurez confirmé votre travail, vous ne pourrez pas revenir en arrière. Êtes-vous sûr de vouloir procéder ?",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#3099d6",
                    cancelButtonColor: "#d33",
                    cancelButtonText: "Annuler",
                    confirmButtonText: "Confirmer"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = this.href;
                    }
                });
            });
        });


        var liensSupprimer = document.querySelectorAll("#supprimer");

        // Parcourir chaque lien d'archivage et ajouter un écouteur d'événements
        liensSupprimer.forEach(function(lien) {
            lien.addEventListener("click", function(event) {
                event.preventDefault();
                Swal.fire({
                    title: "Voulez-vous vraiment supprimer ce fichier ?",
                    text: "",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#3099d6",
                    cancelButtonColor: "#d33",
                    cancelButtonText: "Annuler",
                    confirmButtonText: "Supprimer"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = this.href;
                    }
                });
            });
        });
    </script>

<?php
}
?>


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