import * as common from "./game_common.js";
import * as api_common from "./api_common.js";

const mapArea = document.querySelector("#map-area");
const mainContainer = document.querySelector("#main");

export const RED_DB_ID = 2;
export const BLUE_DB_ID = 3;

const RED_IMG_CLASS = "won-red";
const BLUE_IMG_CLASS = "won-blue";
const WHITE_IMG_CLASS = "st0";

const RED_PLAYER_NAME = "#red_player_name";
const RED_BAR = "#red_bar";
const RED_SCORE = "#red_score";

const BLUE_PLAYER_NAME = "#blue_player_name";
const BLUE_BAR = "#blue_bar";
const BLUE_SCORE = "#blue_score";

const BAR_FILL_SUFFIX = " .bar-fill";
const PIPS_CONTAINER = "deployments";
const PIPS_CONTAINER_SUFFIX = "_pips";
const PIPS_CLASS = "pip_b";

const PIPS_BLUE_CLASS = "blue_pips";
const PIPS_RED_CLASS = "red_pips"

const MAIN_RED_CLASS = "red";
const MAIN_BLUE_CLASS = "blue";

let max_votes_map = {}
export let chosenState = null;

function setupMap()
{
    setupMapEventListener();
    attachStateButtonListeners();
}
setupMap();

function setupMapEventListener()
{
    document.addEventListener(common.START_EVENT, function(event)
    {
        setupInitialEVs();
        resetPips();
        setupColor();
    });
    
    document.addEventListener(common.MAP_UPDATE_EVENT, function(event)
    {
        mapArea.classList.remove("hidden");
        updateMap();
        updateDeploymentPips();
    });
}

function setupInitialEVs()
{
    max_votes_map = {};
    let room_code = document.querySelector("#room_code").value;
    let game_id = document.querySelector("#game_id").value;

    // check if there are deployments
    let url = `${api_common.API_URL_BASE}/get_initial_EVs.php?room_code=${room_code}&game_id=${game_id}`;

    common.get_request(url)
    .then(res => {
        if (res.has_errors)
        {
            alert(res.error_msg);
            return;
        }
        
        let data = res.data;
        
        // add entries in score map for each player
        let initialEVs = data.initial_EVs;
        for (const [color_id, initial_EVs_data] of Object.entries(initialEVs)) {
            common.set_player_score_map(initial_EVs_data.name, initial_EVs_data.EVs, color_id);
            
            max_votes_map[color_id] = initial_EVs_data.EVs + data.max_swing_votes;
        }
        
        common.update_map();
    })
}


function updateMap()
{
    // update map colors
    let color_map = common.get_state_color_map();
    for (const [state_abbrev, color] of Object.entries(color_map)) {
        let state_images = document.querySelectorAll(`#${state_abbrev} path`);
        state_images.forEach(state_image => {
            state_image.setAttribute("class", colorToMapClass(color));
        });
    }
    
    // update player scorebard
    let player_score_map = common.get_player_score_map();
    for (const [color, score_info] of Object.entries(player_score_map)) {
        let playerNameID;
        let playerScoreID;
        let playerBarID;
        
        if (color == RED_DB_ID)
        {
            playerNameID = RED_PLAYER_NAME;
            playerScoreID = RED_SCORE;
            playerBarID = RED_BAR;
            updateScoreboard(playerNameID, playerScoreID, playerBarID, score_info, color);
        }
        else if (color == BLUE_DB_ID)
        {
            playerNameID = BLUE_PLAYER_NAME;
            playerScoreID = BLUE_SCORE;
            playerBarID = BLUE_BAR;
            updateScoreboard(playerNameID, playerScoreID, playerBarID, score_info, color);
        }
        
        
    }
}

function updateScoreboard(playerNameID, playerScoreID, playerBarID, score_info, color)
{
    let playerName = document.querySelector(playerNameID);
    let playerScore = document.querySelector(playerScoreID);
    let playerBar = document.querySelector(playerBarID + BAR_FILL_SUFFIX)
    
    playerName.innerHTML = score_info.player_name;
    playerScore.innerHTML = score_info.score;
    
    let barPercent = score_info.score / max_votes_map[color] * 100;
    playerBar.setAttribute("style", `width: ${barPercent}%;`);
}

