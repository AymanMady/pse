<?php
 session_start() ;
 $email = $_SESSION['email'];
 if($_SESSION["role"]!="ens"){
     header("location:../authentification.php");
 }
include_once "../connexion.php";

$id_sous = $_GET['id_sous'];
$id_matiere = $_GET['id_matiere'];

function filterData(&$str)
{
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if (strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
}

$req_matiere = mysqli_query($conn, "SELECT * FROM matiere  WHERE id_matiere = $id_matiere");
$row_matiere = mysqli_fetch_assoc($req_matiere);

// Excel file name for download 
$fileName = "Note_" . $row_matiere['libelle'] . date('Y-m-d H:i:s') . ".xls";

// Column names 
$fields = array('Matricule', 'Nom et Prenom', 'Note');

// Display column names as first row 
$excelData = "Institut Superieur du Numerique \n\n Filiere : TC   Element :  [" . $row_matiere['code'] ."]  " . $row_matiere['libelle'] ." \n\n\n".implode("\t", array_values($fields)) ."\n";

// Fetch records from database
$query1 = $conn->query("SELECT DISTINCT matricule, nom, prenom FROM etudiant INNER JOIN inscription USING(id_etud) where id_matiere=$id_matiere ");
if ($query1->num_rows > 0) {
    // Output each row of the data 
    while ($row1 = $query1->fetch_assoc()) {
         $b = false;
$query = $conn->query("SELECT matricule, nom, prenom, note FROM etudiant INNER JOIN reponses USING(id_etud) WHERE id_sous = $id_sous");
        while ($row = $query->fetch_assoc()) {
            //$status = ($row['status'] == 1)?'Active':'Inactive';

            if($row['matricule']==$row1['matricule']){
                $lineData = array($row['matricule'], $row['nom'] . " " . $row['prenom'], $row['note']);
                array_walk($lineData, 'filterData');
                $excelData .= implode("\t", array_values($lineData)) ."\n"; 
                $b = true;
            }

        }
        if($b===false){
        $lineData = array($row1['matricule'], $row1['nom'] . " " . $row1['prenom'], 0);
        array_walk($lineData, 'filterData');
        $excelData .= implode("\t", array_values($lineData)) ."\n";
        }   
    }
} else {
    $excelData .= 'No records found ... ' . "\n";
}


// Headers for download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$fileName\"");


//
$_SESSION['exporte_ressi'] = true; 

// Render excel data 
echo $excelData;
exit;
