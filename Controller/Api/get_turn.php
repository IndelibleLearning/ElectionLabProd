<?php
    require dirname(dirname(dirname(__FILE__))) . "/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/TurnModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/StateModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/StateEVModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/DeploymentModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $game_id = $_GET["game_id"];
    $turn_num = $_GET["turn_num"];
    
    $game_model = new GameModel();
    $turn_model = new TurnModel();
    $state_model = new StateModel();
    $state_ev_model = new StateEVModel();
    $player_model = new PlayerModel();
    $deployment_model = new DeploymentModel();
    
    // get game
    $game_response = $game_model->validate_game_by_id($game_id);
    if ($game_response->get_has_errors())
    {
        echo $game_response->get_json_error();
        die();
    }

    $turn_results = $turn_model->validate_get_turn($game_id, $turn_num);
    if ($turn_results->get_has_errors())
    {
        echo $turn_results->get_json_error();
        die();
    }
    $turn = $turn_results->get_data();
    $state_id = $turn["state_id"];
        
    // get state abbrev
    $state_response = $state_model->validate_by_id($state_id);
    if ($state_response->get_has_errors())
    {
        echo $state_response->get_json_error();
        die();
    }
    $state = $state_response->get_data();
    $state_abbrev = $state["state_abbrev"];
    
    // get state Evs
    $state_ev_response = $state_ev_model->validate_by_state_id($state["id"]);
    if ($state_ev_response->get_has_errors())
    {
        echo $state_ev_response->get_json_error();
        die();
    }
    $state_ev = $state_ev_response->get_data();    
    $EVs = $state_ev["electoral_votes"];

    $turn["state_abbrev"] = $state_abbrev;
    $turn["EVs"] = $EVs;

    // create turn
    echo ApiResponse::success_data($turn)->get_json();
    