function colorToMapClass(color)
{
    let className = "";
    switch(color)
    {
        case RED_DB_ID:
            className = RED_IMG_CLASS;
            break;
        case BLUE_DB_ID:
            className = BLUE_IMG_CLASS;
            break;
        default:
            className=WHITE_IMG_CLASS;
    }
    
    return className;
}

function updateDeploymentPips()
{
    let room_code = document.querySelector("#room_code").value;
    let game_id = document.querySelector("#game_id").value;
    let player_name = document.querySelector("#player_name").value;

    // check if there are deployments
    let url = `${api_common.API_URL_BASE}/get_deployments.php?room_code=${room_code}&game_id=${game_id}`;
    common.get_request(url)
    .then(res => {
        if (res.has_errors)
        {
            console.log(res.err_msg);
            return;
        }
        updatePips(res.data[player_name].deployments);
    });
}

function updatePips(deployments)
{
    
    resetPips();
    deployments.forEach(deployment => {
        let num_pieces = deployment.num_pieces;
        let state_abbrev = deployment.state_abbrev;
        
        setStatePips(state_abbrev, num_pieces);
    });
}

function resetPips()
{
    let allPips = document.querySelectorAll(`.${PIPS_CLASS}`);
    allPips.forEach(pip => {
        pip.classList.add("hidden");
    })
}

function setStatePips(state_abbrev, num_pieces)
{
    let pips = document.querySelectorAll(`#${state_abbrev}${PIPS_CONTAINER_SUFFIX} .${PIPS_CLASS}`);
    
    // TODO: eventually we may need to make this modular
    num_pieces = Math.min(num_pieces, 5);
    // TODO: ^^^

    for(let i=0; i<num_pieces; i++)
    {
        pips[i].classList.remove("hidden");
    }
}

function setupColor()
{
    let room_code = document.querySelector("#room_code").value;
    let game_id = document.querySelector("#game_id").value;
    let player_name = document.querySelector("#player_name").value;

    // check if there are deployments
    let url = `${api_common.API_URL_BASE}/get_player_color.php?room_code=${room_code}&game_id=${game_id}&player_name=${player_name}`;
    common.get_request(url)
    .then(res => {
        if (res.has_errors)
        {
            console.log(res.err_msg);
            return;
        }
        setPipColors(res.data.color_id);
        setMainCss(res.data.color_id);
    });
}

function setPipColors(color_id)
{
    let pips = document.querySelector(`#${PIPS_CONTAINER}`);
    let colorClass = "";
    if (color_id === RED_DB_ID)
    {
        colorClass = PIPS_RED_CLASS;
    } else if (color_id === BLUE_DB_ID)
    {
        colorClass = PIPS_BLUE_CLASS;
    }
    
    pips.classList.add(colorClass);
}

function setMainCss(color_id)
{
    let colorClass = "";
    if (color_id === RED_DB_ID)
    {
        colorClass = MAIN_RED_CLASS;
    } else if (color_id === BLUE_DB_ID)
    {
        colorClass = MAIN_BLUE_CLASS;
    }
    mainContainer.classList.add(colorClass);
}


export function setStatesSelectable(states)
{
    chosenState = null;
    for (const state of states) {
        let stateImage = mapArea.querySelector(`#${state.state_abbrev}`);
        stateImage.setAttribute("data-state-abbrev", state.state_abbrev);
        stateImage.classList.add("selectable-state");
    }
}

export function attachStateButtonListeners()
{
    mapArea.addEventListener("click", function(event)
    {
        mapArea.querySelectorAll(".selectable-state").forEach(function(button) {
            button.classList.remove("selected");
        });
        chosenState = null;
        
        let clickedState = common.checkAncestorsForClass(event.target, "selectable-state", mapArea);
        
        // only do stuff if it is a valid clickable state
        if (clickedState) {
            chosenState = clickedState.getAttribute("data-state-abbrev");
            clickedState.classList.add("selected");
        }
    });
}

export function makeStatesUnselectable()
{
    chosenState = null;
    let selectableStates = mapArea.querySelectorAll(".selectable-state");
    for(const state of selectableStates)
    {
        state.classList.remove("selectable-state");
        state.classList.remove("selected");
    }
}

