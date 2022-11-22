<?php
    session_start();
    require dirname(dirname(dirname(__FILE__))) . "/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/RoomModel.php";
    require_once PROJECT_ROOT_PATH . "/Controller/Api/RestHelper.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    header('Content-Type: application/json; charset=utf-8');
    
    
    $room_code = $_GET["room_code"];
    $election_year = $_GET["election_year"];
    $game_mode = $_GET["game_mode"];
    
    $room_model = new RoomModel();
    $player_model = new PlayerModel();

    // check room
    $room_response = $room_model->validate_room_by_code($room_code);
    if ($room_response->get_has_errors())
    {
        echo $room_response->get_json_error();
        die();
    }
    $room = $room_response->get_data();
    
    // get the players
    $players = $player_model->get_players_by_room($room["id"]);
    
    // validate there are enough players
    if (!$players || count($players) < 2) 
    {
        $err_code = "not_enough_players";
        $err_msg = "Not enough players in the room to create a match";
        echo ApiResponse::error($err_code, $err_msg)->get_json();
        die();
    }
    
    
    // get the unmatched players
    $unmatched_players = [];
    foreach($players as $player)
    {
        if (!$player["game_id"])
        {
            array_push($unmatched_players, $player);
        }
    }
    
    // validate there are enough players
    if (!$unmatched_players || count($unmatched_players) < 2) 
    {
        $err_code = "not_enough_unmatched_players";
        $err_msg = "Not enough unmatched players in the room to create a match";
        echo ApiResponse::error($err_code, $err_msg)->get_json();
        die();
    }
    
    // randomize players
    shuffle($unmatched_players);
    
    // starting matching players
    for($i=0; $i < count($unmatched_players) - 1; $i+=2)
    {
        $red_player = $unmatched_players[$i];
        $blue_player = $unmatched_players[$i + 1];
        
        // try and create the game
        $create_game_url = CONTROLLER_API_PATH . "create_game.php?room_code=$room_code&election_year=$election_year&game_mode=$game_mode";
        $created_game_id = RestHelper::rest_call($create_game_url)["data"]["game_id"];
        
        join_game($red_player, "red", $created_game_id, $room_code);
        join_game($blue_player, "blue", $created_game_id, $room_code);
    }
    
    echo ApiResponse::success()->get_json();
    
    

    function join_game($player, $color_name, $game_id, $room_code)
    {
        /// set player color
        $player_id = $player["id"];
        $color_url = CONTROLLER_API_PATH . "set_player_color.php?player_id=$player_id&color_name=$color_name";
        RestHelper::rest_call($color_url);

        // player join
        $player_name = urlencode($player["player_name"]);
        $join_url = CONTROLLER_API_PATH . "join_game.php?room_code=$room_code&player_name=$player_name&game_id=$game_id";
        RestHelper::rest_call($join_url);
    }