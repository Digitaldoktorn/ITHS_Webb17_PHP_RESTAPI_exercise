<?php
    // header('Content-Type: application/json');

    // Initiala variable
    $idString = "1"; // defaultvärde 1. Om  man ej skriver in ID i URL, ska då lista alla
    $sort = "";
    $limit = "";

    // Vill användaren sortera efter någon kolumn?
    if(isset($_GET['sort'])){
        $sort = " ORDER BY " . $_GET['sort'];
    }

    // Vill användaren bara visa några
    if(isset($_GET['limit'])) {
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
            LEFT JOIN actorsPerMovie ON movies.ID = actorsPerMovie.movie
            LEFT JOIN actors ON actors.ID = actorsPerMovie.actor
            LEFT JOIN categories ON categories.categoryID = movies.category
            WHERE " . $idString .
            $sort .
            $limit
        );
        if($stmt->execute()) {
            while ($row = $stmt->fetchAll(PDO::FETCH_ASSOC)) {
                /*
                Då en film kan innehålla flera skådespelare,
                ger SQL resultatet i flera rader, och det vill
                jag inte ha när det är fråga om JSON.
                Därför sparar jag resultatet rad för rad
                och bygger upp en extra array (en nivå ner)
                på actors, i vilken jag placerar skådespelarna.
                */ 
                $result[$row['movie']]['movieTitle'] = $row['movieTitle'];
                $result[$row['movie']]['year'] = $row['Year'];
                $result[$row['movie']]['category'] = $row['categoryName'];
                $result[$row['movie']]['actors'][] = $row['actorName'];
            }
        } 
        else {
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