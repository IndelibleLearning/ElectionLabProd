<?php
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/RoomModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/DeploymentModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/TurnModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/StateModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $room_code = $_GET["room_code"];
    $game_id = $_GET["game_id"];
    $state_abbrev = $_GET["state_abbrev"];
    
    $room_model = new RoomModel();
    $game_model = new GameModel();
    $deployment_model = new DeploymentModel();
    $turn_model = new TurnModel();
    $state_model = new StateModel();
    
    // get room
    $room_response = $room_model->validate_room_by_code($room_code);
    if ($room_response->get_has_errors())
    {
        echo $room_response->get_json_error();
        die();
    }
    
    // get game
    $game_response = $game_model->validate_game_by_id($game_id);
    if ($game_response->get_has_errors())
    {
        echo $game_response->get_json_error();
        die();
    }
    $game = $game_response->get_data();
    $game_id = $game["id"];
    $total_turns = $game["total_turns"];
    
    // get state
    $state_response = $state_model->validate_state_by_abbrev($state_abbrev);
    if ($state_response->get_has_errors())
    {
        echo $state_response->get_json_error();
        die();
    }
    $state = $state_response->get_data();
    $state_id = $state["id"];
    
    // make sure we have not already had a turn for this state
    // get turn
    $turn_results = $turn_model->validate_already_did_state($game_id, $state_id);
    if ($turn_results->get_has_errors())
    {
        echo $turn_results->get_json_error();
        die();
    }
    $turn = $turn_results->get_data();
    $turn_id = $turn["id"];
    $state_id_for_turn = $turn["state_id"];
    
    // create turn
    $turn_model->create_turn($game_id, $total_turns, $state_id);
    
    // set this games turn to the created turn
    $game_model->set_total_turns($game_id, $total_turns + 1);
    
    // return back success with the number of the turn
    $result= [];
    $result["turn_num"] = $total_turns;
    echo ApiResponse::success_data($result)->get_json();