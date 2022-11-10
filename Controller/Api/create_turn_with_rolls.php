<?php
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/RoomModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/DeploymentModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    require_once PROJECT_ROOT_PATH . "/Controller/Api/RestHelper.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $room_code = $_GET["room_code"];
    $game_id = $_GET["game_id"];
    $state_abbrev = $_GET["state_abbrev"];
    
    // create a turn
    $create_turn_url = CONTROLLER_API_PATH . "create_turn.php?room_code=$room_code&game_id=$game_id&state_abbrev=$state_abbrev";
    $turn_created_response = RestHelper::rest_call($create_turn_url);
    
    if ($turn_created_response["has_errors"])
    {
        echo json_encode($turn_created_response);
        die();
    }
    $turn_num = $turn_created_response["data"]["turn_num"];
    
    // roll for the turn
    $roll_url = CONTROLLER_API_PATH . "roll_for_turn.php?room_code=$room_code&game_id=$game_id&turn_num=$turn_num";
    $roll_response = RestHelper::rest_call($roll_url);
    
    if($roll_response["has_errors"])
    {
        echo json_encode($roll_response);
        die();
    }
    
    echo ApiResponse::success()->get_json();
    