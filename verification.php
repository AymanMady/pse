<?php
include_once("controller.php");
?>
<style>
.custom-alert {
      color: #19682c;
      background-color: #d4edda;
      border: 1px solid #c3e6cb;
      border-radius: 4px;
      padding: 10px;
      margin: 10px 0;
      max-width: 300px;
    }
  </style>
</head>
<body>


<title>Vérification de rôle</title>

<div class="container-scroller">
   <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth">
            <div class="row flex-grow">
               <div class="col-lg-4 mx-auto">
                  <div class="auth-form-light text-left p-5">
                        <form action="" method="POST">
                           <?php
                            if ($errors > 0) {
                              foreach ($errors as $displayErrors) {
                           ?>
                                    <div id="alert alert-danger" class="form-group me-4 ms-4"><?php echo $displayErrors; ?></div>
                           <?php
                              }
                           }
                           ?>
                         <div class="form-group">
                         <input type="email" id="inputRecherche" class="form-control form-control-lg" name="email" oninput="showDiv()" placeholder="Adresse e-mail">
</div>

                         
                          <?php
                          if(isset($_SESSION['verification'])){
                           $_SESSION['codde']=$_SESSION['verification'];
                          }

                          ?>
                            <div class="form-group">
                           <div id="resultats" class="custom-alert" ></div>
                           </div>
                           <div class="mt-3">
                              <center>
                                 <?php 
                                
                                 ?>
                                 <input type="submit" name="verifier" value="Vérifier"  class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">
                                  <?php 
                                  
                                  ?>
                                    
                              </center>
                           </div>
                           
                           <div class=" mt-4 font-weight-light">
                              <a href="authentification.php" class="text-primary">Connectez-vous</a>
                           </div>
                        </form>
                  </div>
               </div>
            </div>
      </div>
   </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cacher le div 'resultats' au chargement de la page
        document.getElementById('resultats').style.display = 'none';
    });

    function rechercheDynamique() {
        var inputVal = document.getElementById("inputRecherche").value;
        var resultatsDiv = document.getElementById("resultats");

        // Cacher le div avant de commencer la recherche
        resultatsDiv.style.display = 'none';

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                // Afficher le div seulement si la réponse n'est pas vide
                if (this.responseText.trim() !== '') {
                    resultatsDiv.innerHTML = this.responseText;
                    resultatsDiv.style.display = 'block';
                }
            }
        };
        xhttp.open("GET", "recherche.php?q=" + inputVal, true);
        xhttp.send();
    }

    function showDiv() {
        var userInput = document.getElementById('inputRecherche').value;
        var resultatsDiv = document.getElementById('resultats');

        if (userInput.trim() !== '') {
            rechercheDynamique(); // Appeler rechercheDynamique ici
        } else {
            resultatsDiv.style.display = 'none'; // Cacher le div si l'input est vide
        }
    }
</script>




