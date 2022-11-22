import * as request from "./request.js";
import * as user_common from "./user_common.js";
import * as player_common from "./player_common.js";
import * as api_common from "./api_common.js";

const matchedPlayerList = document.querySelector("#matched_player_list");
const unmatchedPlayerList = document.querySelector("#unmatched_player_list");

const matchPlayersButtonContainer = document.querySelector("#match_players_button_container");
const matchPlayersButton = document.querySelector("#match_players_button");
const eventModeButtonContainer = document.querySelector("#event_mode_button_container");
const eventModeButton = document.querySelector("#event_mode_button");
const joinGameButton = document.querySelector("#join_game_button");
const newGameButton = document.querySelector("#new_game_button");
const joinUrl = document.querySelector("#join-link");
let room_code = "";

const CHECK_MATCH_RATE_MS = 1000;
const LOAD_PLAYERS_RATE_MS = 1000;
const CHECK_AUTO_MATCH_RATE = 5000;
const EVENTS_GAME_MODE_ID = 2;
const STANDARD_GAME_MODE_ID = 1;

let autoMatchInterval = null;
let eventsEnabled = false;

function setup()
{
    room_code = request.queryParam("room_code");
    setupJoinUrl();
    setupRoomCodeDisplay();
    setupPlayerNameDisplay();
    if (player_common.getPlayerFinishedGame())
    {
        promptForNewGame();
    }
    else
    {
        setupCheckForGame();
    }
    loadPlayers();
    setupMatchButton();
    setupEventModeButton();
}
setup();

function setupJoinUrl()
{
    joinUrl.innerHTML = api_common.PLAYER_JOIN_URL;
}

function setupRoomCodeDisplay()
{
    const roomCodeDisplay = document.querySelector("#title_room_code");
    roomCodeDisplay.innerHTML = room_code;
}

function setupPlayerNameDisplay()
{
    const playerName = player_common.getPlayerName();
    if (playerName)
    {
        const playerNameContainer = document.querySelector("#player_name_container");
        playerNameContainer.classList.remove("hidden");
            const playerNameDisplay = document.querySelector("#player_name_display");
        playerNameDisplay.innerHTML = playerName;
    }
}

function setupCheckForGame()
{
    const player_name = player_common.getPlayerName();
    
    if (player_name)
    {
        checkGameStarted(player_name);
    }
}

function checkGameStarted(player_name)
{
    let url = player_common.checkPlayerHasGameURL(room_code, player_name);
    request.get(url)
    .then(res => {
        if (res.has_errors)
        {
            console.log(res.err_msg);
            return;
        }
        
        if (res.data && res.data.game_id)
        {
            // we have a game so start the prompt
            promptJoinGame(res.data.game_id)
            return;
        }
        
        setTimeout(checkGameStarted, CHECK_MATCH_RATE_MS, player_name);
    });
}

function loadPlayers()
{
    let url = user_common.GET_PLAYERS_IN_ROOM_BASE + room_code;
    request.get(url)
    .then(res => {
        if (res.has_errors)
        {
            console.log(res.err_msg);
            return;
        }
        
        matchedPlayerList.innerHTML = "";
        unmatchedPlayerList.innerHTML = "";
        
        let players = res.data;
        
        if (players)
        {
            players.forEach(player => {
                if (player.game_id)
                {
                    matchedPlayerList.appendChild(createPlayer(player));
                }
                else
                {
                    unmatchedPlayerList.appendChild(createPlayer(player));
                }
            });
        }
        
        setTimeout(loadPlayers, LOAD_PLAYERS_RATE_MS);
    })
}

function createPlayer(player)
{
    let playerContainer = document.createElement("div");
    playerContainer.innerHTML = player.player_name;
    
    if (player.player_name == player_common.getPlayerName())
    {
        playerContainer.classList.add("local_player");
    }
    return playerContainer;
}

function setupMatchButton()
{
    // only show if we are the "host" TODO: Update this to actually check host of room
    if (user_common.getUserName() && user_common.getLoginToken())
    {
        matchPlayersButtonContainer.classList.remove("hidden");
        matchPlayersButton.checked = false;
    }
    matchPlayersButton.addEventListener("click", event => {
        if (matchPlayersButton.checked)
        {
            setupAutoMatch();
        }
        else
        {
            clearAutoMatch();
        }
    });
}

function setupAutoMatch()
{
    autoMatch();
    autoMatchInterval = setInterval(autoMatch, CHECK_AUTO_MATCH_RATE)
}

function clearAutoMatch()
{
    clearInterval(autoMatchInterval);
    autoMatchInterval = null;
}

function autoMatch()
{
    let gameMode = eventsEnabled ? EVENTS_GAME_MODE_ID : STANDARD_GAME_MODE_ID;
    let url = user_common.matchPlayersUrl(room_code, gameMode);
    request.get(url)
    .then(res => {
        matchPlayersButton.removeAttribute("disabled");
        if (res.has_errors)
        {
            console.log(res.err_msg);
            return;
        }
        
        //alert("Matches created!");
        //loadPlayers();
    })
}

function promptJoinGame(gameId)
{
    player_common.setPlayerGameId(gameId);
    if(confirm("Game found. Would you like to join the game?"))
    {
        window.location = player_common.GAME_URL;
    }
    else
    {
        setupJoinGameButton();
    }
}

function setupJoinGameButton()
{
    // add button that lets you retroactively join the game
    joinGameButton.classList.remove("hidden");
    joinGameButton.addEventListener("click", event => {
        window.location = player_common.GAME_URL;
    });
}

function promptForNewGame()
{
    if(confirm("You finished your game! Would you like to queue up for another game?"))
    {
        leaveGame();
    }
    else
    {
        setupNewGameButton();
        setupJoinGameButton();
    }
}

function setupNewGameButton()
{
    newGameButton.classList.remove("hidden");
    newGameButton.addEventListener("click", event => {
        newGameButton.setAttribute("disabled", true);
        leaveGame();
    });
}

function leaveGame()
{
    const player_name = player_common.getPlayerName();
    let url = player_common.getLeaveGameURL(room_code, player_name);
    request.get(url)
    .then(res => {
        newGameButton.removeAttribute("disabled");
        if (res.has_errors)
        {
            console.log("Error when leaving game: " + res.err_msg);
            return;
        }
        
        alert("Reentered matching area")
        joinGameButton.classList.add("hidden");
        newGameButton.classList.add("hidden");
        player_common.resetPlayerFinishedGame();
        setupCheckForGame();
    })
}

function setupEventModeButton()
{
    if (user_common.getUserName() && user_common.getLoginToken())
    {
        eventModeButtonContainer.classList.remove("hidden");
        eventModeButton.checked = false; 
    }
    eventModeButton.addEventListener("click", event => {
        if (eventModeButton.checked)
        {
            eventsEnabled = true;;
        }
        else
        {
            eventsEnabled = false;;
        }
    });
}
