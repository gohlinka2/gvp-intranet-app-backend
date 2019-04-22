<?php
include 'creds.php';
try {
    $conn = new PDO("mysql:host=$server;dbname=$database",$username,$pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT id_event, topic, text, date FROM $tableNews WHERE visible = 1 ORDER BY id_event DESC LIMIT 15");
    $stmt->execute();
    $articles = $stmt->fetchAll();
    foreach ($articles as $index => $article) {
        $output[$index]['id'] = $article['id_event'];
        $output[$index]['title'] = $article['topic'];
        $output[$index]['description'] = $article['text'];
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
