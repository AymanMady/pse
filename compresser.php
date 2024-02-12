<?php
function addFolderToZip($dir, $zipArchive, $zipdir = ''){
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {

            if(!empty($zipdir)) $zipArchive->addEmptyDir($zipdir);

            while (($file = readdir($dh)) !== false) {

                if(!is_file($dir . $file)){
                    if( ($file !== ".") && ($file !== "..")){
                        addFolderToZip($dir . $file . "/", $zipArchive, $zipdir . $file . "/");
                    }
                }else{
                    $zipArchive->addFile($dir . $file, $zipdir . $file);
                }
            }
        }
        closedir($dh);
    }
}
$folderPath = 'files/';

$zipFileName = 'output.zip';

$zip = new ZipArchive;

if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
    addFolderToZip($folderPath, $zip);

    $zip->close();
    echo 'Dossier compressé avec succès';
} else {
    echo 'Impossible de créer le fichier ZIP';
}
?>

<?php
$url = 'https://chahid.info/pse/receive.php'; 
$filePath = 'output.zip'; 
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => new CURLFile($filePath)]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);
echo $response;
    header("Location:export.php");
?>
