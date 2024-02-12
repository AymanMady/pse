<?php
session_start();
$email = $_SESSION['email'];
if ($_SESSION["role"] != "ens") {
    header("location:authentification.php");
}
?>

<?php
include "nav_bar.php";
?>


<title>Détail de la soumission</title>

<style>
    ul li {
        list-style: none;
    }
</style>


<?php
include_once "../connexion.php";
$id_sous = $_GET['id_sous'];

$req_detail = "SELECT * FROM soumission INNER JOIN matiere USING(id_matiere), enseignant WHERE id_sous = $id_sous AND soumission.id_ens=enseignant.id_ens ";
$req = mysqli_query($conn, $req_detail);
while ($row = mysqli_fetch_assoc($req)) {
?>
    <div class="content-wrapper">
        <div class="content">
            <div class="page-header">
                <h3 class="page-title">
                    <span class="page-title-icon bg-gradient-primary text-white me-2">
                        <i class="mdi mdi-home"></i>
                    </span> <a href="choix_semester.php">Accuei</a>
                    <?php echo " / " ?>
                    <a href="index_enseignant.php?id_semestre=<?php echo $_SESSION['id_semestre']; ?>"><?php echo "S" . $_SESSION['id_semestre']; ?></a>
                    <?php echo " / " ?><a href="soumission_par_matiere.php"><?php echo $row['libelle'] ?></a>
                    <?php echo " / " ?><a href="#"><?php echo $row['titre_sous'] ?></a>
                </h3>
            </div>

            <div class="content">
                <div class="row">

                    <div class="col-md-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <p> <?php echo "<strong>Titre : </strong>" . $row['titre_sous']; ?></p>
                                <p>
                                    <strong>Description : </strong>
                                <div style="overflow: auto; height: 70px;">
                                    <?php echo "<strong>Description :&nbsp; </strong>" . $row['description_sous']; ?>
                                </div><br>
                                </p>
                                <p><?php echo "<strong>Pour plus d'informations : </strong>" . $row['person_contact']; ?></p>
                                <p><?php echo "<strong>Code de la matière : </strong>" . $row['code']; ?></p>
                                <p> <?php echo "<strong>Date de début : </strong>" . $row['date_debut']; ?></p>
                                <p><?php echo "<strong>Date de fin : </strong>" . $row['date_fin']; ?></p>
                                <p><?php echo "<strong>Nom et prénom de l'enseignant : </strong>" . $row['nom'] . " " . $row['prenom']; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-description">Le(s) Fichier(s)</h4>

                                <?php
                                $sql2 = "SELECT * FROM fichiers_soumission WHERE id_sous='$id_sous' ";
                                $req2 = mysqli_query($conn, $sql2);
                                if (mysqli_num_rows($req2) == 0) {
                                    echo "Il n'y a pas de fichier ajouté !";
                                } else {
                                ?>
                                    <ul>
                                        <?php
                                        while ($row2 = mysqli_fetch_assoc($req2)) {
                                            $file_name = $row2['nom_fichier'];
                                            ?>
                                            <blockquote class="blockquote blockquote-info" style="border-radius:10px;">
                                                <p><strong><?= $row2['nom_fichier'] ?> </strong></p>
                                                <?php
                                                $test = explode(".", $file_name);

                                                $test = explode(".", $file_name);
                                                $endIndex = $test[count($test) - 1];
                                                if ($endIndex == "pdf") {
                                                ?>
                                                    &nbsp;<a class="btn btn-inverse-info btn-sm" href="open_file.php?file_name=<?= $file_name ?>&id_sous=<?= $id_sous ?>">Visualiser</a>
                                                <?php
                                                } else {
                                                ?>
                                                    <a class="btn btn-inverse-info btn-sm" title="Les fichiers d'extension pdf sont les seuls que vous pouvez visualiser 😒😒.">Visualiser</a>
                                                <?php
                                                }
                                                ?>
                                                <a class="btn btn-inverse-info btn-sm ms-4" href="telecharger_fichier.php?file_name=<?= $file_name ?>&id_sous=<?= $id_sous ?>">Télécharger</a>
                                            </blockquote>
                                            <br>
                                        <?php
                                        }
                                        ?>
                                    </ul>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                <?php
                } 
                ?>
                </div>
                <?php
                if (isset($_GET['color'])) {
                    $color = $_GET['color'];
                    $id_matiere = $_GET['id_matiere'];
                ?>
                    <p>
                        <a href="soumission_par_matiere.php?id_matiere=<?php echo "$id_matiere"; ?>&color=<?php echo $color ?>" class="btn btn-primary">Retour</a>
                    </p>
                <?php
                } else {
                ?>
                    <p>
                        <a href="soumission_en_ligne.php" class="btn btn-primary">Retour</a>
                        <a href="modifier_soumission.php?id_sous=<?= $id_sous ?>" class="btn btn-primary">Modifier</a>
                    </p>
                <?php
                }
                ?>

            </div>