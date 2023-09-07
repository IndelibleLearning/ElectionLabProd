import * as common from "./game_common.js";
import * as api_common from "./api_common.js";
import {get_request, getRoomUrl, show} from "./game_common.js";

// game details keys
const ROOM_CODE_KEY = "indl_elo_room_code";
const PLAYER_NAME_KEY = "indl_elo_player_name";
const GAME_ID_KEY = "indl_elo_game_id";

// html ids
const GAME_INPUT_ENTRY = "#game_entry";
const ROOM_CODE_INPUT_ID = "#room_code";
const PLAYER_NAME_INPUT_ID = "#player_name";
const GAME_ID_INPUT_ID = "#game_id";

const ROOM_CODE_BUTTON_ID = "#save_room_code";
const PLAYER_NAME_BUTTON_ID = "#save_player_name";
const GAME_ID_BUTTON_ID = "#save_game_id";
const START_GAME_BUTTON_ID = "#start_game";
const BACK_TO_LOBBY_ID = "#back_to_lobby";

const GAME_TERMINATED_MODAL = "#game-terminated";
const GAME_TERMINATED_BUTTON = "#game-terminated-button";

const CHECK_STILL_RUNNING_PERIOD = 1000;

function setup()
{
    setupGameDetailsInputs();
    setupStartGameButton();
    setupBackToRoomButton();
    setupCheckGameStillRunning();
    common.deploy_event(common.UPDATE_MAP_EVENT);
    startGame();
}
setup();

function setupGameDetailsInputs()
{
    setupGameDetail(ROOM_CODE_BUTTON_ID, ROOM_CODE_INPUT_ID, ROOM_CODE_KEY);
    setupGameDetail(PLAYER_NAME_BUTTON_ID, PLAYER_NAME_INPUT_ID, PLAYER_NAME_KEY);
    setupGameDetail(GAME_ID_BUTTON_ID, GAME_ID_INPUT_ID, GAME_ID_KEY);
}

function setupGameDetail(buttonId, inputId, saveKey) 
{
    let input = document.querySelector(inputId);
    
     // check if there is save data
    let saveData = window.localStorage.getItem(saveKey);
    // if there is, then fill it in
    if (saveData)
    {
        input.value = saveData;
    }
    
    // attach listener to help save
    let button = document.querySelector(buttonId);
    button.addEventListener("click", function(e){
        window.localStorage.setItem(saveKey, input.value);
    });
}

function setupStartGameButton()
{
    let start_button = document.querySelector(START_GAME_BUTTON_ID);
    
    start_button.addEventListener("click", function(event){
        startGame();
    });
    
}

function startGame()
{
    let room_code = document.querySelector("#room_code").value;
    let game_id = document.querySelector("#game_id").value;
    let player_name = document.querySelector("#player_name").value;
    
   // alert("starting game");
    
    // send start game event
    common.deploy_event(common.START_EVENT);
    common.set_turn_number(1);

    // check if there are deployments
    
    const gameData = {
        "room_code": room_code,
        "game_id": game_id
    }
    let url = `${api_common.API_URL_BASE}/all_deployments_ready.php`;

    fetch(url, 
    {
		method: "POST",
		headers: {
          'Content-Type': 'application/json'
        },
		body: JSON.stringify(gameData)
	})
    .then(response => response.json())
    .then(data => {
        if (data.has_errors)
        {
            alert(data.err_msg);
            return;
        }
        let inputArea = document.querySelector(GAME_INPUT_ENTRY);
        inputArea.classList.add("hidden");
        if(data.data) 
        {
            // already have deployments so go straight to turn 1
            common.deploy_event(common.CREATE_TURN_EVENT);
        }
        else
        {
            // No depoyments so go to deployment area
            common.deploy_event(common.DEPLOYMENT_EVENT);
        }
    })
    .catch((error) => {
        alert('Error:', error);
    });
}

function setupBackToRoomButton()
{
    const room_code = document.querySelector("#room_code").value;
    const backToRoom = document.querySelector(BACK_TO_LOBBY_ID);
    backToRoom.addEventListener("click", event => 
    {
        if (confirm("Are you sure you want to leave the game?")) {
            window.location = common.getRoomUrl(room_code);
        }
    });
}

function setupCheckGameStillRunning()
{
    verifyGameStillRunning();
}

function verifyGameStillRunning()
{
    let room_code = document.querySelector(ROOM_CODE_INPUT_ID).value;
    let player_name = document.querySelector(PLAYER_NAME_INPUT_ID).value;
    let game_id = document.querySelector(GAME_ID_INPUT_ID).value;
    const url =  common.CHECK_GAME_RUNNING_BASE + room_code + "&player_name=" + player_name + "&game_id=" + game_id;
    get_request(url)
        .then(res=>{
            setTimeout(verifyGameStillRunning, CHECK_STILL_RUNNING_PERIOD);
            if (res.has_errors){
                console.log(res.error_msg);
                return;
            }
            if (!res.data)
            {
                showGameTerminated();
            }
        });
}

function showGameTerminated()
{
    let modal = document.querySelector(GAME_TERMINATED_MODAL);
    setupTerminatedButton();
    common.show(modal);
}

function setupTerminatedButton()
{
    let terminatedButton = document.querySelector(GAME_TERMINATED_BUTTON);
    terminatedButton.addEventListener("click", () => {
        const room_code = document.querySelector("#room_code").value;
        window.location = common.getRoomUrl(room_code);
    });
}