<?php
    require dirname(dirname(dirname(__FILE__))) . "/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/RoomModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/TurnModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/StateEVModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/StateModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    require_once PROJECT_ROOT_PATH . "/Controller/Api/RestHelper.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $room_code = $_GET["room_code"];
    $game_id = $_GET["game_id"];

    $game_model = new GameModel();
    
    $RED_ID = 2;
    $BLUE_ID = 3;
 
    // get the state status
    $url = CONTROLLER_API_PATH . "get_score.php?room_code=$room_code&game_id=$game_id";
    $player_scores = RestHelper::rest_call($url)["player_scores"];
    
   
    $player_color_score_map = [];
    foreach($player_scores as $player_id => $score_data)
    {
        $entry = $score_data;
        $entry["player_id"] = $player_id;
        $player_color_score_map[$score_data["color_id"]] = $entry;
    }
    
     // check for tie first (eventually let's clean this up and combine with lower check)
    if($player_color_score_map[$RED_ID]["EVs"] == $player_color_score_map[$BLUE_ID]["EVs"])
    {
        $results = [];
        $results["winner_id"] = null;
        $results["winner_name"] = null;
        $results["score"] = $player_color_score_map[$RED_ID]["EVs"];
        echo ApiResponse::success_data($results)->get_json();
        die();
    }
    
    
    $winner_id = "";
    // finds winner based on who has the highest total
    $max = null;
    $winner_id = "";
    foreach($player_scores as $player_id => $score_data)
    {
        if ($max == null || $max < $score_data["EVs"])
        {
            $winner_id = $player_id;
            $max = $score_data["EVs"];
        }
    }
    
    // update the database to see the winner
    $game_model->set_winner($game_id, $winner_id);
    
    $results = [];
    $results["winner_id"] = $winner_id;
    $results["winner_name"] = $player_scores[$winner_id]["player_name"];
    $results["score"] = $max;
    echo ApiResponse::success_data($results)->get_json();
    
    
    