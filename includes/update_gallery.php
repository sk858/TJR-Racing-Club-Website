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
    $msg ="";
    $error = false;

    if(isset($_POST['new_album_submit'])){
        $new_album_name = filter_input( INPUT_POST, 'new_album_name', FILTER_SANITIZE_STRING );

        if (!empty($new_album_name)){

            $sql = 'SELECT max(album_id) FROM album;';

            $result = $mysqli->query($sql);
            $row = $result->fetch_row();
            $new_album_id = $row[0]+1;
            $new_album_name  = ucfirst(strtolower($new_album_name));

            $sql = $mysqli->prepare("INSERT INTO album ( album_id, album_name ) VALUES ( ?,? );");
            $sql->bind_param('is', $new_album_id, $new_album_name);


            if( ! empty( $sql ) ) {
                if( $sql->execute()) {
                    $msg .= 'Album Saved.';
                }
                else
                    $msg.= 'Album Existed.';
            }
        }
        else
            $msg .='Incomplete Album Information.';

        echo "<div><p>$msg</p><div>";
    }
?>

<?php

    if(isset($_POST['new_photo_submit'])){
        $title = filter_input( INPUT_POST, 'new_title', FILTER_SANITIZE_STRING );
        $album = $_POST['new_album'];

        if (!empty( $_FILES['new_url'] ) &&  !empty($title)) {

            if(!preg_match('/^[a-zA-Z0-9 ]+$/',$title) || strlen($title)>50) {
                $msg.="Invalid title. ";
                $error=true;
            }

            if (!$error){
                $newPhoto = $_FILES['new_url'];
                $originalName = $newPhoto['name'];
                if ( $newPhoto['error'] == 0 ) {
                    $tempName = $newPhoto['tmp_name'];
                    move_uploaded_file( $tempName, "images/$originalName");
                } else {
                    $msg .="The image was not uploaded.";
                    $error = true;
                }
            }

            if (!$error){
                $sql = 'SELECT max(photo_id) FROM photo;';

                $result = $mysqli->query($sql);
                $row = $result->fetch_row();
                $photo_id = $row[0]+1;
                $title = ucfirst(strtolower($title));

                $sql = $mysqli->prepare("INSERT INTO photo ( photo_caption, photo_url, photo_id ) VALUES ( ?,'images/$originalName',?);");
                $sql->bind_param('si', $title, $photo_id);

                $sql_album = $mysqli->prepare("INSERT INTO photoinalbum (photo_id, album_id ) VALUES (?,?);");
                $sql_album->bind_param('ii', $photo_id, $album);

            }
            if(!empty($sql) && !empty($sql_album)) {
                if($sql->execute() && $sql_album->execute()) {
                    $msg.= 'Photo Saved.';
                }
                else{
                    $msg.= 'Photo Existed.';
                }
            }
            else if(!empty($sql)) {
                if($sql->execute()) {
                    $msg.= 'Photo Saved.';
                }
                else{
                    $msg.= 'Photo Existed.';
                }
            }
        }
        else{
            $msg .='Incomplete Photo Information.';
        }
        echo "<div><p>$msg</p><div>";
    }
?>

<?php
    if(isset($_POST['edit_album'])){
        $edit_name = filter_input( INPUT_POST, 'edit_album_name', FILTER_SANITIZE_STRING );
        $select_album = $_POST['select_album'];
        $error = false;

        if($select_album &&!empty($edit_name)){
            if(!preg_match('/^[a-zA-Z0-9 ]+$/',$edit_name) || strlen($edit_name)>50) {
                $msg.="Invalid new name. ";
                $error=true;
            }

            if (!$error){
                $sql = $mysqli->prepare("UPDATE album SET album_name = ? where album_id = ?;");
                $sql->bind_param('si', $edit_name,$select_album);

                if( $sql->execute()) {
                    $msg.= 'Ablum Edited.';
                }
                else{
                    $msg.= 'Editing fail.';
                }
            }
        }
        else{
            $msg .='Incomplete editing info.';
        }
        echo "<div><p>$msg</p><div>";
    };

    if(isset($_POST['delete_album'])){
        $select_album = $_POST['select_album'];
        if($select_album){
            $sql_delete_album = $mysqli->prepare("DELETE from album where album_id = ?;");
            $sql_delete_album->bind_param('i', $select_album);

            if( $sql_delete_album->execute())
                $msg .= "Delete successfully.";
            else
                $msg .= "Delete error.";
        }
        else
            $msg .= "Plesase select the album to delete.";
        echo "<div><p>$msg</p><div>";
    }
?>

