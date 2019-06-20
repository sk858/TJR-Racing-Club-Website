<?php session_start(); ?>
<?php

    require_once 'config.php';
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($mysqli->errno) {
        print($mysqli->error);
        exit();
    }

    $choice = $_POST['choice'];

    $sql = $mysqli->prepare("SELECT photo_url from photo where photo_id =?;");
    $sql->bind_param('i', $choice);
    $sql->execute();
    $result = $sql->get_result();
    $d = array();

    if($result){
        while ($row = $result->fetch_assoc()) {
            $url= $row['photo_url'];
            $new = array($url);
            array_push($d,$new);
        }
    }

    echo json_encode($d);
?>