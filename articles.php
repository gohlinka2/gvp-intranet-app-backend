<?php
include 'creds.php';
const QUERY_PARAM_LAST_ARTICLE_ID = "lastArticleId";
try {
    $conn = new PDO("mysql:host=$server;dbname=$database",$username,$pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if(isset($_GET[QUERY_PARAM_LAST_ARTICLE_ID]) && is_numeric($_GET[QUERY_PARAM_LAST_ARTICLE_ID])) {
        $stmt = $conn->prepare("SELECT id_clanku, topic, preface, text, autor, date FROM $tableArticles WHERE visible = 1 AND id_clanku < :".QUERY_PARAM_LAST_ARTICLE_ID." ORDER BY id_clanku DESC LIMIT 15");
        $stmt->bindParam(':'.QUERY_PARAM_LAST_ARTICLE_ID,$_GET[QUERY_PARAM_LAST_ARTICLE_ID]);
    } else {
        $stmt = $conn->prepare("SELECT id_clanku, topic, preface, text, autor, date FROM $tableArticles WHERE visible = 1 ORDER BY id_clanku DESC LIMIT 15");
    }
    $stmt->execute();
    $articles = $stmt->fetchAll();
    foreach ($articles as $index => $article) {
        $output[$index]['id'] = $article['id_clanku'];
        $output[$index]['title'] = $article['topic'];
        $output[$index]['description'] = $article['preface'];
        $output[$index]['content'] = $article['text'];
        $output[$index]['author'] = $article['autor'];
        $output[$index]['date'] = $article['date'];
    }
    header('Content-Type: application/json');
    echo json_encode($output,JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
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
