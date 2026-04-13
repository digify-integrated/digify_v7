<!DOCTYPE html>
<html lang="en" >
    <head>
        @include('partials.head-meta-tags')
        @include('partials.head-stylesheet')
    </head>
    <body  id="kt_body" class="auth-bg bgi-size-cover bgi-attachment-fixed bgi-position-center">
        @include('partials.theme-script')
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
                            {{ content }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('partials.error-modal')
        @include('partials.required-js')

        <script type="module" src="./assets/js/auth/login.js?v=<?= rand(); ?>"></script>
    </body>
</html>