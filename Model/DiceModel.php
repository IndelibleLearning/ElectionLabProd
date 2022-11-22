<?php
    require dirname(dirname(__FILE__)) . "/inc/bootstrap.php";
    
    require_once PROJECT_ROOT_PATH . "/Model/Database.php";

    class DiceModel extends Database
    {
        
        public function create_dice(
            $die_face, 
            $roll_id, 
            $player_id)
        {
            $param_types = "iii";
            return $this->insert("INSERT INTO dice (
                die_face, 
                roll_id, 
                player_id) 
                VALUES (?, ?, ?)", 
                $param_types, 
                [$die_face, $roll_id, $player_id]);
        }
        
        public function get_dice_for_roll($roll_id)
        {
            $param_types = "i";
            return $this->select("SELECT * FROM dice where roll_id = ?", $param_types, [$roll_id]);
        }
        
        public function get_dice_faces_for_roll($roll_id)
        {
            $param_types = "i";
            return $this->select("SELECT die_face FROM dice where roll_id = ? order by die_face DESC", $param_types, [$roll_id]);
        }
    }