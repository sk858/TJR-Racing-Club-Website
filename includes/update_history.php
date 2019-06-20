<?php
if(isset($_SESSION['logged_user_by_sql'])){

    require_once 'includes/config.php';
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($mysqli->errno) {
       print($mysqli->error);
        exit();
    }
?>

<?php
    $msg = "";
    $error =false;

    if(isset($_POST['new_history_submit'])){
        $year = filter_input( INPUT_POST, 'new_year', FILTER_SANITIZE_NUMBER_INT );
        $title = filter_input( INPUT_POST, 'new_title', FILTER_SANITIZE_STRING );
        $intro = filter_input( INPUT_POST, 'new_intro', FILTER_SANITIZE_STRING );

        if (!empty($year) && !empty($title) && !empty($intro)){
            if( !preg_match('/(19\d{2})|(20[0-9][0-9])/',$year)|| strlen($title)>50) {
                $msg.="Invalid history input. ";
                $error=true;
            }

            if (!$error){
                $sql = $mysqli->prepare("INSERT INTO history ( year, com, intro ) VALUES (?,?,?);");
                $sql->bind_param('iss', $year, $title, $intro);

                if( ! empty($sql) ) {
                    if( $sql->execute() ) {
                        $msg.= 'History Saved.';
                    }
                    else
                        $msg.= 'History Existed.';
                }
            }
        }
        else
            $msg .='Incomplete Album Information.';

        echo "<div><p>$msg</p><div>";
    }
?>

<?php
    $msg = "";
    $error =false;

    if(isset($_POST['edit_history_submit'])){
        $year = filter_input( INPUT_POST, 'edit_year', FILTER_SANITIZE_NUMBER_INT );
        $old_title = filter_input( INPUT_POST, 'cur_title', FILTER_SANITIZE_STRING );
        $title = filter_input( INPUT_POST, 'edit_title', FILTER_SANITIZE_STRING );
        $intro = filter_input( INPUT_POST, 'edit_intro', FILTER_SANITIZE_STRING );

        if (!empty($year) && !empty($title) && !empty($intro)){
            if(strlen($title)>50) {
                $msg.="Invalid history input. ";
                $error=true;
            }

            if (!$error){
                $sql = $mysqli->prepare("DELETE FROM history where year = ? and com ='?;");
                $sql->bind_param('is', $year, $old_title);

                $sql = $mysqli->prepare("INSERT INTO history ( year, com, intro ) VALUES (?,?,?);");
                $sql->bind_param('iss', $year, $title, $intro);

                if( ! empty($sql) ) {
                    if( $sql->execute() ) {
                        $msg.= 'History Saved.';
                    }
                    else
                        $msg.= 'History Existed.';
                }
            }
        }
        else
            $msg .='Incomplete Album Information.';

        echo "<div><p>$msg</p><div>";
    }
?>

<?php
    $msg = "";
    $error =false;

    if(isset($_POST['image_history_submit'])){
        $year = filter_input( INPUT_POST, 'image_year', FILTER_SANITIZE_NUMBER_INT );
        $photo_list = $_POST['photo_list'];


        if (!empty($year) && !empty($photo_list)){
            if( !preg_match('/(19\d{2})|(20[0-9][0-9])/',$year)) {
                $msg.="Invalid year input. ";
                $error=true;
            }

            if (!$error){
                $sql = $mysqli->prepare("DELETE FROM photoinhistory where year = ?;");
                $sql->bind_param('i', $year);
                $sql->execute();

                foreach($photo_list as $check) {
                    $sql = $mysqli->prepare("INSERT INTO photoinhistory ( year, photo_id ) VALUES (?,?);");
                    $sql->bind_param('ii', $year, $check);
                    $sql->execute();
                }
            }
        }
        else
            $msg .='Incomplete Image Information.';

        echo "<div><p>$msg</p><div>";
    }
?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
        <h3>Add History</h3>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Year</label>
                <input type="text" name="new_year" class="form-control">
            </div>
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="new_title" class="form-control">
            </div>
            <div class="form-group">
                <label>Introduction</label>
                <textarea rows="3" cols="40" name="new_intro" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <input type="submit" name="new_history_submit" value="Add history" class="btn primary" >
            </div>
        </form>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
        <h3>Edit History</h3>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Year</label>
                <select name="edit_year" class="form-control" id="year_select">
                    <?php
                        $sql = 'SELECT distinct year FROM history;';
                        $result = $mysqli->query($sql);
                        if (!$result) {
                            print($mysqli->error);
                            exit();
                        }
                        echo "<option value='0' selected>---</option>";
                        while ($row = $result->fetch_assoc()){
                            $year = $row['year'];
                            echo "<option value='$year'>{$row['year']}</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>Title</label>
                <select name="cur_title" class="form-control" id="title_select">
                </select>
            </div>
            <div class="form-group">
                <label>New Title</label>
                <input type="text" name="edit_title" class="form-control">
            </div>
            <div class="form-group">
                <label>Introduction</label>
                <textarea rows="3" cols="40" name="edit_intro" class="form-control" id="intro_select"></textarea>
            </div>
            <div class="form-group">
                <input type="submit" name="edit_history_submit" value="Edit history" class="btn primary" >
            </div>
        </form>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
        <h3>History Image Edit</h3>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Year</label>
                <input type="text" name="image_year" class="form-control">
            </div>
            <div class="form-group">
                <label>Image</label>
                <div class=" scrollbar" >
                    <?php
                        $sql = 'SELECT photo_id, photo_caption FROM photo;';
                        $result = $mysqli->query($sql);
                        if (!$result) {
                            print($mysqli->error);
                            exit();
                        }
                        while ($row = $result->fetch_assoc()){
                            $photo_id = $row['photo_id'];
                            echo "<input type='checkbox' name='photo_list[]' value='$photo_id'>{$row['photo_caption']}<br>";
                        }
                    ?>
                </div>
            </div>
            <div class="form-group">
                <input type="submit" name="image_history_submit" value="Add history" class="btn primary" >
            </div>
        </form>
        </div>
    </div>

</div>

<?php
}
?>