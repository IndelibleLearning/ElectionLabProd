<?php
    require dirname(dirname(__FILE__)) . "/inc/bootstrap.php";

    require_once PROJECT_ROOT_PATH . "/Model/Database.php";
    require_once PROJECT_ROOT_PATH . "/Model/DateTimeUtility.php";
    require_once PROJECT_ROOT_PATH . "/Model/TurnModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/StateEVModel.php";

    class GameModel extends Database
    {

        public function create_game($room_id, $election_year_id, $game_mode)
        {
            $expire_date = DateTimeUtility::datetime_from_now("+1 hour");
            $creation_date = date("Y-m-d H:i:s");

            $param_types = "iiiss";
            return $this->insert("INSERT INTO games (room_id, election_year_id, game_mode, expires_at, created_at) VALUES (?, ?, ?, ?, ?)", $param_types, [$room_id, $election_year_id, $game_mode, $expire_date, $creation_date]);
        }

        public function get_game_by_id($game_id)
        {
            $param_types = "i";
            return $this->select("SELECT * FROM games where id = ?", $param_types, [$game_id]);
        }

        public function set_total_turns($game_id, $total_turns)
        {
            $param_types = "ii";
            return $this->update("UPDATE games SET total_turns=? where id=?", $param_types, [$total_turns, $game_id]);
        }

        public function validate_game_by_id($game_id)
        {
            $error_code = "no_game_found";
            $error_msg = "Could not find game associated with id $game_id";
            $results = $this->get_game_by_id($game_id);
            return ApiResponse::validate_single_entry_not_empty($results, $error_code, $error_msg);
        }

        public function set_winner($game_id, $winner_id)
        {
            $end_date = date("Y-m-d H:i:s");
            $param_types = "isi";
            return $this->update("UPDATE games SET winner_id=?, ended_at=? where id=?", $param_types, [$winner_id, $end_date, $game_id]);
        }
    }