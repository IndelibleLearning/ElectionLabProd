<?php
    require dirname(dirname(__FILE__)) . "/inc/bootstrap.php";
    
    require_once PROJECT_ROOT_PATH . "/Model/Database.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";

    class HistogramAnswerModel extends Database
    {
        public function create_histogram_answer(
            $player_id, 
            $game_id, 
            $correct_answer,
            $player_answer)
        {
            $param_types = "iiss";
            return $this->insert("INSERT INTO histogram_answers (
                player_id, 
                game_id, 
                correct_answer,
                player_answer) 
                VALUES (?, ?, ?, ?)", 
                $param_types, 
                [$player_id, $game_id, $correct_answer, $player_answer]);
        }
    }