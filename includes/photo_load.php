<?php session_start(); ?>
<?php

    require_once 'config.php';
    //Establish a database connection
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    //Was there an error connecting to the database?
    if ($mysqli->errno) {
        //The page isn't worth much without a db connection so display the error and quit
        print($mysqli->error);
        exit();
    }

    $choice = $_POST['choice'];

    if($choice == 0){
        $sql = " SELECT * from photo left join photoinalbum using(photo_id) left join album using(album_id);";
        $result = $mysqli->query($sql);
    }
    else{
        $sql = $mysqli->prepare("SELECT * from photo left join photoinalbum using(photo_id) left join album using(album_id) where album_id = ?;");
        $sql->bind_param('i', $choice);
        $sql->execute();
        $result = $sql->get_result();
    }

    $d = array();

    while ($row = $result->fetch_assoc()) {
        $album= $row['album_id'];
        $id= $row['photo_id'];
        if ($album == null) $album ='';
        $url = $row['photo_url'];
        $name = $row['photo_caption'];

        $new = array($album,$id,$name,$url);
        array_push($d,$new);
    }

    shuffle($d);
    $output = array_slice($d, 0, 15);

    echo json_encode($output);
?>
