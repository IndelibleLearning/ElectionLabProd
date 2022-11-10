import * as common from "./game_common.js";
import * as api_common from "./api_common.js";

const eventCardArea = document.querySelector("#event_card_area");
const eventCardHand = document.querySelector("#event_card_hand");
const eventCardSelection = document.querySelector("#event_card_selection");
const eventCardPlaceholder = document.querySelector("#event_card_placeholder");
const eventWaitingArea = document.querySelector("#waiting_event_card_area");
const noCardButton = document.querySelector("#no_card");
const goToCardEffectButton = document.querySelector("#go_to_card_effect");
const eventCardTitle = document.querySelector("#event-card-state");

const CHECK_OPPONENT_TIME = 1000;

let selectedCard = null;

function setupEventCard()
{
    setupEventCardEventListener();
    setupNoCardButton();
    setupGoToCardEffectButton();
}
setupEventCard();

function setupEventCardEventListener()
{
    document.addEventListener(common.EVENT_CARD_EVENT, function(event)
    {
        resetEventCard();
        checkAllowedToPlay();
    });
    
    // any resets that need to happen if they user presses start again
    document.addEventListener(common.START_EVENT, function(event)
    {
        common.hide(eventCardArea);
    });
}

function resetEventCard()
{
    common.refresh_event_cards();
    noCardButton.removeAttribute("disabled");
    common.show(noCardButton);
    goToCardEffectButton.removeAttribute("disabled");
    common.hide(goToCardEffectButton);
    eventCardSelection.innerHTML = "";
}

function checkAllowedToPlay()
{
    // grab the current player's hand
    let player_name = document.querySelector("#player_name").value;
    let game_id = document.querySelector("#game_id").value;
    let url = `${api_common.API_URL_BASE}/check_played_card.php?game_id=${game_id}&turn_num=${common.get_api_turn_number()}&player_name=${player_name}`;
    common.get_request(url)
    .then(res=>{
        if (res.has_errors){
            console.log("event cards:" + res.error_msg);
            goToEventWaitingArea();
            return;
        }
        
        const hasPlayedCard = res.data;
        
        if(!hasPlayedCard)
        {
            checkHasCards();
        }
        else
        {
            goToEventWaitingArea();
        }
    });
}

function checkHasCards()
{
    // grab the current player's hand
    let player_name = document.querySelector("#player_name").value;
    let game_id = document.querySelector("#game_id").value;
    let url = `${api_common.API_URL_BASE}/get_player_cards_for_turn.php?player_name=${player_name}&game_id=${game_id}&turn_num=${common.get_api_turn_number()}`;
    common.get_request(url)
    .then(res=>{
        if (res.has_errors){
            console.log("event cards:" + res.error_msg);
            goToEventWaitingArea();
            return;
        }
        
        // player does not have a hand
        if(!res.data || res.data.cards.length <= 0)
        {
            // create an empty turn card entry
            chooseCard(null);
            goToEventWaitingArea();
            return;
        }
        
        showEventCardUI();
    });
}



function showEventCardUI()
{
    common.refresh_event_cards();
    // grab the current player's hand
    let game_id = document.querySelector("#game_id").value;
    let url = `${api_common.API_URL_BASE}/get_turn.php?game_id=${game_id}&turn_num=${common.get_api_turn_number()}`;
    common.get_request(url)
    .then(res=>{
        if (res.has_errors){
            console.log("event cards couldn't get turn:" + res.error_msg);
            goToEventWaitingArea();
            return;
        }
        const stateAbbrev = res.data["state_abbrev"];
        const stateEVs = res.data["EVs"];
        common.show(eventCardArea);
        common.show(eventCardHand);
        setupCardSelection();
        eventCardTitle.innerHTML = `${stateAbbrev}(${stateEVs}) was picked`;

    });

}

function setupCardSelection()
{
    eventCardPlaceholder.classList.remove("hidden");
    eventCardHand.classList.add("selectable");
    attachHandClickListeners();
}

function stopCardSelection()
{
    eventCardPlaceholder.classList.add("hidden");
    eventCardHand.classList.remove("selectable");
    removeHandClickListeners();
}

