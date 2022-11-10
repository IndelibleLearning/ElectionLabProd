import * as request from "./request.js";
import * as user_common from "./user_common.js";
import * as player_common from "./player_common.js";

let roomCodeInput = document.querySelector("#room_code");
let playerNameInput = document.querySelector("#player_name");

let submitButton = document.querySelector("#submit");

function setup()
{
    checkForPreviousRoom();
    
    submitButton.addEventListener("click", function(event)
    {
        submitButton.setAttribute("disabled", true);
        
        let roomCode = roomCodeInput.value;
        let playerName = playerNameInput.value;
        
        if (!roomCode || !playerName)
        {
            alert ("Please enter room code and player name");
            submitButton.removeAttribute("disabled");
            return;
        }
        
        let url = user_common.JOIN_ROOM_BASE + `?room_code=${roomCode}&player_name=${playerName}`;
        
        request.get(url)
        .then(res => {
           console.log(res); 
           if (res.has_errors)
           {
               alert(res.err_msg);
               submitButton.removeAttribute("disabled");
               return;
           }
           
           player_common.setPlayerName(playerName);
           player_common.setPlayerRoom(roomCode);
           player_common.resetPlayerFinishedGame();
           
           // redirect to 
           window.location = user_common.getRoomUrl(roomCode);
        });
    });
}

setup();

function checkForPreviousRoom()
{
    if(player_common.getPlayerRoom() && player_common.getPlayerName())
    {
        let join_again = confirm(`You have already joined a room as ${player_common.getPlayerName()}! Would you like to join it again?`);
        
        if (join_again)
        {
            window.location = user_common.getRoomUrl(player_common.getPlayerRoom());
        }
    }
}