<?php
    require_once './config/config.php';
    require_once './config/session-check.php';

    unset($_SESSION['2fa_user_account_id']);

    $pageTitle = APP_NAME;
?>
<!DOCTYPE html>
<html lang="en" >
    <head>
       <?php 
            require_once './app/Views/Partials/head-meta-tags.php'; 
            require_once './app/Views/Partials/head-stylesheet.php';
        ?>
    </head>
    <body  id="kt_body" class="auth-bg bgi-size-cover bgi-attachment-fixed bgi-position-center" >
            <?php require_once './app/Views/partials/theme-script.html'; ?>
	    <div class="d-flex flex-column flex-root">
            <style>
                body {
                    background-image: url('./assets/media/auth/bg10.jpeg');
                }

                [data-bs-theme="dark"] body {
                    background-image: url('./assets/media/auth/bg10-dark.jpeg');
                }
            </style>
            <div class="d-flex flex-center flex-column-fluid flex-lg-row">
                <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12 p-lg-20">
                    <div class="bg-body d-flex flex-column align-items-stretch flex-center rounded-4 w-md-500px w-100 p-10">
                        <div class="d-flex flex-center flex-column flex-column-fluid px-0 pb-lg-10 pt-lg-10">
                            <form class="form w-100" id="login_form" method="post" action="#">
                                <div class="text-center mb-11">
                                    <h2 class="mb-2 mt-4 fs-1 fw-bolder">Login to your account</h2>
                                    <p class="mb-10 fs-5">Enter your email below to login to your account</p>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" class="form-control" id="email" name="email" autocomplete="off">
                                </div>
                                <div class="mb-8">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="position-relative mb-3">
                                        <input class="form-control" type="password" id="password" name="password" autocomplete="off" />

                                        <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2 password-addon">
                                            <i class="ki-outline ki-eye-slash fs-2 p-0"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button id="signin" type="submit" class="btn btn-primary">Sign In</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php 
            require_once './app/Views/Partials/error-modal.php';
            require_once './app/Views/Partials/required-js.php';        
        ?>

        <script type="module" src="./assets/js/auth/login.js?v=<?= rand(); ?>"></script>
    </body>
</html>