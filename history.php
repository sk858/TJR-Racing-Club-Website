<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include 'includes/head.php' ?>
        <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
  </head>
  <body>
    <?php
        $currentPage = "history";
        include 'includes/navbar.php'
    ?>

    <div class="container">
        <h1>History</h1>
    </div>

    <div class="container">
    <div class="row">
        <form method="post" enctype="multipart/form-data">
        <?php
            require_once 'includes/config.php';
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            if ($mysqli->errno) {
                print($mysqli->error);
                exit();
            }

            $sql = " SELECT distinct year FROM history ORDER BY year desc;";
            $result = $mysqli->query($sql);


            echo "<div class='col-md-1' id='year'>";

            while ($row = $result->fetch_assoc()) {
                $year = $row['year'];
                echo "<p><input type='button' class='btn' value ='$year'/><p>";
            }
            echo "</div>";

        ?>
        </form>
        <div class="col-md-8" id="pic"></div>
        <div class="col-md-3" id="content" style="margin:20px"></div>
        <?php include('includes/try_history.php'); ?>

    </div>
    </div>



    <?php include 'includes/footer.php' ?>
  </body>
</html>