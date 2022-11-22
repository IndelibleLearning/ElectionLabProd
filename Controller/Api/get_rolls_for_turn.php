<?php
    require dirname(dirname(dirname(__FILE__))) . "/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/RoomModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/TurnModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/RollModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/DiceModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/StateModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/StateEVModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    require_once PROJECT_ROOT_PATH . "/Controller/Api/RestHelper.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $room_code = $_GET["room_code"];
    $game_id = $_GET["game_id"];
    $turn_num = $_GET["turn_num"];
    
    $room_model = new RoomModel();
    $player_model = new PlayerModel();
    $game_model = new GameModel();
    $turn_model = new TurnModel();
    $roll_model = new RollModel();
    $dice_model = new DiceModel();
    $state_model = new StateModel();
    $state_EV_model = new StateEVModel();
    
    // validate room
    $room_response = $room_model->validate_room_by_code($room_code);
    if ($room_response->get_has_errors())
    {
        echo $room_response->get_json_error();
        die();
    }
    
    // validate game
    $game_response = $game_model->validate_game_by_id($game_id);
    if ($game_response->get_has_errors())
    {
        echo $game_response->get_json_error();
        die();
    }
    
    // get turn
    $turn_results = $turn_model->validate_turn($game_id, $turn_num);
    if ($turn_results->get_has_errors())
    {
        echo $turn_results->get_json_error();
        die();
    }
    $turn = $turn_results->get_data();
    $turn_id = $turn["id"];
    $winner_id = $turn["winner"];
    $state_id = $turn["state_id"];
    
    // get state
    $state_repsonse = $state_model->validate_by_id($state_id);
    if ($state_repsonse->get_has_errors())
    {
        echo $state_repsonse->get_json_error();
        die();
    }
    $state = $state_repsonse->get_data();
    $state_abbrev = $state["state_abbrev"];
    $state_id = $state["id"];
    
    // get state EVs
    $state_EV_repsonse = $state_EV_model->validate_by_state_id($state_id);
    if ($state_EV_repsonse->get_has_errors())
    {
        echo $state_EV_repsonse->get_json_error();
        die();
    }
    $state_EV = $state_EV_repsonse->get_data();
    $EVs = $state_EV["electoral_votes"];
    
    // get players
    $player_response = $player_model->validate_players_in_game($game_id);
    if ($player_response->get_has_errors())
    {
        echo $player_response->get_json_error();
        die();
    }
    $players = $player_response->get_data();
    
    $player_color_map = [];
    $player_name_map = [];
    foreach ($players as $player)
    {
        $player_color_map[$player["id"]] = $player["color_id"];
        $player_name_map[$player["id"]] = $player["player_name"];
    }
    
    $rolls_map = [];
    
    $rolls_for_turn = $roll_model->get_rolls_for_turn($turn_id);
    foreach($rolls_for_turn as $roll)
    {
        $dice = $dice_model->get_dice_faces_for_roll($roll["id"]);
        $rolls_map[$roll["roll_round"]][$roll["player_id"]] = $dice;
    }
    
    $final_results = [];
    $final_results["winner_id"] = $winner_id;
    $final_results["winner_name"] = $player_name_map[$winner_id];
    $final_results["rolls"] = $rolls_map;
    $final_results["color_map"] = $player_color_map;
    $final_results["state_abbrev"] = $state_abbrev;
    $final_results["EVs"] = $EVs;
    

    echo ApiResponse::success_data($final_results)->get_json();

    