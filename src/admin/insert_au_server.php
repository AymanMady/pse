<?php 
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pse";
session_start();
$conn = mysqli_connect($servername, $username, $password, $dbname);
$soumissions = 'soumission.json';
$reponses = 'reponses.json';
$files_reponses = 'fichiers_reponses.json';
$files_soumission = 'fichiers_soumission.json';
$id_s = "SELECT MAX(id_sous) AS next_id FROM soumission";
                    $id_sou = mysqli_query($conn, $id_s);
                    $max_id_row = mysqli_fetch_assoc($id_sou);
                    $next_id = $max_id_row['next_id'];
                    if($next_id==null)$next_id=0;
                    // $_SESSION['id_sous_insert']=$next_id;

$id_rep_query = "SELECT MAX(id_rep)  AS next_id FROM reponses";
                    $id_rep_result = mysqli_query($conn, $id_rep_query);
                    
                    if ($id_rep_result) {
                        $max_id_row = mysqli_fetch_assoc($id_rep_result);
                        $next_id_rep = $max_id_row['next_id'];
                        if($next_id_rep==null)$next_id_rep=0;

                    } else {
                        echo "Error fetching next id_rep for reponses table: " . mysqli_error($conn);
                        exit; // Exit if there's an error
                    }
$id_rep_query_fichiers_reponses = "SELECT MAX(id_rep) AS next_id FROM fichiers_reponses";
                    $id_rep_result_fichiers_reponses = mysqli_query($conn, $id_rep_query_fichiers_reponses);
                    $max_id_row_fichiers_reponses = mysqli_fetch_assoc($id_rep_result_fichiers_reponses);
                    $next_id_rep_fichiers_reponses = $max_id_row_fichiers_reponses['next_id'];
                    if($next_id_rep_fichiers_reponses==null)$next_id_rep_fichiers_reponses=0;
                    $next_id=$next_id+1;
  
    $soumissions_json = file_get_contents($soumissions);
    
    $tableData = json_decode($soumissions_json, true);
    
    foreach ($tableData as $row) {
        // Access each field by its key and store its value in a variable
        $id_sous = $row['id_sous'];
        $titre_sous = $row['titre_sous'];
        $description_sous = $row['description_sous'];
        $person_contact = $row['person_contact'];
        $id_ens = $row['id_ens'];
        $date_debut = $row['date_debut'];
        $date_fin = $row['date_fin'];
        $valide = $row['valide'];
        $status = $row['status'];
        $id_matiere = $row['id_matiere'];
        $id_type_sous = $row['id_type_sous'];
        echo "<br>----------------------------------<br>";
                    

        $rep="INSERT INTO 
        `soumission`(`id_sous`, `titre_sous`, `description_sous`, `person_contact`,
         `id_ens`, `date_debut`, `date_fin`, `valide`, `status`, `id_matiere`, `id_type_sous`)
          VALUES ($next_id,'$titre_sous','$description_sous', '$person_contact',$id_ens,'$date_debut',
          '$date_fin', $valide ,$status,$id_matiere,$id_type_sous)";
$res = mysqli_query($conn, $rep);

if ($res) {
    echo "le sommission inserer !";
} else {
    echo "Error: " . mysqli_error($conn);
}    
    }
    
    echo "<br>";
    
    $reponses_json = file_get_contents($reponses);

    // Decode the JSON contents into an associative array
    $tableData = json_decode($reponses_json, true);
    
    // Iterate through each row of data
    foreach ($tableData as $row) {
        // Access each field by its key and store its value in a variable
        $id_repp = $row['id_rep'];
        $description_rep = $row['description_rep'];
        $date = $row['date'];
        $render = $row['render'];
        $confirmer = $row['confirmer'];
        $note = $row['note'];
        $id_sous = $row['id_sous'];
        $id_etud = $row['id_etud'];
    


// Construct and execute the INSERT query for the reponses table
$rep_reponses = "INSERT INTO 
    `reponses`(`id_rep`, `description_rep`, `date`, `render`, `confirmer`, `note`, `id_sous`, `id_etud`)
      VALUES ($next_id_rep+$id_repp, '$description_rep', '$date', '$render', '$confirmer', '$note', $next_id, $id_etud)";
$res_reponses = mysqli_query($conn, $rep_reponses);
// 8888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888

// 8888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888
        echo "<br>----------------------------------<br>";
    }
    

    $files_reponses_json = file_get_contents($files_reponses);
    $tableData = json_decode($files_reponses_json, true);
    
    // Iterate through each row of data
    foreach ($tableData as $row) {
        // Check if the id_rep matches the filter id_rep
        if ($row['id_rep'] ) {
            $id_fich_rep = $row['id_fich_rep'];
            $filter_id_rep = $row['id_rep']; // Replace with the id_rep you want to filter by

            // Define the query to fetch the next available id_rep for fichiers_reponses table
                        
    
            // Access each field by its key and store its value in a variable
            // $id_rep = $row['id_rep'];
            $nom_fichiere = $row['nom_fichiere'];
            $chemin_fichiere = $row['chemin_fichiere'];
    // max =29 
            // Construct the INSERT query
            $insert_query = "INSERT INTO fichiers_reponses (id_fich_rep, id_rep, nom_fichiere, chemin_fichiere) 
                             VALUES ($next_id_rep_fichiers_reponses+$id_fich_rep,$next_id_rep+$filter_id_rep, '$nom_fichiere', '$chemin_fichiere')";
    
            // Execute the INSERT query
            $result = mysqli_query($conn, $insert_query);
    
            // Check if the query was successful
            if ($result) {
                echo "Data inserted successfully for id_rep $filter_id_rep.<br>";
            } else {
                echo "Error inserting data for id_rep $filter_id_rep: " . mysqli_error($conn) . "<br>";
            }
        }
    }
    
mysqli_close($conn);
echo"<script>window.location.href = 'mise_a_jour.php';";

?>


