<?php
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
?>

<html>
 <head>
  <title>Login</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <link rel="stylesheet" href="/test/david/View/css/signup-view.css">
  <link rel="stylesheet" href="/test/david/View/css/login-view.css">
 </head>
 <body>
    <section class="intro">
  <div class="bg-image h-100">
    <div class="mask d-flex align-items-center h-100">
      <div class="container">
        <div class="title">
          <image class="title-image" src="images/LabTitle2.svg"/>
        </div>
        <div class="row justify-content-center">
          <div class="col-12 col-md-10 col-lg-7 col-xl-6">
            <div class="card mask-custom">
              <div class="card-body p-5 text-white">

                <div class="my-4">
                  <h2 class="text-center mb-5">Login</h2>

                  <div>
                    <!-- 2 column grid layout with text inputs for the first and last names -->
                    <div class="form-outline form-white mb-4">
                      <input type="text" id="username" class="form-control form-control-lg" name="user_name" />
                      <label class="form-label" for="username">Username</label>
                    </div>
                      
                    <!-- Password input -->
                    <div class="form-outline form-white mb-4">
                      <input type="password" id="password" class="form-control form-control-lg" name="password" />
                      <label class="form-label" for="password">Password</label>
                    </div>



                    <!-- Submit button -->
                      <button type="submit" id="submit" class="btn btn-light btn-block mb-4">Login</button>
                      <!-- Automatic rendering -->
                      <button type="submit" id="google-submit" class="btn btn-light btn-block mb-4">google</button>

                  </div>

                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script type="module" src="js/login_view.js"></script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script src="https://apis.google.com/js/platform.js" async defer></script>
 </body>
</html>