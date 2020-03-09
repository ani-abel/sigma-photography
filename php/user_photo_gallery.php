<?php
/**
 * Created by PhpStorm.
 * User: WILDcard
 * Date: 6/7/2018
 * Time: 11:52 AM
 */
require_once("../includes/initialize.php");

if(!($session->is_logged_in() || isset($session->user_id))){ redirect_to("../index.php"); }

else{
    $rs = $database->query("SELECT first_name FROM users WHERE id='{$session->user_id}' LIMIT 1");
    global $first_name;
    $first_name = $database->fetch_array($rs)["first_name"];
}

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

    <title>Your Gallery ::Sigma</title>
</head>
<body>

<?php include_layout_template("modal_box.html"); ?>

<div class="container main-con">
    <header>
        <nav class="col-xs-12 col-sm-12 col-md-12 col-lg-12 nav-con" style="text-align: center;">
            <h4>
                <span><i class="fa fa-smile-o"></i></span> Hi <span class="text-success text-capitalize"><?php echo $first_name; ?></span>, check out all images in your gallery
            </h4>
        </nav>

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 nav-con" STYLE="text-align: center;">
            <h4>
                <a href="./user_add_photo.php" style="color:#010101;">
                    <span><i class="fa fa-plus"></i></span> ADD PHOTOS
                </a>&nbsp;&nbsp;&nbsp;
                <a href="../ajax_codes/user_logout.php" style="color:#010101;">
                    <span><i class="fa fa-power-off"></i></span> LOG OUT
                </a>
            </h4>
        </div>
    </header>

    <section class="header-con header-con-2 col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 site-logo-sec">
            <span><i class="fa fa-picture-o"></i></span>
            <h3 style="font-family: 'Viner Hand ITC'" class="text-success"><?php echo $first_name."'s"; ?> gallery</h3>
        </div>
    </section>

    <?php
        $rs = $database->query("SELECT * FROM images WHERE uploaded_by='{$session->user_id}' ORDER BY date_of_entry DESC");
        $__your_images = false;
        $all_img_lg = []; $all_img_sm = []; $all_images=[]; $upload_dates=[];
        if($rs){
            if($database->num_rows($rs) > 0){
                $__your_images = true;
                while($db_arr = $database->fetch_array($rs)) {
                    $all_images = $db_arr;
                    $date_of_entry =  $all_images['date_of_entry'];
                    $all_img_text = $all_images['img_uploaded'];

                    //push all valid dates into the @array => $upload_dates
                    if(!in_array($date_of_entry,$upload_dates)){ array_push($upload_dates, $date_of_entry); }

                    //push all 'copy' images into $all_img_sm
                    $explode_img = explode(",", $all_img_text);

                    foreach ($explode_img as $img){
                        if(!in_array($img, $all_img_sm)){
                            if(preg_match("/\/copies\//i", $img)){ array_push($all_img_sm, $img); }
                        }
                        if(!in_array($img, $all_img_lg)){
                            if(!preg_match("/\/copies\//i", $img)){ array_push($all_img_lg, $img); }
                        }
                    }
                }
                global $__img_count;
                $__img_count = count($all_img_lg);
            }
        }
    ?>
    <section class="main-gallery-con col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 sec-heading">
            <h4><span><i class="fa fa-image"></i></span> YOUR PHOTOS(<?php echo number_format($__img_count); ?>)</h4>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 all-gallery-img-con">
            <?php
                if($__your_images){
                    foreach($upload_dates as $key=>$date){
                        $get_img = $database->query("SELECT img_uploaded FROM images WHERE date_of_entry='{$date}' AND uploaded_by='{$session->user_id}' ORDER BY img_id DESC");

            ?>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 img-group-detail" style="background: #f5e79e !important;">
                <h5>
                    <span><i class="fa fa-calendar"></i></span> UPLOADS ON:
                    <span class="text-success" style="font-weight: bold;"><?php echo $date; ?></span>
                </h5>
            </div>
            <?php
               while($fetch = $database->fetch_array($get_img)){
                  $get_arr = [];
                  $get_arr = $fetch;
                  $explode_img = explode(",", $get_arr["img_uploaded"]);

                  $lg_arr = []; $sm_arr=[];
                  foreach ($explode_img as $img) {

                     if(!in_array($img, $sm_arr)){
                        if(preg_match("/\/copies\//i", $img)){ array_push($sm_arr, $img); }
                     }
                     if(!in_array($img, $lg_arr)){
                        if(!preg_match("/\/copies\//i", $img)){ array_push($lg_arr, $img); }
                     }
                  }
                  foreach ($sm_arr as $img_key=>$img){
            ?>
            <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 main-img-widget" title="Click to see full image">
                <img src="<?php echo $img; ?>" alt="IMAGE NOT FOUND" />
                <input type="hidden"
                       id="img-lg"
                       value="<?php echo $lg_arr[$img_key]; ?>" />
            </div>
            <?php } } } } else{ ?>

            <!--DEFAULT SECTION TO BE DISPLAYED IF THERE ARE NO IMAGES IN THE DB BEGINS HERE-->
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 default-div" style="background: #f8efc0; text-align: center; padding: 30px 10px;border-top: 1px solid #d4d4d4;">
                <span><i class="fa fa-image" style="color:#fff;font-size: 50px;"></i></span>
                <h4>NO IMAGES FOUND</h4>
            </div>
            <!--DEFAULT SECTION TO BE DISPLAYED IF THERE ARE NO IMAGES IN THE DB ENDS HERE-->
            <?php } ?>
        </div>
    </section>

    <?php include_once("../includes/footer.php"); ?>
</div>
</body>

<script>

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

</html>
