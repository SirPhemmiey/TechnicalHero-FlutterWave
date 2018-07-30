<div class="main-container">
            <!-- sign up container -->
            <section class="cover unpad--bottom switchable text-center-xs">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-11 col-md-6 mt--3 mx-auto login-wrapper" aria-live="assertive">
                            <h4 class="form-title">
                                Log in
                            </h4>
                            <?php if (isset($msg)) {?>
                                <div class="alert alert-<?php echo isset($color) ? $color : '';?> alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">x</button>
                            <?php echo isset($msg) ? $msg: ''?>
                            </div>
                            <?php }?>
                            <form action="<?php echo $this->webroot?>users/login" role="form" method="post">
                                <div class="form-group form-group-default">
                                    <label>Email</label>
                                    <input type="email" class="form-control" required name="data[Login][email]" aria-label="enter your email address"
                                    placeholder="enter your email address">
                                </div>
                                <div class="form-group form-group-default">
                                    <label>Password</label>
                                    <input type="password" class="form-control" required aria-label="enter your password"
                                    placeholder="enter your password" name="data[Login][password]">
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary btn-block" aria-label="Log in">Log in</button>
                                </div>
                                <hr>
                                <div class="col-md-12 text-center">
                                    <div>Are you new?
                                        <a href="<?php echo $this->webroot?>users/signup" class="login-link">Sign up</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->
            </section>
        </div>