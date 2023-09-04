import * as api_common from "./api_common.js";

const LOGIN_TOKEN_KEY = "indl_elo_login_token";
const USER_NAME_KEY = "indl_elo_user_name";

// API URLs
export const LOGIN_URL = `${api_common.API_URL_BASE}/login.php`;
export const SIGNUP_URL = `${api_common.API_URL_BASE}/signup.php`;
export const GET_ROOMS_URL = `${api_common.API_URL_BASE}/get_rooms_by_user_name.php`
export const CREATE_ROOM_URL = `${api_common.API_URL_BASE}/create_room.php`;
export const GET_PLAYERS_IN_ROOM_BASE = `${api_common.API_URL_BASE}/get_players_in_room.php?room_code=`;
export const JOIN_ROOM_BASE = `${api_common.API_URL_BASE}/join_room.php`

const MATCH_PLAYERS_BASE = `${api_common.API_URL_BASE}/match_players.php`

export const KILL_GAME_BASE = `${api_common.API_URL_BASE}/kill_game.php?game_id=`

export const KICK_PLAYER_BASE = `${api_common.API_URL_BASE}/kick_player.php?room_code=`

export const UPDATE_PLAYER_FRESHNESS_BASE = `${api_common.API_URL_BASE}/update_player_freshness.php?room_code=`

// Views URL
export const DASHBOARD_URL = `${api_common.VIEW_URL_BASE}/user_dashboard.php`
const ROOM_URL_BASE = `${api_common.VIEW_URL_BASE}/player_list_view.php`;
const LOGIN_VIEW_URL = `${api_common.VIEW_URL_BASE}/login_view.php`;

export function setLoginToken(token)
{
    window.localStorage.setItem(LOGIN_TOKEN_KEY, token);
}

export function getLoginToken()
{
    return window.localStorage.getItem(LOGIN_TOKEN_KEY);
}

export function setUserName(user_name)
{
    window.localStorage.setItem(USER_NAME_KEY, user_name);
}

export function getUserName()
{
    return window.localStorage.getItem(USER_NAME_KEY);
}

export function getRoomUrl(room_code)
{
    return ROOM_URL_BASE + "?room_code=" + room_code
}

export function goToLogin()
{
    window.location.href = LOGIN_VIEW_URL;
}

export function matchPlayersUrl($room_code, game_mode)
{
    return MATCH_PLAYERS_BASE + "?room_code=" + $room_code + "&election_year=2024&game_mode=" + game_mode;
}
