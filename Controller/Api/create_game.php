<?php
    session_start();
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/ElectionYearModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/RoomModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/GameModeModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $room_code = $_GET["room_code"];
    $election_year = $_GET["election_year"];
    $game_mode = $_GET["game_mode"];
    
    $election_year_model = new ElectionYearModel();
    $room_model = new RoomModel();
    $game_model = new GameModel();
    $game_mode_model = new GameModeModel();
    
    // check room
    $room_response = $room_model->validate_room_by_code($room_code);
    if ($room_response->get_has_errors())
    {
        echo $room_response->get_json_error();
        die();
    }
    $room = $room_response->get_data();
    
    $election_year_results = $election_year_model->validate_election_by_year($election_year);
    if ($election_year_results->get_has_errors())
    {
        echo $election_year_results->get_json_error();
        die();
    }
    $election_year = $election_year_results->get_data();
    
    // validate game ode
    $game_mode_results = $game_mode_model->validate_by_id($game_mode);
    if ($game_mode_results->get_has_errors())
    {
        $game_mode = 1;
    }
    else
    {
        $game_mode = $game_mode_results->get_data()["id"];
    }
    
    $insert_id = $game_model->create_game($room["id"], $election_year["id"], $game_mode);
    
    // get the id of the created game
    $result = [];
    $result["game_id"] = $insert_id;
    
    echo ApiResponse::success_data($result)->get_json();
    