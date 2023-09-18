<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Election Lab Online</title>
    <!-- Bootstrap -->

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/game_view.css">
    <link rel="stylesheet" href="css/event-card-hand.css">
    <link rel="stylesheet" href="css/fonts/fonts.css">
  </head>
  <body id="main">
      <div class="header">
          <button id="back_to_lobby" class="back_to_lobby">Back to Lobby</button>
          <div class="game-title">
                Election Lab
          </div>
          <button id="help-button" class="help-button">Help</button>
      </div>
	  
      <div id="game_entry" class="elo_test_section game_details hidden">
		  <label for="room_code">Room Code:</label>
		  <input type="text" id="room_code" maxlength="6" size="10"> 
		  <button id="save_room_code">Save</button><br>
		  
		  <label for="player_name">Player Name:</label>
		  <input type="text" id="player_name" maxlength="20" size="30"> 
		  <button id="save_player_name">Save</button><br>
		  
		  <label for="game_id">Game ID:</label>
		  <input type="text" id="game_id" maxlength="4" size="6"> 
		  <button id="save_game_id">Save</button><br><br>
		  
		  <div>
		      <button id="start_game">Start Game</button>
		  </div>
	  </div>

      <div id="game-terminated" class="game-terminated hidden">
          <div class="game-terminated-container">
              <div class="game-terminated-modal">
                  <h1>Game has been ended</h1>
                  <button id="game-terminated-button">Return to lobby</button>
              </div>
          </div>
      </div>

	  <?php
         include 'game_common_view.php';
         include 'modal.php';
         include 'game_finish_view.php';
         include 'game_postgame_view.php';
         include 'game_map_view.php';
         include 'tutorials/game_tutorials.php'
      ?> 
      
      <!-- HAND -->
  	  <div id="event_card_hand" class="event_card_hand">
      </div>
      
	  <!-- DEPLOYMENTS -->
	  <?php
        include 'game_deployments_view.php';
        include 'game_create_turn_view.php';
	    include 'game_event_card_view.php';
        include 'game_roll_view.php';
        include 'effect_views/effect_plus_view.php';
        include 'effect_views/effect_shift_view.php';
      ?>

	  <script src="js/jquery-3.4.1.min.js"></script>
	  <script src="js/popper.min.js"></script>
	  <script src="js/bootstrap.min.js"></script>
	  <script type="module" src="js/game_common.js"></script>
	  <script type="module" src="js/game_view.js"></script>
	  <script type="module" src="js/game_map.js"></script>
	  <script type="module" src="js/game_deployments.js"></script>
	  <script type="module" src="js/game_create_turn.js"></script>
	  <script type="module" src="js/game_event_card.js"></script>
	  <script type="module" src="js/game_event_card_wait.js"></script>
	  <script type="module" src="js/game_roll.js"></script>
	  <script type="module" src="js/game_finish.js"></script>
	  <script type="module" src="js/game_postgame.js"></script>
	  <script type="module" src="js/refresh_hand.js"></script>
      <script type="module" src="js/modal.js"></script>

  </body>
</html>