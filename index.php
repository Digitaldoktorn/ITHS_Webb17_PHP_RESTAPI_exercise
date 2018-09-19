<?php
    // Punkt 1-4 från Hans version
    // header('Content-Type: application/json');

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $idString = "1"; // defaultvärde 1. Om  man ej skriver in ID i URL, ska då lista alla

    // När vi har å,ä,ö i värden, kan vi behöva sätta charset när vi skapar objektet.
    $dbh = new PDO("mysql: host=localhost; dbname=moviedatabase; charset=utf8", "Anders", "abc123");

    if(isset($_GET['id'])) { // listar alla filmer utan parametrar i URL, dvs inget efter index.php (1 i $idString är som TRUE, se även SQL, 

        // Kolla om det finns ett ID-värde med.
        if(is_numeric($_GET['id'])){
            $idString = "movies.ID = " . $_GET["id"];
        }

        else {
            echo "Error! ID must be a number";
            exit;
        }
    }



    // $idString är antingen tom
    //eller fylld med "ID=" + ett nummer
    $stmt = $dbh->prepare("
        SELECT *
        FROM movies
        WHERE " . $idString
    );

    if($stmt->execute()) {
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } else {
        $result = $stmt->errorInfo();
        var_dump($stmt);
    }

    // Om queryn inte hittar något, ge felmeddelande
    if($result == NULL){
        echo "Error! No value!";
    } 
    
    $result = json_encode($result);
    echo $result;
    echo "<br>";

?>