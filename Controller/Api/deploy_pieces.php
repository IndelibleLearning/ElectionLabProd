<?php
    require dirname(dirname(dirname(__FILE__))) . "/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/RoomModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/DeploymentModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/StateModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $PER_STATE_MIN=0;
    $PER_STATE_MAX=5;
    $PIECES_TOTAL_MAX = 24;
    
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $room_code = $data["room_code"];
    $player_name = $data["player_name"];
    $game_id = $data["game_id"];

    $room_model = new RoomModel();
    $player_model = new PlayerModel();
    $game_model = new GameModel();
    $deployment_model = new DeploymentModel();
    $state_model = new StateModel();
    
     // check room
    $room_response = $room_model->validate_room_by_code($room_code);
    if ($room_response->get_has_errors())
    {
        echo $room_response->get_json_error();
        die();
    }
    $room = $room_response->get_data();
    
    // check player
    $player_response = $player_model->validate_player_in_room($room["id"], $player_name);
    if ($player_response->get_has_errors())
    {
        echo $player_response->get_json_error();
        die();
    }
    $player_id = $player_response->get_data()["id"];
    
    // check game
    $game_response = $game_model->validate_game_by_id($game_id);
    if ($game_response->get_has_errors())
    {
        echo $game_response->get_json_error();
        die();
    }
    $game = $game_response->get_data();
    $game_id = $game["id"];

    // process deployments
    $deployments = $data["deployments"];
    
    // Create validated list of state deployments
    // validate based on:
    // - total number of pieces placed is 24
    // - each deployment has betweeon 0-5 pieces
    // - each state is valid
    $validated_deployments = [];
    $total_sum = 0;
    foreach($deployments as $deployment)
    {
        $pieces = $deployment["pieces"];
        $state = $deployment["state"];
        
        if ($pieces < $PER_STATE_MIN || $pieces > $PER_STATE_MAX)
        {
            echo ("Can't deploy " . $pieces . "pieces");
            continue;
        }
        
        $state_result = $state_model->get_state_by_abbrev($state);
        if (!$state_result || count($state_result) <= 0)
        {
            echo ("Could not find state " . $state);
            continue;
        }
        $state_id = $state_result[0]["id"];
        
        // valid so add to deployments
        $this_deployment["state_id"] = $state_id;
        $this_deployment["pieces"] = $pieces;
        array_push($validated_deployments, $this_deployment);
        $total_sum += $pieces;
    }
    
    // eventually validate based on total number of pieces 
    // but don't right now for testing
    if ($total_sum != $PIECES_TOTAL_MAX)
    {
        // print a message and call die()
    }
    
    // error code info
    $err_msg = "Error when inserting deployments";
    $err_code = "inserting_deployments";
    $valid_deployments = [];

    foreach($validated_deployments as $deployment)
    {
        $pieces = $deployment["pieces"];
        $state_id = $deployment["state_id"];
        
        $response =  $deployment_model->create_deployment($player_id, $game_id, $state_id, $pieces);
        
        $api_response = ApiResponse::validate_not_null_or_blank($response, $err_code, $err_msg);
        
        if (!$api_response->get_has_errors())
        {
            array_push($valid_deployments, $response);
        }
        
    }
    

    $final_response = null;
    if (count($valid_deployments) <= 0)
    {
        $final_response =  new ApiResponse(null, true, $err_code, $err_msg);
    }
    else
    {
        $final_response = ApiResponse::success_data($valid_deployments);
    }
    
    echo $final_response->get_json();
    
    