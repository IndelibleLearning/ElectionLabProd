import * as common from "./game_common.js";
import * as api_common from "./api_common.js";

const eventWaitingArea = document.querySelector("#waiting_event_card_area");

const CHECK_OPPONENT_TIME = 1000;

let selectedCard = null;

function setupEventCard()
{
    setupEventCarWaitdEventListener();
}
setupEventCard();

function setupEventCarWaitdEventListener()
{
    document.addEventListener(common.EVENT_CARD_WAIT_EVENT, function(event)
    {
        goToEventWaitingArea();
    });
    
    // any resets that need to happen if they user presses start again
    document.addEventListener(common.START_EVENT, function(event)
    {
        common.hide(eventWaitingArea);
    });
    
}

function goToEventWaitingArea()
{
    common.show(eventWaitingArea);
    checkOpponentPlayed();
}

function checkOpponentPlayed()
{
    // grab the current player's hand
    let player_name = document.querySelector("#player_name").value;
    let game_id = document.querySelector("#game_id").value;
    let url = `${api_common.API_URL_BASE}/check_opponent_played_card.php?game_id=${game_id}&turn_num=${common.get_api_turn_number()}&player_name=${player_name}`;
    common.get_request(url)
    .then(res=>{
        if (res.has_errors){
            console.log("event cards:" + res.error_msg);
            setTimeout(checkOpponentPlayed, CHECK_OPPONENT_TIME);
            return;
        }
        
        const opponentPlayed = res.data;
        
        if(opponentPlayed)
        {
            goToRoll();
        }
        else
        {
            setTimeout(checkOpponentPlayed, CHECK_OPPONENT_TIME)
        }
    });
}

function goToRoll()
{
    common.hide(eventWaitingArea);
    common.deploy_event(common.ROLL_EVENT);
}

