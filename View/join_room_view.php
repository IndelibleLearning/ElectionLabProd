<?php
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
?>

<html>
 <head>
  <title>Join Room</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
     <link rel="stylesheet" href="css/signup-view.css">
     <link rel="stylesheet" href="css/join-room-view.css">
 </head>
 <body>
    <section class="intro">
  <div class="bg-image h-100 join-room">
      <div class="join-room-elo-title-container">
          <img class="join-room-elo-title" src="images/hub_assets/joinroom_title.png"/>
      </div>
    <div class="mask align-items-center">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-12 col-md-10 col-lg-7 col-xl-6">
            <div class="card mask-custom">
              <div class="card-body p-5 text-white">

                <div class="my-4">

                  <h2 class="text-center join-room-title mb-5">Join Room</h2>

                  <div>
                    <!-- 2 column grid layout with text inputs for the first and last names -->
                    <div class="form-outline form-white mb-4">
                      <input type="text" id="room_code" class="form-control form-control-lg" name="room_code" />
                      <label class="form-label join-room-text" for="room_code">Room Code</label>
                    </div>
                      
                    <!-- Password input -->
                    <div class="form-outline form-white mb-4">
                      <input type="text" id="player_name" class="form-control form-control-lg" name="player_name" />
                      <label class="form-label join-room-text" for="player_name">Name</label>
                    </div>

                    <!-- Submit button -->
                    <button type="submit" id="submit" class="btn btn-light btn-block mb-4">Go</button>
                    <img class="indelible-logo" src="images/hub_assets/Logo_Flame_with_Name.png"/>
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
<script type="module" src="js/join_room_view.js"></script>
 </body>
</html>