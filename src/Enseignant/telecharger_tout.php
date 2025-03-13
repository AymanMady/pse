<?php
include_once "../connexion.php";

function downloadFilesAsZip($files) {
    $zip = new ZipArchive();
    $zipFilename = 'les_fichies_de_somission.zip';

    if ($zip->open($zipFilename, ZipArchive::CREATE) === TRUE) {
        // Group files by matricule
        $filesByMatricule = array();
        foreach ($files as $file) {
            $matricule = substr($file['filename'], 0, strpos($file['filename'], "_"));
            $filesByMatricule[$matricule][] = $file;
        }

        // Add files to the ZIP archive
        foreach ($filesByMatricule as $matricule => $matriculeFiles) {
            // Create a folder for each set of files with the same matricule
            $zip->addEmptyDir($matricule);

            // Add files to the created folder
            foreach ($matriculeFiles as $file) {
                $zip->addFile($file['path'], $matricule . '/' . $file['filename']);
            }
        }

        $zip->close();

        // Set headers for downloading the zip file
        header('Content-Description: File Transfer');
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zipFilename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($zipFilename));

        // Read and output the zip file content
        readfile($zipFilename);

        // Delete the temporary zip file
        unlink($zipFilename);
    } else {
        echo "Failed to create zip archive.";
    }
}

$id_sous = $_GET['id_sous'];

$sql2 = "SELECT fichiers_reponses.*, reponses.id_rep, etudiant.matricule
         FROM fichiers_reponses, reponses, etudiant 
         WHERE fichiers_reponses.id_rep 
         IN (SELECT id_rep FROM reponses WHERE id_sous=$id_sous) 
         AND fichiers_reponses.id_rep=reponses.id_rep 
         AND etudiant.id_etud=reponses.id_etud 
         ORDER BY etudiant.matricule";
$req2 = mysqli_query($conn, $sql2);

// Create an array to store file information
$files = array();

while ($row = mysqli_fetch_assoc($req2)) {
    $file_chemin = $row['chemin_fichiere'];
    $filepath = $file_chemin;

    if (isset($file_chemin) && file_exists($filepath)) {
        $filename = $row['matricule'] . "_" . $row['nom_fichiere'];
        $files[] = array(
            'filename' => $filename,
            'path' => $filepath,
        );
    }
}

// Download files as a zip archive
downloadFilesAsZip($files);

// Close the database connection
mysqli_close($conn);
?>