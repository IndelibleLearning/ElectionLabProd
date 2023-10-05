import * as common from "./game_common.js";
import * as api_common from "./api_common.js";
import * as game_map from "./game_map.js";

const mapArea = document.querySelector("#map_area");
const waitingRollArea = document.querySelector("#waiting-roll-area");
const waitingRollAreaFirst = document.querySelector("#waiting-roll-area-first");

// How long in milliseconds we wait between checks
// for other player choosing state/turn
const WAITING_OTHER_PLAYER_MS = 1000;
const SINGLE_CLICK_CODE = 1;

const CREATE_TURN_LABEL = "Pick a swing state";

let wait_turn_interval = null;
let hasSeenFirstRollMessage = false;

function setupCreateTurn()
{
    setupDeploymentEventListener();
}
setupCreateTurn();

function setupDeploymentEventListener()
{
    document.addEventListener(common.CREATE_TURN_EVENT, function(event)
    {
        common.update_map();
        common.refresh_event_cards();
        checkWinner();
    });
    
    // any resets that need to happen if they user presses start again
    document.addEventListener(common.START_EVENT, function(event)
    {
        clearInterval(wait_turn_interval);
    });
}

function checkWinner()
{
    // write for ties
    
    // check if at end of game if number of turns matches total swing states
    let room_code = document.querySelector("#room_code").value;
    let game_id = document.querySelector("#game_id").value;
    let url = `${api_common.API_URL_BASE}/get_states_status.php?room_code=${room_code}&game_id=${game_id}`;
    common.get_request(url)
    .then(res=>{
        if (res.has_errors){
            console.log(res.error_msg);
            return;
        }
        if(common.get_api_turn_number() < Object.keys(res).length) 
        {
           // game is still going
           checkCreateTurn();
        }
        else
        {
           // no more turns left so go to end
           goToFinish();
        }
    });
}

function checkCreateTurn()
{
    // Should first check if we've already created this turn 
    let game_id = document.querySelector("#game_id").value;
    let url = `${api_common.API_URL_BASE}/get_turn.php?game_id=${game_id}&turn_num=${common.get_api_turn_number()}`;
    common.get_request(url)
    .then(res=>{
       if(res.has_errors && res.err_code == "no_turn_for_game") 
       {
           // turn hasn't been created yet
           startCreateTurn();
       }
       else
       {
           // turn already exists so go straight to rolling
           goToEventCards();
       }
    });
}

function startCreateTurn()
{
    //common.show_turn_number();
    common.refresh_event_cards();
    checkShowCreateTurn();
}

function goToEventCards()
{
    hideCreateTurn();
    hideWaitingArea();
    common.deploy_event(common.EVENT_CARD_EVENT);
}

function goToFinish()
{
    hideCreateTurn();
    hideWaitingArea();
    common.deploy_event(common.FINISH_EVENT);
}


function setup_state_buttons() 
{
    let room_code = document.querySelector("#room_code").value;
    let game_id = document.querySelector("#game_id").value;
    let url = `${api_common.API_URL_BASE}/get_states_status.php?room_code=${room_code}&game_id=${game_id}`;
        
    fetch(url, {
		method: "GET",
		headers: {
          'Content-Type': 'application/json'
        }
	})
	.then(response => response.json())
    .then(data => {
        common.showLabel(CREATE_TURN_LABEL);
        common.showConfirmDefault(setupTurnCallback);
        
        let selectableStates = [];
        
        for (const [id, state] of Object.entries(data)) {
            if (!state.winner_name)
            {
                selectableStates.push(state);
            }
        }
        
        game_map.setStatesSelectable(selectableStates);
    })
    .catch((error) => {
      console.error('Error:', error);
    });
}

function checkShowCreateTurn()
{
    let room_code = document.querySelector("#room_code").value;
    let game_id = document.querySelector("#game_id").value;
    // if i'm the winning player then hide button
    let url = `${api_common.API_URL_BASE}/get_score.php?room_code=${room_code}&game_id=${game_id}`;
    common.get_request(url)
    .then(data => {
      let least = null;
      let losing_player = null;
      
      // who is the winning player?
      for (const [id, score_data] of Object.entries(data["player_scores"])) {
          if (id && (least == null || least > score_data["EVs"]))
          {
              least = score_data["EVs"];
              losing_player = score_data["player_name"];
          }
      }
      let local_player = document.querySelector("#player_name").value;
      if (local_player !== losing_player)
      {
          showWaitingArea();
          return;
      }
      setup_state_buttons();
    })
    .catch((error) => {
      console.error('Error:', error);
    });
    
}

function setupTurnCallback(e)
{
    // only lets single click get past here
    /*if (e.detail !== SINGLE_CLICK_CODE)
    {
        return;
    }
     */
    if (!game_map.chosenState)
    {
        alert("You have to choose a state");
        common.enableConfirm();
        return;
    }
    let room_code = document.querySelector("#room_code").value;
    let game_id = document.querySelector("#game_id").value;
    
    let url = `${api_common.API_URL_BASE}/create_turn.php?room_code=${room_code}&game_id=${game_id}&state_abbrev=${game_map.chosenState}`;
    common.get_request(url)
    .then(data => {
      console.log('Success:', data);
      alert("Created turn!");
      goToEventCards();
    })
    .catch((error) => {
      console.error('Error:', error);
    });
}

function showWaitingArea()
{
    if (!hasSeenFirstRollMessage)
    {
        common.show(waitingRollAreaFirst)
        hasSeenFirstRollMessage = true;
    }
    else
    {
        common.show(waitingRollArea);
    }

    // kick of the holding pattern where we check every x seconds
    
    startCheckingForTurn();
}

function startCheckingForTurn()
{
    wait_turn_interval = setInterval(checkForTurn, WAITING_OTHER_PLAYER_MS);
}

function checkForTurn()
{
    // check if the latest turn is different?
    let game_id = document.querySelector("#game_id").value;
    let url = `${api_common.API_URL_BASE}/get_turn.php?game_id=${game_id}&turn_num=${common.get_api_turn_number()}`;
    common.get_request(url)
   .then(data => {
       console.log(data);
       if (!data.has_errors)
       {
           // Turn you want exists
           goToEventCards();
       }
    })
    .catch((error) => {
      console.log('Error loading latest turn', error);
    });
}

function hideCreateTurn()
{
    common.hideLabel();
    common.hideConfirm();
    game_map.makeStatesUnselectable();
    
}

function hideWaitingArea()
{
    clearInterval(wait_turn_interval);
    common.hide(waitingRollArea);
    common.hide(waitingRollAreaFirst);
}