<?php
    require dirname(dirname(dirname(__FILE__))) . "/inc/bootstrap.php";
?>

<html>
 <head>
  <title>Sign up</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <link rel="stylesheet" href="/test/david/View/css/signup-view.css">
 </head>
 <body>
    <section class="intro">
  <div class="bg-image h-100" style="background-image: url('https://mdbootstrap.com/img/Photos/new-templates/glassmorphism-article/img5.jpg');">
    <div class="mask d-flex align-items-center h-100">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-12 col-md-10 col-lg-7 col-xl-6">
            <div class="card mask-custom">
              <div class="card-body p-5 text-white">

                <div class="my-4">

                  <h2 class="text-center mb-5">Register Form</h2>

                  <div>
                    <!-- 2 column grid layout with text inputs for the first and last names -->
                    <div class="form-outline form-white mb-4">
                      <input type="text" id="username" class="form-control form-control-lg" name="username" />
                      <label class="form-label" for="username">Username</label>
                    </div>
                      
                    <!-- Password input -->
                    <div class="form-outline form-white mb-4">
                      <input type="password" id="password" class="form-control form-control-lg" name="password" />
                      <label class="form-label" for="password">Password</label>
                    </div>


                    <!-- Email input -->
                    <div class="form-outline form-white mb-4">
                      <input type="email" id="email" class="form-control form-control-lg" name="email" />
                      <label class="form-label" for="email">Email </label>
                    </div>


                    <!-- Checkbox -->
                    <div class="form-check d-flex justify-content-center mb-4">
                      <input
                        class="form-check-input me-2"
                        type="checkbox"
                        value=""
                        id="form2Example3"
                        checked
                      />
                      <label class="form-check-label" for="form2Example3">
                        Subscribe to our newsletter
                      </label>
                    </div>

                    <!-- Submit button -->
                    <button type="submit" id="submit" class="btn btn-light btn-block mb-4">Sign up</button>

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
<script type="module" src="js/signup_view.js"></script>
 </body>
</html>