<?php
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
?>

<html>
 <head>
  <title>Player List</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <link rel="stylesheet" href="/test/david/View/css/signup-view.css">
  <link rel="stylesheet" href="/test/david/View/css/player_list_view.css">
 </head>
 <body>
    <section class="intro">
      <div class="main-bg">
        <div class="mask d-flex align-items-center">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-12 col-md-10 col-lg-7 col-xl-6">
                  <h1 class="title">Room Code: <span id="title_room_code"></span></h1>
                  <div class="instructions">
                      <div class="join_prompt">To join, go to:</div>
                      <div class="join_link" id="join-link">https://indeliblelearning.com/electionlab</div>
                  </div>
                  <div id="player_name_container" class="hidden player_name_container">
                      You are joined as <span id="player_name_display"></span>
                  </div>
                <div class="card mask-custom matched_players">
                  <div class="card-body p-5 text-white">
                    <div class="my-4">
                        <button id="join_game_button" class="hidden match_players_button">Play Current Game</button>
                         <button id="new_game_button" class="hidden new_game_button">New Game</button>
                      <h2 class="text-center mb-5">Currently Playing</h2>
    
                      <div id="matched_player_list">
                        
                      </div>
                    </div>
    
                  </div>
                </div>
                <div class="card mask-custom unmatched_players">
                  <div class="card-body p-5 text-white">
    
                    <div class="my-4">
                        <div class="form-check form-switch match_players_button_container hidden" id="match_players_button_container">
                          <input class="form-check-input" type="checkbox" role="switch" id="match_players_button">
                          <label class="form-check-label match_players_label" for="match_players_button">Start matches</label>
                        </div>
                        <div class="form-check form-switch event_mode_button_container hidden" id="event_mode_button_container">
                          <input class="form-check-input" type="checkbox" role="switch" id="event_mode_button">
                          <label class="form-check-label event_mode_label" for="event_mode_button">Use Events</label>
                        </div>
                        <h2 class="text-center mb-5">Unmatched Players</h2>
    
                      <div id="unmatched_player_list">
                        
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
    <script type="module" src="js/player_list_view.js"></script>
 </body>
</html>