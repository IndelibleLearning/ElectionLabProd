<?php
    require dirname(dirname(dirname(__FILE__))) . "/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
    require_once PROJECT_ROOT_PATH . "/Controller/Api/RestHelper.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $room_code = $_GET["room_code"];
    $game_id = $_GET["game_id"];
    $player_name = $_GET["player_name"];
    
    $player_model = new PlayerModel();
    
    // check player
    $player_response = $player_model->validate_player_in_game($game_id, $player_name);
    if ($player_response->get_has_errors())
    {
        echo $player_response->get_json_error();
        die();
    }
    
    // get all states info for game
    $unwon_states_url = CONTROLLER_API_PATH . "get_unwon_states.php?room_code=$room_code&game_id=$game_id";
    $unwon_states_response = RestHelper::rest_call($unwon_states_url);
    
    if ($unwon_states_response["has_errors"])
    {
        echo json_encode($unwon_states_response);
        die();
    }
    $unwon_states = $unwon_states_response["data"];
    
    // get deployments
    $deployments_url = CONTROLLER_API_PATH . "get_deployments.php?room_code=$room_code&game_id=$game_id";
    $deployments_response = RestHelper::rest_call($deployments_url);
    
    if ($deployments_response["has_errors"])
    {
        echo json_encode($deployments_response);
        die();
    }
    $deployments = $deployments_response["data"];
    $player_deployments = $deployments[$player_name]["deployments"];
    
    // create a map of the num pieces to state abbrev
    $pieces_map = [];
    foreach($player_deployments as $deployment)
    {
        $pieces_map[$deployment["state_abbrev"]] = $deployment["num_pieces"];
    }
    
    // add the pieces field to the results of unwon states
    foreach($unwon_states as $key => $state)
    {
        $unwon_states[$key]["num_pieces"] = $pieces_map[$state["state_abbrev"]];
    }
    
    
    echo ApiResponse::success_data($unwon_states)->get_json();
    