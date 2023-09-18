import * as common from "./game_common.js";
import * as api_common from "./api_common.js";
import {doConfettiEffect} from "./confetti.js";
import {customConfirm} from "./modal.js";

const finishArea = document.querySelector("#finish-area");
const scoreboard = document.querySelector("#scoreboard");
const postGameButton = document.querySelector("#post-game-button");
const finishBackToLobbyButton = document.querySelector("#finish-back-to-lobby");

function setupFinish()
{
    setupFinishEventListener();
    //setupGoToPostGameButton(); Scrap for now
    setupFinishBackToLobbyButton();
}
setupFinish();

function setupFinishEventListener()
{
    document.addEventListener(common.FINISH_EVENT, function(event)
    {
        showFinish();
    });
}

function setupGoToPostGameButton()
{
    postGameButton.addEventListener("click", e => {
        //common.hide(finishArea);
        common.deploy_event(common.POST_GAME_EVENT);
    })
}

function setupFinishBackToLobbyButton()
{
    const room_code = document.querySelector("#room_code").value;
    finishBackToLobbyButton.addEventListener("click", ()=>{
        customConfirm("Are you sure you want to leave the game?", (userClickYes) => {
            if (userClickYes)
            {
                window.location = common.getRoomUrl(room_code);
            }
        });
    });

}

function showFinish()
{
    let room_code = document.querySelector("#room_code").value;
    let game_id = document.querySelector("#game_id").value;
    let url = `${api_common.API_URL_BASE}/get_winner.php?room_code=${room_code}&game_id=${game_id}`;
    common.get_request(url)
    .then(res=>{
       common.set_finished_game();
       common.show(finishArea);
       if (res.data.winner_name)
       {
            scoreboard.innerHTML = "Winner is " + res.data.winner_name;
            doConfettiEffect();
       }
       else
       {
            scoreboard.innerHTML = "Tie game!";
       }
    });
}