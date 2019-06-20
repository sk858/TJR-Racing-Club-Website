<?php session_start(); ?>
<?php

    require_once 'config.php';
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($mysqli->errno) {
        print($mysqli->error);
        exit();
    }

    $choice = $_POST['choice'];

    if($choice!=0){
        $sql = $mysqli->prepare("SELECT photo_id, photo_url from photoinhistory inner join photo using(photo_id) where year =?;");
        $sql->bind_param('i', $choice);
        $sql->execute() or die(mysqli_error());
        $result = $sql->get_result();
    }
    else {
        $sql = " SELECT photo_id, photo_url from photoinhistory inner join photo using(photo_id) where year = (select max(year) from history);";
        $result = $mysqli->query($sql);
    }

    $d = array();

    while ($row = $result->fetch_assoc()) {
        $id= $row['photo_id'];
        $url= $row['photo_url'];

        $new = array($id,$url);
        array_push($d,$new);
    }

    shuffle($d);
    $output = array_slice($d, 0, 4);

    echo json_encode($output);
?>