function attachHandClickListeners()
{
    selectedCard = null;
    eventCardHand.addEventListener("click", handClickListener);
}

function handClickListener(event)
{
    let clickedCard = getClickedCard(event.target, event.currentTarget);
        
    if (clickedCard !== null)
    {
        eventCardHand.classList.remove("selectable");
        eventCardSelection.innerHTML = "";
        eventCardSelection.appendChild(clickedCard.cloneNode(true));
        common.hide(eventCardPlaceholder);
        common.hide(noCardButton);
        common.show(goToCardEffectButton);
        
        // handle selection
        if (selectedCard && selectedCard !== clickedCard)
        {
            selectedCard.classList.remove("selected");
        }
        selectedCard = clickedCard;
        clickedCard.classList.add("selected");
    }
}

function removeHandClickListeners()
{
    eventCardHand.removeEventListener("click", handClickListener);
}

function getClickedCard(node, stoppingPoint)
{
    if (node === stoppingPoint)
    {
        return null;
    }else if (node.className.includes("card_container"))
    {
        return node;
    }
    else
    {
        return getClickedCard(node.parentNode);
    }
}

function getHand()
{
    return document.querySelector(".card_container");
}

function chooseCard(card_id)
{
    let player_name = document.querySelector("#player_name").value;
    let game_id = document.querySelector("#game_id").value;
    let url = `${api_common.API_URL_BASE}/play_event_card.php?game_id=${game_id}&turn_num=${common.get_api_turn_number()}&player_name=${player_name}&event_card_id=${card_id}`;
    common.get_request(url)
    .then(res=>{
        if (res.has_errors){
            console.log("event cards:" + res.error_msg);
        }
    });
}

function goToEventWaitingArea()
{
    stopCardSelection();
    hideEventSection();
    common.deploy_event(common.EVENT_CARD_WAIT_EVENT);
}

function setupNoCardButton()
{
    noCardButton.addEventListener("click", function(e)
    {
        noCardButton.setAttribute("disabled", "true");
        chooseCard(null);
        goToEventWaitingArea();
    })
}

function hideEventSection()
{
    common.hide(eventCardArea);
}

function setupGoToCardEffectButton()
{
    goToCardEffectButton.addEventListener("click", function(e)
    {
        goToCardEffectButton.setAttribute("disabled", "true");
        stopCardSelection();
        const cardEventKey = selectedCard.getAttribute("data_event_key");
        const playerEventCardId = selectedCard.getAttribute("data_player_event_card_id");
        const params = {
            "playerEventCardId": playerEventCardId
        }
        
        hideEventSection();
        switch(cardEventKey)
        {
            case "plus":
                common.deploy_event(common.EFFECT_PLUS, params);
                break;
            case "shift":
                // special shift validation - we need a better way to do this
                validateShift(params);
                break;
            default:
                goToEventWaitingArea();
                break;
        }
    });
}

function validateShift(params)
{
    let room_code = document.querySelector("#room_code").value;
    let game_id = document.querySelector("#game_id").value;
    let player_name = document.querySelector("#player_name").value;
    let url = `${api_common.API_URL_BASE}/get_unwon_states_with_pieces.php?room_code=${room_code}&game_id=${game_id}&player_name=${player_name}`;
    common.get_request(url)
    .then(res=>{
        if (res.has_errors){
            console.log(res.error_msg);
            return;
        }
        
        const unwonStates = res.data;
        
        // not enough states to do a shift
        if (!unwonStates || unwonStates.length < 2)
        {
            alert("Not enough states to shift");
            common.deploy_event(common.EVENT_CARD_EVENT, params);
            return;
        }
        
        // check if there are no pips in the remaining states
        let totalPips = 0;
        for(let i=0; i < unwonStates.length; i++)
        {
            let state = unwonStates[i];
            totalPips += state["num_pieces"];
        }
        if (totalPips <= 0)
        {
            alert("Not enough resources left to perform a shift");
            common.deploy_event(common.EVENT_CARD_EVENT, params);
            return;
        }
        
        common.deploy_event(common.EFFECT_SHIFT, params);
       
    });
}

