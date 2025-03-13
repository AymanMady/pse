<?php
// Connexion à la base de données MySQL
$servername = "localhost";
$username = "chahztvt_chahztvt_test";
$password = "3991Eyem";
$dbname = "chahztvt_pse2";
session_start();
// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$q = $_GET['q'];

    $sql = "SELECT * FROM utilisateur WHERE login LIKE '%$q%' limit 1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $_SESSION['verification']=3;
    echo "L’e-mail est déjà pris !". "<br>";
} else  {
    $sql = "SELECT * FROM enseignant WHERE email LIKE '%$q%' limit 1";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()) {
    echo  " Enseignant " . "<br>";
}

$_SESSION['verification']=2;
}
if ($result->num_rows == 0) {
$sql = "SELECT * FROM etudiant WHERE email LIKE '%$q%' limit 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Affichage des résultats
    while($row = $result->fetch_assoc()) {
        echo "Etudiant". "<br>";
    }
    $_SESSION['verification']=3;
}
    
     
}

if ($result->num_rows == 0) {
    
    
    echo "Tu n'as pas le droit de créer un email". "<br>";
    $_SESSION['verification']=4;  
}
$conn->close();
?>
