import * as common from "./game_common.js";
import * as api_common from "./api_common.js";

const eventCardArea = document.querySelector("#event_card_area");
const eventCardHand = document.querySelector("#event_card_hand");
const eventWaitingArea = document.querySelector("#waiting_event_card_area");
const goToRollButton = document.querySelector("#go_to_roll");

function setupEventCard()
{
    setupEventCardEventListener();
}
setupEventCard();

function setupEventCardEventListener()
{
    document.addEventListener(common.REFRESH_HAND_EVENT, function(event)
    {
        refreshHand();
    });
    
    // any resets that need to happen if they user presses start again
    document.addEventListener(common.START_EVENT, function(event)
    {
        eventCardHand.innerHTML = "";
    });
    
    document.addEventListener(common.FINISH_EVENT, function(event)
    {
        eventCardHand.innerHTML = "";
    });
}

function refreshHand()
{
    // grab the current player's hand
    let player_name = document.querySelector("#player_name").value;
    let game_id = document.querySelector("#game_id").value;
    let url = `${api_common.API_URL_BASE}/get_player_cards_for_turn.php?player_name=${player_name}&game_id=${game_id}&turn_num=${common.get_api_turn_number()}`;
    common.get_request(url)
    .then(res=>{
        if (res.has_errors){
            console.log("refresh hand:" + res.error_msg);
            return;
        }
        eventCardHand.innerHTML = "";
        if(res.data.cards && res.data.cards.length > 0)
        {
            showHand(res.data.cards);
        }
        

    });
}

function showHand(cards)
{
    eventCardHand.innerHTML = "";
    for(let i=0; i < cards.length; i++)
    {
        const card = cards[i];
        let cardDOM = createCard(card);
        eventCardHand.appendChild(cardDOM);
    }
}

function createCard(cardInfo)
{
    const cardDiv = document.createElement("div");
    cardDiv.classList.add("card_container");
    cardDiv.setAttribute("data_player_event_card_id", cardInfo["player_event_card_data"]["id"]);
    cardDiv.setAttribute("data_event_key", cardInfo["event_card_data"]["event_key"]);
    
    const title = document.createElement("h3");
    title.classList.add("card_title");
    title.innerHTML = cardInfo["event_card_data"]["event_name"];
    cardDiv.appendChild(title);
    
    const description = document.createElement("div");
    description.classList.add("card_desc");
    description.innerHTML = cardInfo["event_card_data"]["event_description"];
    cardDiv.appendChild(description);
    
    return cardDiv;
}

