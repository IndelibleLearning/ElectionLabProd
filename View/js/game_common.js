import * as api_common from "./api_common.js";
import {UPDATE_PLAYER_FRESHNESS_BASE} from "./user_common.js";

export const TURN_NUM_KEY = "indl_elo_turn_num";
export const PLAYER_FINISHED_GAME = "indl_elo_finished_game";

export const ROOM_CODE_INPUT_ID = "#room_code";
export const PLAYER_NAME_INPUT_ID = "#player_name";
export const GAME_ID_INPUT_ID = "#game_id";


export const START_EVENT = "start";
export const MAP_UPDATE_EVENT = "map_update";
export const DEPLOYMENT_EVENT = "deployment";
export const CREATE_TURN_EVENT = "create_turn";
export const EVENT_CARD_EVENT = "event_card";
export const EVENT_CARD_WAIT_EVENT = "event_card_wait";
export const REFRESH_HAND_EVENT = "refresh_hand";
export const ROLL_EVENT = "roll";
export const FINISH_EVENT = "finish";
export const POST_GAME_EVENT = "post_game";

export const EFFECT_PLUS = "plus";
export const EFFECT_SHIFT = "shift";

export const RED_DB_ID = 2;
export const BLUE_DB_ID = 3;

const ROOM_URL_BASE = `${api_common.VIEW_URL_BASE}/player_list_view.php`;

const gameLabel = document.querySelector("#game-label");
let gameConfirm = document.querySelector("#game-confirm");
const CONTINUE_BUTTON_CLASS = "continue-button";

let TURN_DISPLAY_ID = "#turn_display";
const ENTER_DEBOUNCE_TIME = 250;
let isDebouncing = false;

let local_state_color_map = {};
let local_player_score_map = {};

export const CHECK_GAME_RUNNING_BASE = `${api_common.API_URL_BASE}/check_game_still_running.php?room_code=`

export function deploy_event(eventName, eloParams)
{
    //const event = new Event(event_name);
    //elem.dispatchEvent(event);
    updateFreshness();

    const custEvent = new CustomEvent(eventName, {
      bubbles: true
    });
    custEvent.eloParams = eloParams;
    
    document.dispatchEvent(custEvent);
}

function updateFreshness()
{
    console.log("Updating freshness");
    let room_code = document.querySelector(ROOM_CODE_INPUT_ID).value;
    let player_name = document.querySelector(PLAYER_NAME_INPUT_ID).value;
    const url =  UPDATE_PLAYER_FRESHNESS_BASE + room_code + "&player_name=" + player_name;
    get_request(url)
    .then(res=>{
        if (res.has_errors){
            console.log(res.error_msg);
            return;
        }
        console.log("Freshness updated");
    });
}

export function show_turn_number()
{
    let turn_display = document.querySelector(TURN_DISPLAY_ID);
    let turn_num = window.localStorage.getItem(TURN_NUM_KEY);
    turn_display.classList.remove("hidden");
    turn_display.innerHTML = "Turn : " + turn_num;
}

export function set_turn_number(num)
{
    window.localStorage.setItem(TURN_NUM_KEY, num);
}

export function get_api_turn_number()
{
    return window.localStorage.getItem(TURN_NUM_KEY) - 1;
}

export function incrementTurn()
{
    let turn = parseInt(window.localStorage.getItem(TURN_NUM_KEY));
    set_turn_number(turn + 1);
}

export function get_request(url)
{
    return fetch(url, {
		method: "GET",
		headers: {
          'Content-Type': 'application/json'
        }
	})
   .then(response => response.json())
    .catch((error) => {
      console.log('Error: ' + error);
    });
}

export function add_won_state(state_abbrev, color)
{
    local_state_color_map[state_abbrev] = color;
}

export function get_state_color_map()
{
    return local_state_color_map;
}

export function update_map()
{
    deploy_event(MAP_UPDATE_EVENT);
}

export function set_player_score_map(player_name, score, color)
{
    local_player_score_map[color] = 
    {
        score: score,
        player_name: player_name
    }
}

export function add_to_player_score_map(player_name, score, color)
{
    if (!local_player_score_map[color])
    {
       set_player_score_map(player_name, score, color);
    }
    else
    {
        local_player_score_map[color].score += score;
    }
} 

export function get_player_score_map()
{
    return local_player_score_map;
}

export function getRoomUrl(room_code)
{
    return ROOM_URL_BASE + "?room_code=" + room_code
}

export function set_finished_game()
{
    window.localStorage.setItem(PLAYER_FINISHED_GAME, "true");
}

export function hide(element)
{
    element.classList.add("hidden");
}

export function show(element)
{
    element.classList.remove("hidden");
}


export function refresh_event_cards()
{
    deploy_event(REFRESH_HAND_EVENT);
}

export function checkAncestorsForClass(node, targetClass, stoppingPoint)
{
    if (node === stoppingPoint)
    {
        return null;
    }else if (node.getAttribute("class") && node.getAttribute("class").includes(targetClass))
    {
        return node;
    }
    else
    {
        return checkAncestorsForClass(node.parentNode, targetClass, stoppingPoint);
    }
}

export function showLabel(message)
{
    gameLabel.innerHTML = message;
    show(gameLabel);
}

export function hideLabel()
{
    gameLabel.innerHTML = "";
    hide(gameLabel);
}

export function showConfirm(buttonText, callback)
{
    show(gameConfirm);
    setContinueButton(gameConfirm);
    gameConfirm.removeAttribute("disabled");
    gameConfirm.innerHTML = buttonText;
    const confirmCallback = e => {
        if (!gameConfirm.getAttribute("disabled"))
        {
            gameConfirm.setAttribute("disabled", true);
            callback(e);
        }
    };
    gameConfirm.addEventListener("click", confirmCallback);
}

export function showConfirmDefault(callback)
{
    showConfirm("Confirm", callback);
}

export function hideConfirm()
{
    gameConfirm.removeAttribute("disabled");
    gameConfirm.innerHTML = "";
    hide(gameConfirm);
    
    const newConfirm = gameConfirm.cloneNode(true);
    gameConfirm = gameConfirm.replaceWith(newConfirm);
    gameConfirm = newConfirm;
}

export function enableConfirm()
{
    gameConfirm.removeAttribute("disabled");
}

function clearContinueButton()
{
    let lastContinue = document.querySelectorAll(`.${CONTINUE_BUTTON_CLASS}`);
    for (const contButton of lastContinue)
    {
        contButton.classList.remove(CONTINUE_BUTTON_CLASS);
    }

}

export function setContinueButton(button)
{
    clearContinueButton();
    button.classList.add(CONTINUE_BUTTON_CLASS);
}

function continueButtonListener()
{
    document.addEventListener("keypress", e => {
        if (event.key === "Enter" && !isDebouncing) {
            let lastContinue = document.querySelectorAll(`.${CONTINUE_BUTTON_CLASS}`);
            for (const contButton of lastContinue)
            {
               clearContinueButton();
               contButton.click();
            }
            isDebouncing = true;
            setTimeout(handleDebounce, ENTER_DEBOUNCE_TIME)
        }
    })
}
continueButtonListener();

function handleDebounce()
{
    isDebouncing = false;
}
