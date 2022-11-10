<?php
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    require_once PROJECT_ROOT_PATH . "/Controller/Api/RestHelper.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $room_code = $_GET["room_code"];
    $game_id = $_GET["game_id"];
    
    // get all states info for game
    $get_state_status_url = CONTROLLER_API_PATH . "get_states_status.php?room_code=$room_code&game_id=$game_id";
    $status_status_response = RestHelper::rest_call($get_state_status_url);
    
    if ($status_status_response["has_errors"])
    {
        echo json_encode($status_status_response);
        die();
    }
    $states_status = $status_status_response;
    
    // filter out already won states
    $unwon_states = [];
    foreach($states_status as $state)
    {
        if(!$state["winner_name"])
        {
            array_push($unwon_states, $state);
        }
    }
    
    echo ApiResponse::success_data($unwon_states)->get_json();
    