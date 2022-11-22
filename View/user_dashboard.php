<?php
    require dirname(dirname(dirname(__FILE__))) . "/inc/bootstrap.php";
?>

<html>
 <head>
  <title>Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <link rel="stylesheet" href="/test/david/View/css/signup-view.css">
  <link rel="stylesheet" href="/test/david/View/css/user_dashboard.css">
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
    
                      <h2 class="text-center mb-5">Dashboard</h2>
    
                      <div class="rooms_container">
                          <h1>Rooms</h1>
                          <div id="rooms_list" class="rooms_list">
                              No Rooms yet
                          </div>
                          <button id="create_room_button" class="create_room_button">Create New Room+</button>
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
    <div id="room_creation" class="hidden room_creation">
        <div class="room_create_input_container">
            <div id="close_button" class="close_button">X</div>
            <div>Room Name</div>
            <input id="room_name" type="text" />
            <button id="submit_new_room">Create Room</button>
        </div>
    </div>
    <script type="module" src="js/user_dashboard.js"></script>
 </body>
</html>