<?php
    if(isset($_POST['delete_photo'])){
        $photoid = filter_input( INPUT_POST, 'edit_photo', FILTER_SANITIZE_NUMBER_INT );

        $sql_delete_inalbum = $mysqli->prepare("DELETE from photoinalbum where photo_id = ?;");
        $sql_delete_inalbum->bind_param('i', $photoid);

        $sql_delete_photo = $mysqli->prepare("DELETE from photo where photo_id = ?;");
        $sql_delete_photo->bind_param('i', $photoid);

        if( $sql_delete_inalbum->execute() && $sql_delete_photo->execute())
            $msg .= "Delete successfully.";
        else
            $msg .= "Delete error.";
    }

    if(isset($_POST['edit_photo_submit'])){
        $photoid = filter_input( INPUT_POST, 'edit_photo', FILTER_SANITIZE_NUMBER_INT );
        $title = filter_input( INPUT_POST, 'edit_photo_name', FILTER_SANITIZE_STRING );
        $album = $_POST['edit_photo_album'];

        if (!empty($title)) {
            if(!preg_match('/^[a-zA-Z0-9 ]+$/',$title) || strlen($title)>50) {
                $msg.="Invalid Photo Title. ";
                $error=true;
            }

            if (!$error){
                $title = ucfirst(strtolower($title));

                $sql = $mysqli->prepare("UPDATE photo SET photo_caption = ? where photo_id = ?;");
                $sql->bind_param('si', $title, $photoid);

                if($album){
                    $sql_2 = $mysqli->prepare("UPDATE photoinalbum set album_id = ? WHERE photo_id = ?;");
                    $sql_2->bind_param('ii', $album, $photoid);
                }
                else{
                    $sql_2 = $mysqli->prepare("UPDATE photoinalbum set album_id = null WHERE photo_id = ?;");
                    $sql_2->bind_param('i', $photoid);
                }

                if( $sql->execute() && $sql_2->execute() ) {
                    $msg.= 'Photo Edited.';
                }
                else{
                    $msg.= 'Edting fail.';
                }
            }
        }
        else{
            $msg .='Incomplete Photo Information.';
        }
        echo "<div><p>$msg</p><div>";
    }

?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
        <h3>Add Photo</h3>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="new_title" class="form-control">
            </div>
            <div class="form-group">
                <label>Album</label>
                <select name="new_album" class="form-control">
                    <?php
                        $sql = 'SELECT album_name, album_id FROM album;';
                        $result = $mysqli->query($sql);
                        if (!$result) {
                            print($mysqli->error);
                            exit();
                        }
                        echo "<option value='0' selected>---</option>";
                        while ($row = $result->fetch_assoc()){
                            $album_id = $row['album_id'];
                            echo "<option value='$album_id'>{$row['album_name']}</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>Image</label>
                <input type="file" name="new_url" class="form-control">
            </div>
            <div class="form-group">
                <input type="submit" name="new_photo_submit" value="Add new photo" class="btn primary" >
            </div>
        </form>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
        <h3>Edit Photo</h3>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Select a Photo</label>
                <select name="edit_photo" class="form-control" id="photo_select">
                    <?php
                        $sql = 'SELECT photo_caption, photo_id FROM photo;';
                        $result = $mysqli->query($sql);
                        if (!$result) {
                            print($mysqli->error);
                            exit();
                        }
                        echo "<option value='0' selected>---</option>";
                        while ($row = $result->fetch_assoc()){
                            $photo_id = $row['photo_id'];
                            echo "<option value='$photo_id'>{$row['photo_caption']}</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>View</label>
                <div id="view"></div>
            </div>
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="edit_photo_name" class="form-control">
            </div>
            <div class="form-group">
                <label>Album</label>
                <select name="edit_photo_album" class="form-control">
                    <?php
                        $sql = 'SELECT album_name, album_id FROM album;';
                        $result = $mysqli->query($sql);
                        if (!$result) {
                            print($mysqli->error);
                            exit();
                        }
                        echo "<option value='0' selected>---</option>";
                        while ($row = $result->fetch_assoc()){
                            $album_id = $row['album_id'];
                            echo "<option value='$album_id'>{$row['album_name']}</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <input type="submit" name="edit_photo_submit" value="Edit photo" class="btn primary" >
                <input type="submit" name="delete_photo" value="Delete Photo" onclick="return confirm('Are you sure to delete this album?')" class="btn primary">
            </div>
        </form>
        </div>
    </div>
</div>

<div class="space-buffer sm"></div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
        <h3>Add Album</h3>
        <form method="post">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="new_album_name" class="form-control">
            </div>
            <div class="form-group">
                <input type="submit" name="new_album_submit" value="Add new album" class="btn primary">
            </div>
        </form>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
        <h3>Edit Album</h3>
        <form method="post">
            <div class="form-group">
                <label>Album</label>
                <select name="select_album" class="form-control">
                    <?php
                        $sql = 'SELECT album_name, album_id FROM album;';
                        $result = $mysqli->query($sql);
                        if (!$result) {
                            print($mysqli->error);
                            exit();
                        }
                        echo "<option value='0' selected>---</option>";
                        while ($row = $result->fetch_assoc()){
                            $album_id = $row['album_id'];
                            echo "<option value='$album_id'>{$row['album_name']}</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>Name<label>
                <input type="text" name="edit_album_name" class="form-control">
            </div>
            <div class="form-group">
                <input type="submit" name="edit_album" value="Update Album" class="btn primary">
                <input type="submit" name="delete_album" value="Delete Album" onclick="return confirm('Are you sure to delete this album?')" class="btn primary">
            </div>
        </form>
        </div>
    </div>

</div>
<?php
}
?>