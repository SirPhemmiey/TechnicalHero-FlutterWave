<div class="main-container">
            <!-- sign up container -->
            <section class="cover unpad--bottom switchable text-center-xs">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-11 col-md-6 mt--3 mx-auto login-wrapper" aria-live="assertive">
                            <h4 class="form-title">
                               Sign up
                            </h4>
                            <?php if (isset($msg)) {?>
                                <div class="alert alert-<?php echo isset($color) ? $color : '';?> alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">x</button>
                            <?php echo isset($msg) ? $msg: ''?>
                            </div>
                            <?php }?>
                            <form action="<?php echo $this->webroot?>users/signup" role="form" method="post">
                                <div class="form-group form-group-default">
                                    <label>Name</label>
                                    <input type="text" class="form-control" required name="data[Info][name]" aria-label="enter your name"
                                    placeholder="enter your name">
                                </div>
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
                                <div class="form-group form-group-default">
                                    <label>Phone</label>
                                    <input type="tel" class="form-control" required name="data[Info][phone]" aria-label="enter your phone number"
                                    placeholder="enter your phone number">
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary btn-block" aria-label="Log in">Create account</button>
                                </div>
                                <hr>
                                <div class="col-md-12 text-center">
                                    <div>Already have an account?
                                        <a href="<?php echo $this->webroot?>users/login" class="login-link">Login</a>
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