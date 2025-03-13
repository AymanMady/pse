<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pse";

// Connexion
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Vérification de la connexion
if (!$conn) {
    die("La connexion a échoué : " . mysqli_connect_error());
}

// Array of queries with corresponding table names
$queries = [
    "SELECT * FROM soumission WHERE id_sous = (SELECT MAX(id_sous) FROM soumission)" => "soumission",
    "SELECT reponses.* FROM reponses WHERE id_sous =(SELECT MAX(id_sous) FROM soumission);" => "reponses",
    "SELECT * FROM fichiers_reponses WHERE id_rep IN (SELECT id_rep FROM reponses  WHERE id_sous = (SELECT MAX(id_sous) FROM soumission))" => "fichiers_reponses",
    "SELECT * FROM fichiers_soumission WHERE id_sous = (SELECT MAX(id_sous) FROM soumission)" => "fichiers_soumission"
];

foreach ($queries as $req => $table) {
    // Execute the query
    $query = mysqli_query($conn, $req);

    if ($query) { 
        $data = array(); // Initialize an array to store the fetched data

        while ($row = mysqli_fetch_assoc($query)) {
            // Unescape file paths before storing them in the array
            $data[] = $row; // Append each row to the array
        }

        // Convert the array to JSON format with unescaped slashes
        $jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        // Generate the JSON file name based on the table name
        $jsonFileName = "$table.json";

        // Write JSON data to the file
        file_put_contents($jsonFileName, $jsonData);

        echo "Data from table '$table' has been successfully written to $jsonFileName\n";
    } else {
        echo "Query for table '$table' failed: " . mysqli_error($conn);
    }
}

// Close the connection
mysqli_close($conn);

// Define the JSON file names for each table
$soumissions = 'soumission.json';
$reponses = 'reponses.json';
$files_reponses = 'fichiers_reponses.json';
$files_soumission = 'fichiers_soumission.json';


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

        // Output the values of the specific fields
        echo "id_sous: $id_sous\n";
        echo "titre_sous: $titre_sous\n";
        echo "description_sous: $description_sous\n";
        echo "person_contact: $person_contact\n";
        echo "id_ens: $id_ens\n";
        echo "date_debut: $date_debut\n";
        echo "date_fin: $date_fin\n";
        echo "valide: $valide\n";
        echo "status: $status\n";
        echo "id_matiere: $id_matiere\n";
        echo "id_type_sous: $id_type_sous\n";
    
        echo "<br>----------------------------------<br>";
    }
    
    echo "<br>";


    $reponses_json = file_get_contents($reponses);

    // Decode the JSON contents into an associative array
    $tableData = json_decode($reponses_json, true);
    
    // Iterate through each row of data
    foreach ($tableData as $row) {
        // Access each field by its key and store its value in a variable
        $id_rep = $row['id_rep'];
        $description_rep = $row['description_rep'];
        $date = $row['date'];
        $render = $row['render'];
        $confirmer = $row['confirmer'];
        $note = $row['note'];
        $id_sous = $row['id_sous'];
        $id_etud = $row['id_etud'];
    
        // Output the values of the specific fields
        echo "id_rep: $id_rep\n";
        echo "description_rep: $description_rep\n";
        echo "date: $date\n";
        echo "render: $render\n";
        echo "confirmer: $confirmer\n";
        echo "note: $note\n";
        echo "id_sous: $id_sous\n";
        echo "id_etud: $id_etud\n";
    
        echo "<br>----------------------------------<br>";
    }
    
// Read the contents of the JSON file
$files_reponses_json = file_get_contents($files_reponses);

// Decode the JSON contents into an associative array
$tableData = json_decode($files_reponses_json, true);

// Iterate through each row of data
foreach ($tableData as $row) {
    // Access each field by its key and store its value in a variable
    $id_fich_rep = $row['id_fich_rep'];
    $id_rep = $row['id_rep'];
    $nom_fichiere = $row['nom_fichiere'];
    $chemin_fichiere = $row['chemin_fichiere'];

    // Output the values of the specific fields
    echo "id_fich_rep: $id_fich_rep\n";
    echo "id_rep: $id_rep\n";
    echo "nom_fichiere: $nom_fichiere\n";
    echo "chemin_fichiere: $chemin_fichiere\n";

    echo "<br>----------------------------------<br>";
}
?>
<a href="mis_a_joure.php">mis_a_joure</a>
    <br>
<a href="delet.php">delet</a>
