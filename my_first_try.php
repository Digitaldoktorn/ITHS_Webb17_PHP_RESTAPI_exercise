<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    if(isset($_GET["id"])){
        if(is_numeric($_GET["id"])){
            try {
                $dbh = new PDO("mysql: host=localhost; dbname=moviedatabase; charset=utf8", "Anders", "abc123");

            } catch(PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            }

            $stmt = $dbh->prepare("
                SELECT *
                FROM actors
                WHERE id = 1;
            ");

            $stmt->bindParam(":id", $id);

            if($stmt->execute()) {
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            } else {
                echo "error";
            }
            
            $result = json_encode($result);
            print_r($result);


            // while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            //     echo "<p>" . $row["model"] . ": " . $row["price"];
            // }
        }
    }




?>