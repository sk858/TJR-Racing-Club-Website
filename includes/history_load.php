<?php session_start(); ?>
<?php

    require_once 'config.php';
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($mysqli->errno) {
        print($mysqli->error);
        exit();
    }

    $choice = $_POST['choice'];

    if($choice!=0 && !empty($_POST['title'])) {
        $title = $_POST['title'];
        $sql = $mysqli->prepare("SELECT * from history where year =? and com = ?");
        $sql->bind_param('is', $choice,$title);
        $sql->execute() or die(mysqli_error());
        $result = $sql->get_result();

    }
    elseif($choice!=0){
        $sql = $mysqli->prepare("SELECT * from history where year =? order by com desc;");
        $sql->bind_param('i', $choice);
        $sql->execute() or die(mysqli_error());
        $result = $sql->get_result();
    }
    else {
        $sql = " SELECT * from history where year = (select max(year) from history) order by com desc;";
        $result = $mysqli->query($sql);
    }

    $d = array();

    while ($row = $result->fetch_assoc()) {
        $name= $row['com'];
        $intro= $row['intro'];
        $newintro = str_replace("\\r\\n","<br>", $intro);
        $newintro = str_replace("\r\n","<br>", $newintro);

        $new = array($name,$newintro);
        array_push($d,$new);
    }

    echo json_encode($d);
?>