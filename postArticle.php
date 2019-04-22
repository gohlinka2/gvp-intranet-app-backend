<?php
include 'creds.php';
const PARAM_TITLE = "topic";
const PARAM_DESCRIPTION = "preface";
const PARAM_CONTENT = "text";
const PARAM_AUTHOR = "autor";
const PARAM_DATE = "date";
const PARAM_NOTE = "note";
const PARAM_EMAIL = "email";
const PARAM_VISIBLE = "visible";
try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $art = json_decode(file_get_contents('php://input'));

        if (isset($art->title)
            && (isset($art->description) || isset($art->content)) //either the description or the text should be set
            && isset($art->author)
           && isset($art->email)) {
            $conn = new PDO("mysql:host=$server;dbname=$database",$username,$pass);
//            $conn = new PDO("mysql:host=$server;dbname=$testDatabase",$testUsername,$testPass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("INSERT INTO $tableArticles (".PARAM_TITLE.",".PARAM_DESCRIPTION.",".PARAM_CONTENT.",".PARAM_AUTHOR.",".PARAM_DATE.",".PARAM_NOTE.",".PARAM_EMAIL.",".PARAM_VISIBLE.") VALUES (:".PARAM_TITLE.",:".PARAM_DESCRIPTION.",:".PARAM_CONTENT.",:".PARAM_AUTHOR.",:".PARAM_DATE.",:".PARAM_NOTE.",:".PARAM_EMAIL.",:".PARAM_VISIBLE.")");
//            $stmt = $conn->prepare("INSERT INTO $testTableArticles (".PARAM_TITLE.",".PARAM_DESCRIPTION.",".PARAM_CONTENT.",".PARAM_AUTHOR.",".PARAM_DATE.",".PARAM_NOTE.",".PARAM_EMAIL.",".PARAM_VISIBLE.") VALUES (:".PARAM_TITLE.",:".PARAM_DESCRIPTION.",:".PARAM_CONTENT.",:".PARAM_AUTHOR.",:".PARAM_DATE.",:".PARAM_NOTE.",:".PARAM_EMAIL.",:".PARAM_VISIBLE.")");

            if($art->description == null) $art->description = "";
            if($art->content == null) $art->content = "";
            if($art->note == null) $art->note = "";
            $art->visibility = 0;
            $art->currDate = date("Y-m-d");
            $stmt->bindParam(':'.PARAM_TITLE,$art->title);
            $stmt->bindParam(':'.PARAM_DESCRIPTION,$art->description);
            $stmt->bindParam(':'.PARAM_CONTENT,$art->content);
            $stmt->bindParam(':'.PARAM_AUTHOR,$art->author);
            $stmt->bindParam(':'.PARAM_DATE,$art->currDate);
            $stmt->bindParam(':'.PARAM_NOTE,$art->note);
            $stmt->bindParam(':'.PARAM_EMAIL,$art->email);
            $stmt->bindParam(':'.PARAM_VISIBLE,$art->visibility);
            $stmt->execute();
            mail($adminEmail,"[GVPIntranet app] Nový článek","Do databáze byl přidán nový článek a čeká na schválení.<br/><br/>Náhled: <p style=\"background-color:lightgray;\"><b>$art->title</b><br/>$art->description<br/>$art->content<br/>--------------<br/>$art->author<br/>$art->email<br/>$art->currDate<br/>Poznámka: $art->note</p><br/> Prosím zkontrolujte obsah a zvažte publikování článku.<br/>Na tuto zprávu neodpovídejte, byla vygenerována automaticky.","Content-Type: text/html;");
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
