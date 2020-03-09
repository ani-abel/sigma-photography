<?php
/**
 * Created by PhpStorm.
 * User: WILDcard
 * Date: 6/7/2018
 * Time: 1:08 AM
 */
require_once("includes/initialize.php");

if($session->is_logged_in()){ redirect_to("php/user_welcome.php"); }

?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="lib/bootstrap/bootstrap-3.3.7-dist/css/bootstrap-theme.min.css"/>
    <link rel="stylesheet" href="lib/bootstrap/bootstrap-3.3.7-dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="lib/font-awesome-4.7.0/css/font-awesome.min.css"/>
    <link rel="stylesheet" href="css/main.css"/>

    <script src="lib/bootstrap/bootstrap-3.3.7-dist/js/jquery-ui.min.js"></script>
    <script src="lib/bootstrap/bootstrap-3.3.7-dist/js/jquery.min.js"></script>
    <script src="lib/bootstrap/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>

    <title>Sigma:: Home</title>
</head>
<body>

<?php include_layout_template("modal_box.html"); ?>

    <div class="container main-con">
        <header>
            <nav class="col-xs-12 col-sm-12 col-md-12 col-lg-12 nav-con">
                <div class="hidden-xs col-sm-2 col-md-1 col-lg-1" style="padding:5px 0 0 0;text-align: center;">
                    <span class="span-logo"><i class="fa fa-camera" ></i></span>
                </div>
                <div class="col-xs-12 col-sm-10 col-md-11 col-lg-11 nav-links" style="padding:0;">
                    <ul>
                        <li>
                            <button type="button" data-toggle="modal" data-target="#modal-1" style="outline:none;">
                                <h4 style="margin:0;">LOG IN</h4>
                            </button>
                        </li>
                    </ul>

                <!--MODAL-BOX BEGINS HERE-->
                    <div class="modal" id="modal-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <button type="button" data-dismiss="modal"><span><i class="fa fa-times"></i></span></button>
                                    <h3 style="text-align: center;font-weight: bold;">LOG IN</h3>

                                    <div class="container-fluid modal-f-hold">
                                        <span><i class="fa fa-camera"></i></span>

                                        <form action="" method="post" id="log-in-form">
                                            <div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2 form-element-con">
                                                <input type="text"
                                                       class="form-control form-element"
                                                       placeholder="USERNAME"
                                                       maxlength="20"
                                                       name="username"
                                                       required
                                                       spellcheck="false"
                                                       autocomplete="off"
                                                       value="<?php echo (isset($_COOKIE['sigma_username']))?$_COOKIE['sigma_username']:""; ?>" />
                                            </div>

                                            <div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2 form-element-con">
                                                <input type="password"
                                                       required
                                                       class="form-control form-element"
                                                       placeholder="PASSWORD"
                                                       autocomplete="off"
                                                       name="password"
                                                       maxlength="12"
                                                       value="<?php echo (isset($_COOKIE['sigma_password']))?$_COOKIE['sigma_password']:""; ?>"/>
                                            </div>

                                            <div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2 form-element-con" style="text-align: left;padding: 0 0 0 15px;">
                                                <label class="edit-link invite-txt-2" id="edit-link remember-me" style="cursor: pointer;">
                                                    <input type="checkbox" name="remember_me" checked /> Remember Me
                                                </label>
                                            </div>
                                            <div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2 form-element-con error-log" style="padding: 0 0 0 15px;text-align: left;"></div>

                                            <div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2 form-element-con">
                                                <button class="btn btn-block submit-btn" type="submit" style="outline:none;">
                                                    <span><i class="fa fa-user"></i></span> LOG IN
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <!--MODAL-BOX ENDS HERE-->
                </div>
            </nav>

            <section class="header-con col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 site-logo-sec">
                    <span><i class="fa fa-camera"></i></span>
                </div>

                <div class="hidden-xs col-sm-12 col-md-9 col-lg-9 site-welcome-pics-hold">
                    <div class="img-widgets col-xs-12 col-sm-6 col-md-4 col-lg-4">
                        <img src="site_images/computer-motherboard-pc-wires.jpg" alt="">
                    </div>
                    <div class="img-widgets col-xs-12 col-sm-6 col-md-4 col-lg-4">
                        <img src="site_images/conserve16.jpg" alt="">
                    </div>
                    <div class="img-widgets col-xs-12 col-sm-6 col-md-4 col-lg-4">
                        <img src="site_images/1401x788-07_GROUP_3_1142-91865269_Cal-Aurand.jpg" alt="">
                    </div>
                    <div class="img-widgets col-xs-12 col-sm-6 col-md-4 col-lg-4">
                        <img src="site_images/conserve36.jpg" alt="">
                    </div>
                    <div class="img-widgets col-xs-12 col-sm-6 col-md-4 col-lg-4">
                        <img src="site_images/pexels-photo-330771.jpeg" alt="">
                    </div>
                    <div class="img-widgets col-xs-12 col-sm-6 col-md-4 col-lg-4 widget">
                        <img src="site_images/conserve24.jpg" alt="">
                    </div>
                </div>
            </section>
        </header>


        <?php
        $rs = $database->query("SELECT * FROM images ORDER BY img_id DESC");
        $rows=[];
        $__users_who_upload=[];
        $hide_footer = false;
        $__all_img_arr = [];
        while ($row = $database->fetch_array($rs)){
            $rows = $row;
            $__all_imgA = explode(",",$rows['img_uploaded']);

            foreach($__all_imgA as $__all_img) {
                if (!in_array($__all_img, $__all_img_arr)) {
                    if (preg_match("/\/copies\//i", $__all_img)) {
                        array_push($__all_img_arr, $__all_img);
                    }
                }
            }

            if(!in_array($rows['uploaded_by'], $__users_who_upload)) array_push($__users_who_upload, $rows['uploaded_by']);
        }

        ?>
        <section class="main-gallery-con col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 sec-heading">
                <h4>PHOTO GALLERY(<?php echo number_format(count($__all_img_arr)); ?>)</h4>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 all-gallery-img-con">
                <?php
                    if($database->num_rows($rs) > 0){

                        foreach ($__users_who_upload as $user_id){
                            $img_sel = $database->query("SELECT *, users.username FROM images INNER JOIN users ON images.uploaded_by=users.id WHERE images.uploaded_by='{$user_id}'");

                            if($img_sel){
                                $rs = $database->fetch_array($img_sel);
                                $detail_arr = [];
                                //get the db_table->images fields & store in variables
                                $username = $rs['username'];

                ?>
                <!--SECTION TO BE DISPLAYED IF THERE ARE IMAGES IN THE DB BEGINS HERE-->
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 img-group-detail" style="background: #f5e79e !important;">
                    <h5>
                        <span><i class="fa fa-image"></i></span> IMAGES BY:
                        <span class="text-success" style="font-weight: bold;">@<?php echo $username; ?></span>
                    </h5>
                </div>

                <?php
                    $get_images = $database->query("SELECT img_uploaded FROM images WHERE uploaded_by='{$user_id}'");

                    if($get_images){
                        $in_row2 = [];
                        while($all_user_img = $database->fetch_array($get_images)){
                           $in_row2 = $all_user_img;
                           $img_text = $in_row2["img_uploaded"];
                           $img_text_arr = explode(",",$img_text);

                           $all_img_sm=[]; $all_img_lg = [];

                           foreach ($img_text_arr as $key=>$item){


                               if(!(preg_match("/\/copies\//i", $item))){
                                   //add to large-img array
                                   if(!in_array($item, $all_img_lg)){
                                       array_push($all_img_lg, $item);
                                   }
                               }
                               else if(preg_match("/\/copies\//i", $item)){
                                   //add the small-img array
                                   if(!in_array($item, $all_img_sm)){
                                       array_push($all_img_sm, $item);
                                   }
                               }
                           }

                           foreach ($all_img_sm as $key=>$img){

                ?>
                <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 main-img-widget" title="Click to see full image">
                    <img src="<?php echo str_replace("../", " ", $img); ?>"
                         alt="<?php echo str_replace("../", " ", $img); ?>"/>
                    <input type="hidden"
                           id="img-lg"
                           value="<?php echo str_replace("../"," ",$all_img_lg[$key]); ?>" />
                </div>
                    <!--SECTION TO BE DISPLAYED IF THERE ARE IMAGES IN THE DB ENDS HERE-->
                <?php } } } } } } else{ $hide_footer = true; ?>

                <!--DEFAULT SECTION TO BE DISPLAYED IF THERE ARE NO IMAGES IN THE DB BEGINS HERE-->
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 default-div" style="background: #f8efc0; text-align: center; padding: 30px 10px;border-top: 1px solid #d4d4d4;">
                    <span><i class="fa fa-image" style="color:#fff;font-size: 50px;"></i></span>
                    <h4>NO IMAGES FOUND</h4>
                </div>
                <!--DEFAULT SECTION TO BE DISPLAYED IF THERE ARE NO IMAGES IN THE DB ENDS HERE-->
                <?php } ?>

            </div>

        </section>

        <?php if(!$hide_footer) { include_once("includes/footer.php"); } ?>

    </div>

<script>
    $("#log-in-form").on("submit", function(e){
        e.preventDefault();

        $.ajax({
            url: "ajax_codes/login_ajax_source.php",
            data: new FormData(this),
            cache: false,
            processData: false,
            contentType: false,
            type: "POST",
            success: function(data){
                var error_log = $(".error-log");

                if(/user correct/ig.test(data)){ location.href = "php/user_welcome.php"; }
                else{
                    error_log.empty();
                    if(error_log.html("")){ (data === "user correct") ? error_log.empty() : error_log.html(data); }
                }
            }
        });
    });

    $(".main-img-widget").click(function(e){
        e.preventDefault();
        $(".my-modal").css("display", "block");

        var img_url = $(this).find("#img-lg");
        $("#modal-img").attr("src", img_url.val());
    });

    $(".close-my-modal").click(function(){
        $(".my-modal").css("display", "none");
    });

</script>

</body>
</html>
