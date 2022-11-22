<?php
    session_start();
    require dirname(dirname(dirname(__FILE__))) . "/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/HistogramAnswerModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $game_id = $_GET["game_id"];
    $player_name = $_GET["player_name"];
    $correct_answer = $_GET["correct_answer"];
    $player_answer = $_GET["player_answer"];

    $game_model = new GameModel();
    $player_model = new PlayerModel();
    $histogram_answer_model = new HistogramAnswerModel();

    // validate game
    $game_response = $game_model->validate_game_by_id($game_id);
    if ($game_response->get_has_errors())
    {
        echo $game_response->get_json_error();
        die();
    }

     // check player
    $player_response = $player_model->validate_player_in_game($game_id, $player_name);
    if ($player_response->get_has_errors())
    {
        echo $player_response->get_json_error();
        die();
    }
    $player_id = $player_response->get_data()["id"];

    // TODO: ADD SANITATION FOR THE ANSWERS
    if ($correct_answer == null || strlen($correct_answer > 1) ||
        $player_answer == null || strlen($player_answer > 1))
    {
        echo ApiResponse::error("bad_answer_input", "Bad answers inputed");
        die();
    }

    // submit player answer
    $histogram_answer_model->create_histogram_answer($player_id, $game_id, $correct_answer, $player_answer);
    
    echo ApiResponse::success()->get_json();
    