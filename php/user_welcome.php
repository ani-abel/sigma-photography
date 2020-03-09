<?php
/**
 * Created by PhpStorm.
 * User: WILDcard
 * Date: 6/7/2018
 * Time: 11:28 AM
 */
require_once("../includes/initialize.php");

if(!($session->is_logged_in() || isset($session->user_id))){ redirect_to("../index.php"); }

?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="../lib/bootstrap/bootstrap-3.3.7-dist/css/bootstrap-theme.min.css"/>
    <link rel="stylesheet" href="../lib/bootstrap/bootstrap-3.3.7-dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="../lib/font-awesome-4.7.0/css/font-awesome.min.css"/>
    <link rel="stylesheet" href="../css/main.css"/>

    <script src="../lib/bootstrap/bootstrap-3.3.7-dist/js/jquery-ui.min.js"></script>
    <script src="../lib/bootstrap/bootstrap-3.3.7-dist/js/jquery.min.js"></script>
    <script src="../lib/bootstrap/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>

    <title>User Welcome ::Sigma</title>
</head>
<body>
    <div class="container user-welcome-con">
        <div class="col-xs-8 col-xs-offset-2 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4 col-lg-4 col-lg-offset-4 dashboard-hold">
            <a href="./user_photo_gallery.php">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 dashboard-opt" style="border-bottom: 1px solid #f1a899 !important;">
                    <span><i class="fa fa-camera"></i></span>
                    <h5>VIEW YOUR GALLERY <span><i class="fa fa-arrow-right"></i></span></h5>
                </div>
            </a>

            <a href="./user_add_photo.php">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 dashboard-opt" style="border-bottom: 1px solid #f1a899 !important;">
                    <span><i class="fa fa-image"></i></span>
                    <h5>ADD TO GALLERY <span><i class="fa fa-arrow-right"></i></span></h5>
                </div>
            </a>
            <a href="../ajax_codes/user_logout.php">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 dashboard-opt">
                    <span><i class="fa fa-power-off"></i></span>
                    <h5>LOG OUT</h5>
                </div>
            </a>
        </div>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="file" name="file" accept="video/*" />
            <input type="submit" value="SUBMIT"/>
        </form>


        <script>
            $("form").on("submit", function(e){
                e.preventDefault();

                $.ajax({
                    url: "./intro_page.php",
                    cache: false,
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    type: "POST",
                    success: function (data){
                        alert(data);
                    }
                });
            });
        </script>
    </div>
</body>
</html>
