<?php
    require dirname(dirname(dirname(__FILE__))) . "/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/TurnModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/StateModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $game_id = $_GET["game_id"];

    $game_model = new GameModel();
    $turn_model = new TurnModel();
    $state_model = new StateModel();

    // check game
    $game_response = $game_model->validate_game_by_id($game_id);
    if ($game_response->get_has_errors())
    {
        echo $game_response->get_json_error();
        die();
    }
    $game = $game_response->get_data();
    
    $latest_turn = $game["total_turns"] - 1;
    
    // get turn
    $turn_results = $turn_model->validate_turn($game_id, $latest_turn);
    if ($turn_results->get_has_errors())
    {
        echo $turn_results->get_json_error();
        die();
    }
    $turn = $turn_results->get_data();
    $state_id_for_turn = $turn["state_id"];
    
    // get state
    $state_results = $state_model->validate_by_id($state_id_for_turn);
    if ($state_results->get_has_errors())
    {
        echo $state_results->get_json_error();
        die();
    }
    $state = $state_results->get_data();
    
    $results = [];
    
    $results["latest_turn"] = $latest_turn;
    $results["state_abbrev"] = $state["state_abbrev"];
    
    echo ApiResponse::success_data($results)->get_json();
    
    
    