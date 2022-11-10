<?php
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/RoomModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/TurnModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/StateEVModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/StateModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ColorModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $WHITE_ID = 1;
    
    $room_code = $_GET["room_code"];
    $game_id = $_GET["game_id"];

    $room_model = new RoomModel();
    $player_model = new PlayerModel();
    $game_model = new GameModel();
    $turn_model = new TurnModel();
    $state_ev_model = new StateEVModel();
    $state_model = new StateModel();
    $color_model = new ColorModel();
    
     // check room
    $room_response = $room_model->validate_room_by_code($room_code);
    if ($room_response->get_has_errors())
    {
        echo $room_response->get_json_error();
        die();
    }
    
    // check game
    $game_response = $game_model->validate_game_by_id($game_id);
    if ($game_response->get_has_errors())
    {
        echo $game_response->get_json_error();
        die();
    }
    $game = $game_response->get_data();
    
    // get players
    $player_response = $player_model->validate_players_in_game($game_id);
    if ($player_response->get_has_errors())
    {
        echo $player_response->get_json_error();
        die();
    }
    $players = $player_response->get_data();
    
    // map color ids to the players names, ids and EVs
    $color_EV_map = [];
    foreach($players as $player)
    {
        $player_entry = [];
        $player_entry["name"] = $player["player_name"];
        $player_entry["player_id"] = $player["id"];
        $player_entry["EVs"] = 0;
        $color_id = $player["color_id"];
        if ($color_id)
        {
            $color_response = $color_model->validate_color_by_id($color_id);
            if ($color_response->get_has_errors())
            {
                echo $color_response->get_json_error();
                die();
            }
            $color = $color_response->get_data();

            $player_entry["color_name"] = $color["name"];
        }
        
        $color_EV_map[$color_id] = $player_entry;
    }
    
    // get all the states
    $all_state_EVs = $state_ev_model->get_states_by_election_year($game["election_year_id"]);
    
    $max_white_EVs = 0;
    foreach($all_state_EVs as $state_EV)
    {
        if ($state_EV["initial_color_id"] == $WHITE_ID)
        {
            $max_white_EVs +=  $state_EV["electoral_votes"];
        }
        else 
        {
            $color_EV_map[$state_EV["initial_color_id"]]["EVs"] += $state_EV["electoral_votes"];
        }
    }
    
    $result = [];
    $result["initial_EVs"] = $color_EV_map;
    $result["max_swing_votes"] = $max_white_EVs;
    
    echo ApiResponse::success_data($result)->get_json();
    
    
    
    
    