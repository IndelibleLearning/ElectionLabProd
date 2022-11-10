import * as request from "./request.js";
import * as user_common from "./user_common.js";

const roomsList = document.querySelector("#rooms_list");
const createNewRoomButton = document.querySelector("#create_room_button");

const roomCreationContainer = document.querySelector("#room_creation");
const roomNameInput = document.querySelector("#room_name");
const submitNewRoomButton = document.querySelector("#submit_new_room");
const closeButton = document.querySelector("#close_button");

function setup()
{
 
    loadRooms();
    setupCreateNewRoomButton();
    createSubmitNewRoomButton();
    setupCloseButton();
}
setup();

function loadRooms()
{
    let url = user_common.GET_ROOMS_URL
    let data = {
        "user_name": user_common.getUserName(),
        "token": user_common.getLoginToken()
    }
    request.post(url, data)
    .then(res => {
        if (res.has_errors)
        {
            console.log(res.err_msg);
            return;
        }
        
        let rooms = res.data;
        if (rooms)
        {
            roomsList.innerHTML = "";
            rooms.forEach((room) => {
                createRoomListButton(room);
            })
        }
    })
}

function createRoomListButton(room)
{
    let roomButton = document.createElement("button");
    roomButton.innerHTML = room.room_name;
    roomButton.setAttribute("data-room-code", room.room_code);
    
    roomButton.addEventListener("click", event => {
        window.location.href = user_common.getRoomUrl(room.room_code);
    });
    
    roomsList.appendChild(roomButton);
}

function setupCreateNewRoomButton()
{
    createNewRoomButton.addEventListener("click" , event => {
       roomCreationContainer.classList.remove("hidden");
    });
}

function createSubmitNewRoomButton()
{
    submitNewRoomButton.addEventListener("click", event => {
        let roomName = roomNameInput.value;
        if (!roomName) 
        {
            alert("Please enter a room name");
            return;
        }
        
        submitNewRoomButton.setAttribute("disabled", true);
        
        let url = user_common.CREATE_ROOM_URL;
        let data = {
            "user_name": user_common.getUserName(),
            "token": user_common.getLoginToken(),
            "room_name": roomName
        }
        request.post(url, data)
        .then(res => {
            submitNewRoomButton.removeAttribute("disabled");
            if (res.has_errors)
            {
                alert(res.err_msg);
                return;
            }
            alert("Created room");
            roomCreationContainer.classList.add("hidden");
            loadRooms();
        });
    });
}

function setupCloseButton()
{
    closeButton.addEventListener("click", event => {
        roomCreationContainer.classList.add("hidden");
        submitNewRoomButton.removeAttribute("disabled");
    });
}

