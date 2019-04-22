<?php
include 'creds.php';
const PARAM_ARTICLE_ID = "id_depends";
const PARAM_TEXT = "text";
const PARAM_AUTHOR = "autor";
const PARAM_IP = "ip";
const PARAM_DATE = "datetime";
const PARAM_EMAIL = "email";
const PARAM_VISIBLE = "visible";
try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $comm = json_decode(file_get_contents('php://input'));

        if (isset($comm->articleId) && isset($comm->text) && isset($comm->author) && isset($comm->email)) {
            $conn = new PDO("mysql:host=$server;dbname=$database",$username,$pass);
//            $conn = new PDO("mysql:host=$server;dbname=$testDatabase",$testUsername,$testPass);

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("INSERT INTO $tableComments (".PARAM_ARTICLE_ID.",".PARAM_TEXT.",".PARAM_AUTHOR.",".PARAM_IP.",".PARAM_DATE.",".PARAM_EMAIL.",".PARAM_VISIBLE.") VALUES (:".PARAM_ARTICLE_ID.",:".PARAM_TEXT.",:".PARAM_AUTHOR.",:".PARAM_IP.",:".PARAM_DATE.",:".PARAM_EMAIL.",:".PARAM_VISIBLE.")");
//            $stmt = $conn->prepare("INSERT INTO $testTableComments (".PARAM_ARTICLE_ID.",".PARAM_TEXT.",".PARAM_AUTHOR.",".PARAM_IP.",".PARAM_DATE.",".PARAM_EMAIL.",".PARAM_VISIBLE.") VALUES (:".PARAM_ARTICLE_ID.",:".PARAM_TEXT.",:".PARAM_AUTHOR.",:".PARAM_IP.",:".PARAM_DATE.",:".PARAM_EMAIL.",:".PARAM_VISIBLE.")");

            $comm->ip = $_SERVER['REMOTE_ADDR'];
            $comm->visibility = 0;
            $comm->dateTime = date("Y-m-d H:i:s");
            $stmt->bindParam(':'.PARAM_ARTICLE_ID,$comm->articleId);
            $stmt->bindParam(':'.PARAM_TEXT,$comm->text);
            $stmt->bindParam(':'.PARAM_AUTHOR,$comm->author);
            $stmt->bindParam(':'.PARAM_IP,$comm->ip);
            $stmt->bindParam(':'.PARAM_DATE,$comm->dateTime);
            $stmt->bindParam(':'.PARAM_EMAIL,$comm->email);
            $stmt->bindParam(':'.PARAM_VISIBLE,$comm->visibility);
            $stmt->execute();
            mail($adminEmail,"[GVPIntranet app] Nový komentář k článku","Do databáze byl přidán nový komentář a čeká na schválení.<br/><br/>Náhled: <p style=\"background-color:lightgray;\">ID článku: $comm->articleId<br/>$comm->text<br/>--------------<br/>$comm->author<br/>$comm->email<br/>$comm->ip<br/>$comm->dateTime</p><br/> Prosím zkontrolujte obsah a zvažte publikování článku.<br/>Na tuto zprávu neodpovídejte, byla vygenerována automaticky.","Content-Type: text/html;");
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
