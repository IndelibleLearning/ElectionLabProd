<?php
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/RoomModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/DeploymentModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/StateModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ModifierModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    header('Content-Type: application/json; charset=utf-8');

    $room_code = $_GET["room_code"];
    $game_id = $_GET["game_id"];

    $room_model = new RoomModel();
    $player_model = new PlayerModel();
    $game_model = new GameModel();
    $deployment_model = new DeploymentModel();
    $state_model = new StateModel();
    $modifier_model = new ModifierModel();
    
     // check room
    $room_response = $room_model->validate_room_by_code($room_code);
    if ($room_response->get_has_errors())
    {
        echo $room_response->get_json_error();
        die();
    }
    $room = $room_response->get_data();
    
    // check player
    $player_response = $player_model->validate_players_in_game($game_id);
    if ($player_response->get_has_errors())
    {
        echo $player_response->get_json_error();
        die();
    }
    $players = $player_response->get_data();
    
    // check game
    $game_response = $game_model->validate_game_by_id($game_id);
    if ($game_response->get_has_errors())
    {
        echo $game_response->get_json_error();
        die();
    }
    $game = $game_response->get_data();
    $game_id = $game["id"];

    $all_deployments = [];
    
    foreach($players as $player)
    {
        $player_entry = [];
        $player_id = $player["id"];
        // get deployments
        $deployments = $deployment_model->get_deployments($player_id, $game_id);
        
        // add state name to deployments
        for($i=0; $i<count($deployments); $i++)
        {
            $deployment = $deployments[$i];
            // get state
            $state_results = $state_model->validate_by_id($deployment["state_id"]);
            if ($state_results->get_has_errors())
            {
                continue;
            }
            $state = $state_results->get_data();
            
            // adjust with modification
            $modification = $modifier_model->get_player_modifications($player_id, $game_id, $state["id"]);

            $deployments[$i]["state_abbrev"] = $state["state_abbrev"];
            $deployments[$i]["num_pieces"] += $modification;
        }
        $player_entry["deployments"] = $deployments;
        $player_entry["color_id"] = $player["color_id"];
        $all_deployments[$player["player_name"]] = $player_entry;
    }
    

    
    echo ApiResponse::success_data($all_deployments)->get_json();
    
    
    
    