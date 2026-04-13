<form class="form w-100" id="login_form" method="post" action="#">
    @csrf
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