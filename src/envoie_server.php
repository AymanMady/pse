<?php
session_start();
if ($_SESSION["role"] != "ens") {
    header("location:authentification.php");
    exit; // Terminer le script après la redirection
}

function addFolderToZip($dir, $zipArchive, $zipdir = ''){
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {

            // Ajout du dossier courant au zip
            if(!empty($zipdir)) $zipArchive->addEmptyDir($zipdir);

            while (($file = readdir($dh)) !== false) {

                // Si fichier, l'ajouter directement. Si dossier, utiliser la récursivité.
                if(!is_file($dir . $file)){
                    // dossier : récursivité
                    if( ($file !== ".") && ($file !== "..")){
                        addFolderToZip($dir . $file . "/", $zipArchive, $zipdir . $file . "/");
                    }
                }else{
                    // fichier
                    $zipArchive->addFile($dir . $file, $zipdir . $file);
                }
            }
        }
        closedir($dh);
    }
}

$code = isset($_GET['code']) ? $_GET['code'] : null; // Vérifier si le code est défini
$dat = isset($_GET['dat']) ? $_GET['dat'] : null; // Vérifier si la date est définie

// Vérifier si le code et la date sont définis
if (!$code || !$dat) {
    echo "Code ou date non défini.";
    exit; // Terminer le script si le code ou la date ne sont pas définis
}

// Chemin du dossier à compresser
$folderPath = 'files/'.$code.'/soumission_'.$dat.'/';

// Nom du fichier ZIP de sortie (peut être modifié selon votre besoin)
$zipFileName = 'soumission_'.$dat.'.zip';

// Création de l'objet ZipArchive
$zip = new ZipArchive;

if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
    // Ajout du dossier au zip
    addFolderToZip($folderPath, $zip);

    // Fermeture de l'archive
    $zip->close();
    echo 'Dossier compressé avec succès';
} else {
    echo 'Impossible de créer le fichier ZIP';
    exit; // Terminer le script si la création du fichier ZIP a échoué
}

// Check if the zip file exists
if (file_exists($zipFileName)) {
    echo 'ZIP file created successfully';
} else {
    echo 'Failed to create ZIP file';
}

$url = 'https://chahid.info/pse/receive.php?code='.$code;

$filePath = 'soumission_'.$dat.'.zip'; // Chemin du fichier compressé

// Debugging statements
echo 'File path: ' . $filePath;

// Initialisation de cURL
$ch = curl_init();

// Paramètres de cURL
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => new CURLFile($filePath)]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// Exécution de la requête
$response = curl_exec($ch);

// Debugging statements
if ($response === false) {
    echo 'Error: ' . curl_error($ch);
} else {
    echo 'Response: ' . $response;
}

// Fermeture de la session cURL
curl_close($ch);

// Redirection après avoir terminé le processus
// header("Location:enseignant/reponses_etud.php");
exit; // Assurer la fin du script après la redirection
?>
