<?php
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    
    require_once PROJECT_ROOT_PATH . "/Model/Database.php";

    class DeploymentModel extends Database
    {
        
        public function create_deployment($player_id, $game_id, $state_id, $num_pieces)
        {
            $param_types = "iiii";
            return $this->insert("INSERT INTO deployments (player_id, game_id, state_id, num_pieces) VALUES (?, ?, ?, ?)", $param_types, [$player_id, $game_id, $state_id, $num_pieces]);
        }
        
        public function get_deployment($player_id, $game_id, $state_id)
        {
            $param_types = "iii";
            return $this->select("SELECT * FROM deployments where player_id = ? and game_id = ? and state_id = ?", $param_types, [$player_id, $game_id, $state_id]);
        }
        
        public function get_deployments($player_id, $game_id)
        {
            $param_types = "ii";
            return $this->select("SELECT * FROM deployments where player_id = ? and game_id = ?", $param_types, [$player_id, $game_id]);
        }
    }