<?php
    // Punkt 1-4 från Hans version
    // header('Content-Type: application/json');

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $idString = "1"; // defaultvärde 1. Om  man ej skriver in ID i URL, ska då lista alla
    $sort = "";
    $limit = "";

    // Vill användaren sortera efter någon kolumn?
    if(isset($_GET['sort'])){
        $sort = " ORDER BY " . $_GET['sort'];

        // I vilken ordning vill vi sortera?
        if(isset($_GET['DESC'])){
            $sort .= " DESC ";
        }
        else {
            $sort .= " ASC ";
        }
    }

    // Vill användaren bara visa några
    if((isset($_GET['limit'])) && (is_numeric($_GET['limit']))){
        $limit = " LIMIT " . $_GET['limit'];
    }

    // När vi har å,ä,ö i värden, kan vi behöva sätta charset när vi skapar objektet.
    $dbh = new PDO("mysql: host=localhost; dbname=moviedatabase; charset=utf8", "Anders", "abc123");

    // Lägg till
    if(isset($_POST['movieTitle'])) {
        $stmt = $dbh->prepare("
        INSERT INTO movies
        (movieTitle, Year, category)
        VALUES
        (:mTitle, :year, :cat)
        ");

        $stmt->bindParam(":mTitle", $_POST['movieTitle']);
        $stmt->bindParam(":year", $_POST['year']);
        $stmt->bindParam(":cat", $_POST['category']);

        if($stmt->execute()){
            $result = $dbh->lastInsertId();

        }
        else {
            $result = $stmt->errorInfo();
        }
        $result = json_encode($result);
        echo $result;
    }

    else {
        // Kolla om det finns ett ID-värde med.
        if(is_numeric($_GET['id'])){
            $idString = "movies.ID = " . $_GET["id"];
        }
        
        if (isset($_GET['title'])){
            // Kolla om det finns bindesstreck i titeln.
            if (strpos($_GET['title'], "-") !== false) {
                $_GET['title'] = str_replace("-", " ", $_GET['title']);
            }
            // Ska vi ha LIKE här?
            $idString = "movieTitle LIKE '" . $_GET['title'] . "%'";
        }   

        // $idString är antingen tom
        //eller fylld med "ID=" + ett nummer
        // eller fylld med movieTitle= + en titel
        $stmt = $dbh->prepare("
            SELECT *
            FROM movies
            WHERE " . $idString .
            $sort .
            $limit
        );

        if($stmt->execute()) {
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } else {
            $result = $stmt->errorInfo();
            var_dump($stmt);
        }
    }

    // Kolla om det blev något resultat.
    if(is_null($result)){
        $result = [
            "Beklagar, men vi har ingen film som matchar din sökning: " . $_GET['id'] . $_GET['title']
        ];
    } 
    
    $result = json_encode($result);
    echo $result;
    echo "<br>";

?>