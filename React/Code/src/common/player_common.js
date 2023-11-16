import * as api_common from "./api_common.js";

const PLAYER_NAME_KEY = "indl_elo_player_name";
const PLAYER_ROOM_KEY = "indl_elo_room_code";
const PLAYER_GAME_ID_KEY = "indl_elo_game_id";
const PLAYER_FINISHED_GAME = "indl_elo_finished_game";

export const GAME_URL = `${api_common.VIEW_URL_BASE}/game_view.php`;

const CHECK_PLAYER_HAS_GAME_BASE = `${api_common.API_URL_BASE}/check_player_has_game.php`;
const PLAYER_LEAVE_GAME_BASE = `${api_common.API_URL_BASE}/leave_game.php`

export function setPlayerName(player_name)
{
    window.localStorage.setItem(PLAYER_NAME_KEY, player_name);
}

export function getPlayerName()
{
    return window.localStorage.getItem(PLAYER_NAME_KEY);
}

export function setPlayerRoom(room_code)
{
    window.localStorage.setItem(PLAYER_ROOM_KEY, room_code);

}

export function getPlayerRoom()
{
    return window.localStorage.getItem(PLAYER_ROOM_KEY);
}

export function setPlayerGameId(game_id)
{
    window.localStorage.setItem(PLAYER_GAME_ID_KEY, game_id);
}

export function getPlayerGameID()
{
    return window.localStorage.getItem(PLAYER_GAME_ID_KEY);
}

export function resetPlayerFinishedGame()
{
    return window.localStorage.removeItem(PLAYER_FINISHED_GAME);
}

export function getPlayerFinishedGame()
{
    return window.localStorage.getItem(PLAYER_FINISHED_GAME);
}

export function checkPlayerHasGameURL(room_code, player_name)
{
    return CHECK_PLAYER_HAS_GAME_BASE + `?room_code=${room_code}&player_name=${player_name}`;
}

export function getLeaveGameURL(room_code, player_name)
{
    return PLAYER_LEAVE_GAME_BASE + `?room_code=${room_code}&player_name=${player_name}`;
}