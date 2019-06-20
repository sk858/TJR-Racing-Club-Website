<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include 'includes/head.php' ?>

    <link href='css/simplelightbox.min.css' rel='stylesheet' type='text/css'>

    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>

    <script src="https://npmcdn.com/imagesloaded@4.1/imagesloaded.pkgd.min.js"></script>
    <script src="includes/simple-lightbox.js"></script>
    <script src="includes/jquery.lightroom.js"></script>

  </head>
  <style>
/* Center the loader */
#loader {
  position: absolute;
  left: 50%;
  top: 50%;
  z-index: 1;
  width: 100px;
  height: 100px;
  margin: -75px 0 0 -75px;
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid #3498db;
  width: 80px;
  height: 80px;
  -webkit-animation: spin 2s linear infinite;
  animation: spin 2s linear infinite;
}

@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Add animation to "page content" */
.animate-bottom {
  position: relative;
  -webkit-animation-name: animatebottom;
  -webkit-animation-duration: 1s;
  animation-name: animatebottom;
  animation-duration: 1s
}

@-webkit-keyframes animatebottom {
  from { bottom:-100px; opacity:0 } 
  to { bottom:0px; opacity:1 }
}

@keyframes animatebottom { 
  from{ bottom:-100px; opacity:0 } 
  to{ bottom:0; opacity:1 }
}

</style>
  <body >
    <?php
        $currentPage = "gallery";
        include 'includes/navbar.php'
    ?>


    <?php
        require_once 'includes/config.php';
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if ($mysqli->errno) {
            print($mysqli->error);
            exit();
        }
    ?>

    <div class="container">
        <h1>Gallery</h1>
    </div>
    <div class="container">
        <form>
            <div>
                <label>Filter images: </label>
                <select id="type_select" class="form-control">
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
                <div class="space-buffer sm"></div>
            </div>
        </form>
		<div id="loader"></div>
        <div id="jLightroom" class="jlr gray_out"></div>
        


        <?php include('includes/try.php'); ?>
    </div>

    <?php include 'includes/footer.php' ?>
  </body>
</html>