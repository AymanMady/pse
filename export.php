<?php
session_start();

$dbHost = 'localhost'; 
$dbUsername = 'root'; 
$dbPassword = ''; 
$dbName = 'pse'; 

$fileName = 'backup_queries.sql';
$filePath = 'admin/backup_queries.sql';
if (!file_exists($filePath)) {
    $_SESSION['error_file'] = true;
    header("Location:admin/mise_a_jour.php");
    exit();
}

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'https://chahid.info/pse/receive_sql.php');
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, ['file' => new CURLFile($filePath)]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);

if ($response === false) {
    $_SESSION['error'] = curl_error($curl);
    curl_close($curl);
    $_SESSION['error_curl'] = true;
    header("Location:admin/mise_a_jour.php");
    exit();
}

curl_close($curl);
unlink($filePath);
$_SESSION['response'] = "Réponse de l'Application 2: $response";
$_SESSION['envoie_reussi'] = true;
header("Location:admin/mise_a_jour.php");
exit();
?>
