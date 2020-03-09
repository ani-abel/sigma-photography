<?php
/**
 * Created by PhpStorm.
 * User: WILDcard
 * Date: 6/7/2018
 * Time: 11:52 AM
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

    <title>Add Photos ::Sigma</title>
</head>
<body>
<div class="container main-con">
    <header>
        <nav class="col-xs-12 col-sm-12 col-md-12 col-lg-12 nav-con" style="text-align: center;">
            <h4><span><i class="fa fa-smile-o"></i></span> Add images to your gallery</h4>
        </nav>

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 nav-con" STYLE="text-align: center;">
            <h4>
                <a href="./user_photo_gallery.php" style="color:#010101;">
                    <span><i class="fa fa-image"></i></span> YOUR GALLERY
                </a>&nbsp;&nbsp;&nbsp;
                <a href="../ajax_codes/user_logout.php" style="color:#010101;">
                    <span><i class="fa fa-power-off"></i></span> LOG OUT
                </a>
            </h4>
        </div>
    </header>

    <section class="main-gallery-con col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 sec-heading">
            <h4><span><i class="fa fa-plus"></i></span> ADD PHOTOS</h4>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 all-gallery-img-con">
            <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2 img-upload-f-hold">

                <form action="" method="post" enctype="multipart/form-data" id="f-add-images">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 form-element">
                        <label class="btn btn-block" for="my-file-selector" style="background: #00A07A; color: #fff; font-weight: bold; font-family: Consolas;border-radius:0;">
                            <input accept="image/*" id="my-file-selector" name="files[]" type="file" style="display:none;" multiple />
                            <span><i class="fa fa-image" style="color:#fff;"></i></span> Browse Photos
                        </label>
                    </div>

                    <input type="file" name="file_2" accept="image/*" />
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 display-all-selec-img" style="display:none;"></div>

                    <div class="col-xs-12 col-sm-12 col-sm-12 col-md-12 col-lg-12 form-element">
                        <input type="hidden"
                               name="date_of_entry"
                               id="date_of_entry"
                               value="<?php echo date("Y-m-d, h:i:sa"); ?>" />

                        <button type="submit"
                                class="btn btn-block submit-btn" id="submit-btn"
                                style="display: none;outline:none;">
                        </button>

                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 form-hold error-logs" style="word-break: break-all;"></div>
                </form>
            </div>

        </div>
    </section>

    <?php include_once("../includes/footer.php"); ?>
</div>


<script>
    var img_no = 0;

    $("#f-add-images").on("submit", function(e){
        e.preventDefault();

        $.ajax({
            url: "../ajax_codes/image_uploads_source.php",
            data: new FormData(this),
            cache: false,
            processData: false,
            contentType: false,
            type: "POST",
            success: function(data){
                var error_log = $(".error-logs");
                error_log.empty();
                if(error_log.html("") && !(data==="yes images added")){
                    error_log.html(data);

                    if(/SUCCESS: ALL/i.test(data)){
                        $("#f-add-images").trigger("reset");
                        $("#submit-btn").css("display", "none");
                    }
                }

                if(data=="yes images added"){
                    error_log.html("<h5 style='color:#00A07A;font-family: Consolas;font-weight: bold;'>SUCCESS: ALL "+img_no+" IMAGES ADDED</h5>");
                }
            }
        });
    });

    function previewImages(){
        var display_in = $(".display-all-selec-img");
        display_in.fadeIn("slow");
        display_in.empty();

        if(this.files) $.each(this.files, readAndPreview);

        function readAndPreview(i ,file){
            if(!/\.(jpg?g|png|gif)$/i.test(file.name)){
                $("#submit-btn").css("display", "none");

                return display_in.html("<h5 style='font-weight:bold;color:#101010;'><span style='color:indianred;'>ERROR: </span>INVALID FILE TYPE. ONLY <span style='color:indianred;'>JPEG, JPG, GIF & PNG</span> ARE ALLOWED</h5>");
            }
            else{
                var reader = new FileReader();
                $(reader).on("load", function(){
                    display_in.append("<div class=\"col-xs-10 col-xs-offset-1 col-sm-offset-0 col-sm-6 col-md-4 col-lg-3 upload-img-widget\"><img src=' " + this.result + " ' alt='' /> </div>");
                });
                $("#submit-btn").css("display", "block");

                reader.readAsDataURL(file);
            }
        }

        var btn = $("#submit-btn");

        (this.files.length > 1)?
            btn.html("<span><i class=\"fa fa-plus\"></i></span> ADD "+ this.files.length+" PHOTOS") :
            btn.html("<span><i class=\"fa fa-plus\"></i></span> ADD PHOTO");

        img_no = this.files.length;
    }

    $("#my-file-selector").on("change", previewImages);

</script>
</body>
</html>