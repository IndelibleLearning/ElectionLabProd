import * as common from "../game_common.js";
import * as api_common from "../api_common.js";
import * as game_map from "../game_map.js";

const SHIFT_REMOVE_PROMPT = "Select a state to -1";
const SHIFT_ADD_PROMPT = "Select a state to +1";

let chosenShiftRemoveState = null;
let chosenShiftAddState = null;
let playerEventCardId = null;
let unwonStates = null;

function setupEffectShift()
{
    setupEffectShiftEventListener();
}
setupEffectShift();

function setupEffectShiftEventListener()
{
    document.addEventListener(common.EFFECT_SHIFT, function(event)
    {
        playerEventCardId = event.eloParams.playerEventCardId;
        startEffectShift();
    });
    
    // any resets that need to happen if they user presses start again
    document.addEventListener(common.START_EVENT, function(event)
    {
    });
    
}

function resetShift()
{
    chosenShiftRemoveState = null;
    chosenShiftAddState = null;
    unwonStates = null;
    common.showLabel(SHIFT_REMOVE_PROMPT);
    common.showConfirmDefault(shiftClickCallback);
}



function startEffectShift()
{
    resetShift();
    
    // get the unwon states
    let room_code = document.querySelector("#room_code").value;
    let game_id = document.querySelector("#game_id").value;
    let player_name = document.querySelector("#player_name").value;
    let url = `${api_common.API_URL_BASE}/get_unwon_states_with_pieces.php?room_code=${room_code}&game_id=${game_id}&player_name=${player_name}`;
    common.get_request(url)
    .then(res=>{
        if (res.has_errors){
            alert(res.error_msg);
            return;
        }
        
        unwonStates = res.data;
        
        createRemoveShiftStatesButtons(unwonStates);
    });
}

function createRemoveShiftStatesButtons(unwonStates)
{
    let unwonStatesWithPips = unwonStates.filter(state => state["num_pieces"] > 0);
    
    game_map.setStatesSelectable(unwonStatesWithPips);
}

function shiftClickCallback(e)
{
    if (!game_map.chosenState)
    {
        alert("Please select a state");
        common.enableConfirm();
        return;
    }
    
    if (!chosenShiftAddState && chosenShiftRemoveState)
    {
        chosenShiftAddState = game_map.chosenState;
        applyShiftCardForStates(chosenShiftRemoveState, chosenShiftAddState);
    }
    else
    {
        common.showLabel(SHIFT_ADD_PROMPT);
        chosenShiftRemoveState = game_map.chosenState;
        removeChosenShiftButton();
        common.enableConfirm();
    }
}

function removeChosenShiftButton()
{
    game_map.makeStatesUnselectable();
    game_map.setStatesSelectable(unwonStates);
}

function applyShiftCardForStates(removeState, addState)
{
    let player_name = document.querySelector("#player_name").value;
    let game_id = document.querySelector("#game_id").value;
    const data = 
    {
        "game_id": game_id,
        "player_name": player_name,
        "player_event_card_id": playerEventCardId,
        "add_state_abbrev": chosenShiftAddState,
        "remove_state_abbrev": chosenShiftRemoveState,
        "turn_num": common.get_api_turn_number()
    }
    const url = `${api_common.API_URL_BASE}/event_cards/shift.php`;
    api_common.post_request(url, data)
    .then(res=>{
        if (res.has_errors){
            console.log("Error with shift card " + res.error_msg);
            return;
        }
        
        resetShift();
        finishShift();
    });
}

function finishShift()
{
    common.hideLabel();
    common.hideConfirm();
    game_map.makeStatesUnselectable();
    common.deploy_event(common.EVENT_CARD_WAIT_EVENT);
}
