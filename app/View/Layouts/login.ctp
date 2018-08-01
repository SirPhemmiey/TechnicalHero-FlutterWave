<!doctype html>
<html lang="en" class="login-page">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- fonts-->
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,600,700,800" rel="stylesheet">
    <!-- Stylesheet-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css"> <div></div>
        <link rel="stylesheet" href="<?php echo $this->webroot?>css/style.css">
    <link rel="stylesheet" href="<?php echo $this->webroot?>css/media.css">
    <!-- COMMON TAGS -->
    <title>FlutterWave</title>
</head>

<body>
    <div class="login-content" aria-live="assertive">
        <div class="nav-container">
            <!--end bar-->
            <nav id="menu1" class="bar bar--sm bar-1 hidden-xs bar--absolute">
                <div class="container">
                    <div class="row">
                        <div class="col-md-1 col-sm-4 mx-auto">
                            
                            <!--end module-->
                        </div>
                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->
            </nav>
            <!--end bar-->
        </div>
        <?php echo $this->fetch('content');?>
    </div>
    <script src="<?php echo $this->webroot?>js/bootstrap.min.js"></script>
    <script src="<?php echo $this->webroot?>js/jquery-3.1.1.min.js"></script>
</body>

</html>