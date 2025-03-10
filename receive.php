<?php
$code=$_GET['code'];

// Emplacement où les fichiers seront décompressés
$extractPath = 'files/'.$code.'/';

// Réception du fichier
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $uploadedFilePath = '' . basename($_FILES['file']['name']);
    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadedFilePath)) {

        // Suppression des anciens fichiers
        // Vérification supplémentaire pour éviter la suppression accidentelle
        if (is_dir($extractPath)) {
            $files = glob("$extractPath/*");
            if (is_array($files)) {
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
            }
        } else {
            echo 'Chemin de décompression invalide.';
            exit;
        }

        // Décompression du fichier reçu
        $zip = new ZipArchive;
        if ($zip->open($uploadedFilePath) === TRUE) {
            $zip->extractTo($extractPath);
            $zip->close();
            echo 'Fichier décompressé et remplacé.';
        } else {
            echo 'Erreur lors de la décompression du fichier.';
        }
    } else {
        echo 'Erreur lors du déplacement du fichier téléchargé.';
    }
} else {
    echo 'Erreur lors de la réception du fichier.';
}
?>
