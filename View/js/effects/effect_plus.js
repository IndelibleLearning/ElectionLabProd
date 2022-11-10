import * as common from "../game_common.js";
import * as api_common from "../api_common.js";
import * as game_map from "../game_map.js";

let playerEventCardId = null;

const EFFECT_PLUS_LABEL = "Choose a state to add +1";

function setupEffectPlus()
{
    setupEffectPlusEventListener();
}
setupEffectPlus();

function setupEffectPlusEventListener()
{
    document.addEventListener(common.EFFECT_PLUS, function(event)
    {
        playerEventCardId = event.eloParams.playerEventCardId;
        startEffectPlus();
    });
    
    // any resets that need to happen if they user presses start again
    document.addEventListener(common.START_EVENT, function(event)
    {
    });
    
}

function startEffectPlus()
{
    common.showLabel(EFFECT_PLUS_LABEL);
    common.showConfirmDefault(plusConfirmCallback);

    // get the unwon states
    let room_code = document.querySelector("#room_code").value;
    let game_id = document.querySelector("#game_id").value;
    let url = `${api_common.API_URL_BASE}/get_unwon_states.php?room_code=${room_code}&game_id=${game_id}`;
    common.get_request(url)
    .then(res=>{
        if (res.has_errors){
            alert(res.error_msg);
            return;
        }
        
        const unwonStates = res.data;
        
        createPlusStatesButtons(unwonStates);
    });
}

function createPlusStatesButtons(unwonStates)
{
    game_map.setStatesSelectable(unwonStates);
}

function plusConfirmCallback(e)
{
    if (game_map.chosenState)
    {
        addPlusForState();
    }
    else
    {
        alert("Please choose a state");
        common.enableConfirm();
    }
}

function addPlusForState()
{
    let player_name = document.querySelector("#player_name").value;
    let game_id = document.querySelector("#game_id").value;
    const data = 
    {
        "game_id": game_id,
        "player_name": player_name,
        "player_event_card_id": playerEventCardId,
        "state_abbrev": game_map.chosenState,
        "turn_num": common.get_api_turn_number()
    }
    const url = `${api_common.API_URL_BASE}/event_cards/plus.php`;
    api_common.post_request(url, data)
    .then(res=>{
        if (res.has_errors){
            console.log("Error with plus card " + res.error_msg);
            return;
        }
        
        finishPlus();
    });
}

function finishPlus()
{
    common.hideLabel();
    common.hideConfirm();
    game_map.makeStatesUnselectable();
    common.deploy_event(common.EVENT_CARD_WAIT_EVENT);
}
