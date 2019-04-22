<?php
include 'creds.php';
const PARAM_TITLE = "topic";
const PARAM_DESCRIPTION = "text";
const PARAM_AUTHOR = "autor";
const PARAM_DATE = "date";
const PARAM_EMAIL = "email";
const PARAM_VISIBLE = "visible";
try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $eve = json_decode(file_get_contents('php://input'));

        if (isset($eve->title) && isset($eve->description)) {
            $conn = new PDO("mysql:host=$server;dbname=$database",$username,$pass);
//            $conn = new PDO("mysql:host=$server;dbname=$testDatabase",$testUsername,$testPass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("INSERT INTO $tableEvents (".PARAM_TITLE.",".PARAM_DESCRIPTION.",".PARAM_AUTHOR.",".PARAM_DATE.",".PARAM_EMAIL.",".PARAM_VISIBLE.") VALUES (:".PARAM_TITLE.",:".PARAM_DESCRIPTION.",:".PARAM_AUTHOR.",:".PARAM_DATE.",:".PARAM_EMAIL.",:".PARAM_VISIBLE.")");
//            $stmt = $conn->prepare("INSERT INTO $testTableEvents (".PARAM_TITLE.",".PARAM_DESCRIPTION.",".PARAM_AUTHOR.",".PARAM_DATE.",".PARAM_EMAIL.",".PARAM_VISIBLE.") VALUES (:".PARAM_TITLE.",:".PARAM_DESCRIPTION.",:".PARAM_AUTHOR.",:".PARAM_DATE.",:".PARAM_EMAIL.",:".PARAM_VISIBLE.")");

            if($eve->email == null) $eve->email = "";
            if($eve->author == null) $eve->author = "";
            $eve->visibility = 0;
            $eve->currDate = date("Y-m-d");
            $stmt->bindParam(':'.PARAM_TITLE,$eve->title);
            $stmt->bindParam(':'.PARAM_DESCRIPTION,$eve->description);
            $stmt->bindParam(':'.PARAM_AUTHOR,$eve->author);
            $stmt->bindParam(':'.PARAM_DATE,$eve->currDate);
            $stmt->bindParam(':'.PARAM_EMAIL,$eve->email);
            $stmt->bindParam(':'.PARAM_VISIBLE,$eve->visibility);
            $stmt->execute();
            mail($adminEmail,"[GVPIntranet app] Nová aktualita","Do databáze byla přidána nová aktualita a čeká na schválení.<br/><br/>Náhled: <p style=\"background-color:lightgray;\"><b>$eve->title</b><br/>$eve->description<br/>---------<br/>$eve->author<br/>$eve->email<br/>$eve->currDate</p><br/> Prosím zkontrolujte obsah a zvažte publikování aktuality.<br/>Na tuto zprávu neodpovídejte, byla vygenerována automaticky.","Content-Type: text/html;");
            http_response_code(201);
            echo '201 Created';
        } else {
            http_response_code(400); //bad request
            echo '400 Bad request';
        }

    } else {
        http_response_code(400); //bad request
        echo '400 Bad request';
    }
} catch (PDOException $e) {
    http_response_code(500); //server error
    echo '500 Server error';
    echo "Connection failed: ".$e->getMessage();
} catch (Exception $e) {
    http_response_code(500); //server error
    echo '500 Server error';
    echo "Unknown server error: ".$e->getMessage();
}
?>
