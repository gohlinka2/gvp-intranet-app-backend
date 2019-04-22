<?php
include 'creds.php';
const QUERY_PARAM_LAST_COMMENT_ID = "lastCommentId";
const QUERY_PARAM_ARTICLE_ID = "articleId";
try {
    $conn = new PDO("mysql:host=$server;dbname=$database",$username,$pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if(isset($_GET[QUERY_PARAM_ARTICLE_ID]) && is_numeric($_GET[QUERY_PARAM_ARTICLE_ID])) {
        if(isset($_GET[QUERY_PARAM_LAST_COMMENT_ID]) && is_numeric($_GET[QUERY_PARAM_LAST_COMMENT_ID])) {
            $stmt = $conn->prepare("SELECT id_comment, id_depends, text, autor, datetime FROM $tableComments WHERE id_depends = :".QUERY_PARAM_ARTICLE_ID." AND visible = 1 AND id_comment < :".QUERY_PARAM_LAST_COMMENT_ID." ORDER BY id_comment DESC LIMIT 15");
            $stmt->bindParam(':'.QUERY_PARAM_LAST_COMMENT_ID,$_GET[QUERY_PARAM_LAST_COMMENT_ID]);
            $stmt->bindParam(':'.QUERY_PARAM_ARTICLE_ID,$_GET[QUERY_PARAM_ARTICLE_ID]);
        } else {
            $stmt = $conn->prepare("SELECT id_comment, id_depends, text, autor, datetime FROM $tableComments WHERE id_depends = :".QUERY_PARAM_ARTICLE_ID." AND visible = 1 ORDER BY id_comment DESC LIMIT 15");
            $stmt->bindParam(':'.QUERY_PARAM_ARTICLE_ID,$_GET[QUERY_PARAM_ARTICLE_ID]);
        }
        $stmt->execute();
        $comments = $stmt->fetchAll();
        foreach ($comments as $index => $comment) {
            $output[$index]['id'] = $comment['id_comment'];
            $output[$index]['articleId'] = $comment['id_depends'];
            $output[$index]['text'] = $comment['text'];
            $output[$index]['author'] = $comment['autor'];
            $output[$index]['date'] = $comment['datetime'];
        }
        if ($comments == null) {
            $output = [];
        }
    } else {
        $output = [];
    }
    header('Content-Type: application/json');
    echo json_encode($output, JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
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
