<?php
    require dirname(dirname(dirname(__FILE__))) . "/inc/bootstrap.php";
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
    
    // get turns
    $turn_response = $turn_model->validate_turns_by_game($game_id);
    if ($turn_response->get_has_errors())
    {
        $all_turns = [];
    }
    else 
    {
        $all_turns = $turn_response->get_data();
    }
    
    // get players
    $player_response = $player_model->validate_players_in_game($game_id);
    if ($player_response->get_has_errors())
    {
        echo $player_response->get_json_error();
        die();
    }
    $players = $player_response->get_data();
    
    // map players names and colors to their id
    $player_map = [];
    foreach($players as $player)
    {
        $player_entry = [];
        $player_entry["name"] = $player["player_name"];
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

            $player_entry["color"] = $color["name"];
        }
        
        $player_map[$player["id"]] = $player_entry;
    }
    
    // get all the states
    $all_state_EVs = $state_ev_model->get_states_by_election_year($game["election_year_id"]);
    
    $results = [];
    
    // add all of the states from previous turns
    foreach($all_turns as $turn)
    {
        // get state ev
        $state_id = $turn["state_id"];
        $state_ev_response = $state_ev_model->validate_by_state_id($state_id);
        if ($state_ev_response->get_has_errors())
        {
            continue;
        }
        $state_ev = $state_ev_response->get_data();
        $winner_id = $turn["winner"];
        $winner_name = "";
        $winner_color = "";

        // only add entry with points if there was a winner
        // otherwise enter no one won
        if (!$winner_id)
        {
            $winner_id = -1;
            $winner_name = null;
            $winner_color = "white";
        }
        else
        {
            $winner_name = $player_map[$winner_id]["name"];
            $winner_color = $player_map[$winner_id]["color"];
        }
        
        // get state
        $state_results = $state_model->validate_by_id($state_id);
        if ($state_results->get_has_errors())
        {
            continue;
        }
        $state = $state_results->get_data();
        
        // now assemble the data since it's validated
        $entry = [];
        $entry["state_abbrev"] = $state["state_abbrev"];
        $entry["electoral_votes"] = $state_ev["electoral_votes"];
        $entry["winner_id"] = $winner_id;
        $entry["winner_name"] = $winner_name;
        $entry["color"] = $winner_color;

        $results[$state_id] = $entry;
    }
    
    // add all the white un-won states
    foreach($all_state_EVs as $state_EV)
    {
        $state_id = $state_EV["state_id"];
        // don't use non-white states or already won states
        if($state_EV["initial_color_id"] != $WHITE_ID || $results[$state_id] != null)   
        {
            continue;
        }
        
        
        // get state
        $state_results = $state_model->validate_by_id($state_id);
        if ($state_results->get_has_errors())
        {
            continue;
        }
        $state = $state_results->get_data();

        $entry = [];
        $entry["state_abbrev"] = $state["state_abbrev"];
        $entry["electoral_votes"] = $state_EV["electoral_votes"];
        $entry["winner_id"] = -1;
        $entry["winner_name"] = "";
        $entry["color_id"] = "white";

        $results[$state_id] = $entry;
    }
    
    echo json_encode($results);
    
    
    