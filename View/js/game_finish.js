import * as common from "./game_common.js";
import * as api_common from "./api_common.js";

const finishArea = document.querySelector("#finish-area");
const scoreboard = document.querySelector("#scoreboard");
const postGameButton = document.querySelector("#post-game-button");

function setupFinish()
{
    setupFinishEventListener();
    setupGoToPostGameButton();
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
       }
       else
       {
            scoreboard.innerHTML = "Tie game!";
       }
    });
}