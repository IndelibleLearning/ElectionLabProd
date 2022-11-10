<?php
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    
    require_once PROJECT_ROOT_PATH . "/Model/Database.php";

    class RollModel extends Database
    {
        
        public function create_roll(
            $turn_id, 
            $num_dice, 
            $roll_round, 
            $player_id)
        {
            $param_types = "iiii";
            return $this->insert("INSERT INTO rolls (
                turn_id, 
                num_dice, 
                roll_round, 
                player_id) 
                VALUES (?, ?, ?, ?)", 
                $param_types, 
                [$turn_id, $num_dice, $roll_round, $player_id]);
        }
        
        public function get_roll_for_round($turn_id, $roll_round, $player_id)
        {
            $param_types = "iii";
            return $this->select("SELECT * FROM rolls where turn_id = ? and roll_round = ? and player_id=?", $param_types, [$turn_id, $roll_round, $player_id]);
        }
        
        public function get_rolls_for_turn($turn_id)
        {
            $param_types = "i";
            return $this->select("SELECT * FROM rolls where turn_id = ?", $param_types, [$turn_id]);
        }
        
    }