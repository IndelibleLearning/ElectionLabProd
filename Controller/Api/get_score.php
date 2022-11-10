<?php
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/RoomModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/TurnModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/StateEVModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    require_once PROJECT_ROOT_PATH . "/Controller/Api/RestHelper.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $room_code = $_GET["room_code"];
    $game_id = $_GET["game_id"];
    $turn_num = $_GET["turn_num"];
    

    $room_model = new RoomModel();
    $player_model = new PlayerModel();
    $game_model = new GameModel();
    $turn_model = new TurnModel();
    $state_ev_model = new StateEVModel();
    
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
    
    // get turn
    $turn_response = $turn_model->get_turns_by_game($game_id);
    $all_turns = $turn_response;
    
    // get players
    $player_response = $player_model->validate_players_in_game($game_id);
    if ($player_response->get_has_errors())
    {
        echo $turn_response->get_json_error();
        die();
    }
    $players = $player_response->get_data();
    
    
    $results = [];
    // create entry in results for each player
    foreach($players as $player)
    {
        $player_entry = [];
        $player_entry["player_id"] = $player["id"];
        $player_entry["player_name"] = $player["player_name"];
        $player_entry["color_id"] = $player["color_id"];
        $player_entry["EVs"] = 0; 
        
        $results[$player["id"]] = $player_entry;
    }
    
    // sum up all values
    foreach($all_turns as $turn)
    {
        $state_id = $turn["state_id"];
        $state_ev_response = $state_ev_model->validate_by_state_id($state_id);
        
        if ($state_ev_response->get_has_errors())
        {
            continue;
        }
        else
        {
            $state_ev = $state_ev_response->get_data();
            $num_votes = $state_ev["electoral_votes"];
            $winner_id = $turn["winner"];
            
            $results[$winner_id]["EVs"] += $num_votes; 
        }
    }
    
    // add initial EVs
    $url = CONTROLLER_API_PATH . "get_initial_EVs.php?room_code=$room_code&game_id=$game_id";
    $initial_EVs = RestHelper::rest_call($url)["data"]["initial_EVs"];
    foreach($results as $player_id => $score_data)
    {
        $results[$player_id]["EVs"] += $initial_EVs[$score_data["color_id"]]["EVs"];
    }
    
    
    $final_results = [];
    $final_results["player_scores"] = $results;
    $final_results["total_turns"] = $game["total_turns"] - 1;
    
     echo json_encode($final_results);
  
    