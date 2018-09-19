<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $idString = "1";

    // När vi har å,ä,ö i värden, kan vi behöva sätta charset när vi skapar objektet.
    $dbh = new PDO("mysql: host=localhost; dbname=moviedatabase; charset=utf8", "Anders", "abc123");

    // Kolla om det finns ett ID-värde med.
    if(is_numeric($_GET["id"])){
        $idString = "movies.ID = " . $_GET["id"];
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
    
    $result = json_encode($result);
    echo $result;





?>