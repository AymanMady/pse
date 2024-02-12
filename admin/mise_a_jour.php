<?php
session_start();
$email = $_SESSION['email'];
if ($_SESSION["role"] != "admin") {
    header("location:../authentification.php");
}
include_once 'nav_bar.php';

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
                                <a href="../compresser.php" class="btn btn-gradient-danger btn-icon-text"><i class="mdi mdi-sync btn-icon-prepend"></i> Mise à jour </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
if (isset($_SESSION['envoie_reussi']) && $_SESSION['envoie_reussi'] === true) {
    echo "<script>
    Swal.fire({
        title: 'Mise à jour réussi !',
        text: 'Les données a été envoyer au serveur avec succès.',
        icon: 'success',
        confirmButtonColor: '#3099d6',
        confirmButtonText: 'OK'
    });
    </script>";

    // Supprimer l'indicateur de succès de la session
    unset($_SESSION['envoie_reussi']);

}
if (isset($_SESSION['error_curl']) && $_SESSION['error_curl'] === true) {
    echo "<script>
    Swal.fire({
        title: 'Mise à jour nom réussi !',
        text: 'La connexion au serveur a échoué 😏😏.',
        icon: 'error',
        confirmButtonColor: '#3099d6',
        confirmButtonText: 'OK'
    });
    </script>";

    // Supprimer l'indicateur de succès de la session
    unset($_SESSION['error_curl']);
}
if (isset($_SESSION['error_file']) && $_SESSION['error_file'] === true) {
    echo "<script>
    Swal.fire({
        title: 'Mise à jour nom réussi !',
        text: 'Les données ont déjà été mises à jour.',
        icon: 'error',
        confirmButtonColor: '#3099d6',
        confirmButtonText: 'OK'
    });
    </script>";

    // Supprimer l'indicateur de succès de la session
    unset($_SESSION['error_file']);

}

?>