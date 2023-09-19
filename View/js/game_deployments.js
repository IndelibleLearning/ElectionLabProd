import * as common from "./game_common.js";
import * as api_common from "./api_common.js";
import * as game_tutorials from "./game_tutorials.js"

const TOTAL_PIECES_ID = "#total_pieces";

const DEPLOYMENTS_AREA_ID = "#deployments_area";
const DEPLOY_PIECES_AREA_ID = "#deploy_pieces_area";
const NEXT_AREA_ID = "#create_turn_area";
const CHECK_OTHER_DEPLOY_RATE = 2000;

const deploymentsWaitingArea = document.querySelector("#waiting_depoyments_area");
const hideDeploymentsButton = document.querySelector("#hide-deployments-button");
const submitButton = document.querySelector("#submit_deployments");

// tutorial
const colorModal = document.querySelector("#show-color-modal");
const dismissColorModalButton = document.querySelector("#dismiss-color-modal-button");
const colorMessage = document.querySelector("#player-color-message");
const showTutorialButton = document.querySelector("#show-tutorial-button");

// deployment pieces
const DEPLOYMENT_PIECES_MAX = 24;
let piecesUsed = 0;

// all the state deployments
let state_deployments = {};

function setupDeployment()
{
    setupDeploymentEventListener();
    setupHideDeploymentButton();
    setupSubmit();
    setupSliders();
}
setupDeployment();

function setupDeploymentEventListener()
{
    let deploymentArea = document.querySelector(DEPLOYMENTS_AREA_ID);
    document.addEventListener(common.DEPLOYMENT_EVENT, function(event)
    {
        common.show(deploymentArea);
        //common.show(hideDeploymentsButton);
        setupColorModal();
        common.show(colorModal);
        checkAlreadyDeployed();
    });
}

function setupColorModal()
{
    setColorMessage();
    dismissColorModalButton.addEventListener("click", () =>
    {
        common.hide(colorModal);
        common.show(hideDeploymentsButton);
        showDeployPiecesArea();
    });
    showTutorialButton.addEventListener("click", game_tutorials.showTutorial);
    common.show(colorModal);
}

function setColorMessage()
{
    const color = common.getPlayerColor();
    if (color)
    {
        colorMessage.innerHTML = `You are <div class="player-color">${color}</div>`;
    }
    else
    {
        setTimeout(setColorMessage, 200);
    }

}

function setupHideDeploymentButton()
{
    let deploymentArea = document.querySelector(DEPLOYMENTS_AREA_ID);
    hideDeploymentsButton.addEventListener("click", () => {
        if (common.isHidden(deploymentArea))
        {
            common.show(deploymentArea);
            hideDeploymentsButton.innerHTML = "Hide Deployments";
        }
        else
        {
            common.hide(deploymentArea);
            hideDeploymentsButton.innerHTML = "Show Deployments";
        }
    });
}
function setupSubmit() {
    submitButton.addEventListener("click", function(e){
        submitButton.setAttribute("disabled", "true");
        // first check if the num pieces is right
        if (piecesUsed !== DEPLOYMENT_PIECES_MAX) 
        {
            alert(`Incorrect number of pieces! Deployed ${piecesUsed} when you need ${DEPLOYMENT_PIECES_MAX}`)
            submitButton.removeAttribute("disabled");
            return;
        }
        let deploymentData = {};
        
        // game details inputs
        let room_code = document.querySelector(common.ROOM_CODE_INPUT_ID);
        let player_name = document.querySelector(common.PLAYER_NAME_INPUT_ID);
        let game_id = document.querySelector(common.GAME_ID_INPUT_ID);

        deploymentData.room_code = room_code.value;
        deploymentData.player_name = player_name.value;
        deploymentData.game_id = game_id.value;
        deploymentData.deployments = format_states_for_api();

        fetch(`${api_common.API_URL_BASE}/deploy_pieces.php`, {
			method: "POST",
			headers: {
              'Content-Type': 'application/json'
            },
			body: JSON.stringify(deploymentData)
		})
        .then(response => response.json())
        .then(data => {
            if (!data.has_errors)
            {
                alert("Successful deployment");
                hideDeployPiecesArea();
                hideDeploymentsArea();
                common.hide(hideDeploymentsButton);
                checkOtherPlayerDeployed();
            }
            else
            {
                console.log(data.err_msg);
                submitButton.removeAttribute("disabled");
            }
        })
        .catch((error) => {
            console.log('Error:', error);
        });
    });
}

function setupSliders()
{
    let room_code = document.querySelector(common.ROOM_CODE_INPUT_ID).value;
    let game_id = document.querySelector(common.GAME_ID_INPUT_ID).value;
    let player_name = document.querySelector(common.PLAYER_NAME_INPUT_ID).value;

    let url = `${api_common.API_URL_BASE}/get_unwon_states.php?room_code=${room_code}&game_id=${game_id}&player_name=${player_name}`;
    common.get_request(url)
    .then(res=>{
        if (res.has_errors){
            console.log(res.error_msg);
            return;
        }

        generateStateSliders(res.data);
    });
}

function generateStateSliders(states) {
    let list = document.querySelector("#input-state-deployments");
    list.innerHTML = '';

    states.sort(function(a, b) {
        return b.electoral_votes - a.electoral_votes;
    });

    states.forEach(state => {
        console.log(state);
        let li = document.createElement("li");

        let input = document.createElement("input");
        input.type = "range";
        input.className = "state-slider";
        input.orient = "vertical";
        input.min = "0";
        input.max = "5";
        input.step = "1";
        input.value = "0";
        input.id = `${state.state_abbrev}_pieces`;
        input.setAttribute("data-abbrev", state.state_abbrev);

        let label = document.createElement("label");
        label.htmlFor = input.id;
        label.className = "state-slider-label";
        label.innerText = `${state.state_abbrev} ${state.electoral_votes}`;

        let div = document.createElement("div");
        div.id = `d${state.state_abbrev}`;
        div.className = "state-pip-display";
        div.innerText = "0";

        li.appendChild(input);
        li.appendChild(label);
        li.appendChild(div);

        list.appendChild(li);
    });
}

var list = document.querySelector("#input-state-deployments");
list.addEventListener("change", function(e){
	if(e.target.className == "state-slider"){
		update_deployment_data();
	}
})

function update_deployment_data() {
    piecesUsed = 0;
    let state_input_list = document.querySelectorAll("#input-state-deployments li input");

    state_input_list.forEach((state_input) => {
        let stateAbbrev = state_input.getAttribute("data-abbrev");
        let stateValue = state_input.value;
        state_deployments[stateAbbrev] = stateValue;

        let displayElement = document.getElementById(`d${stateAbbrev}`);
        displayElement.innerText = stateValue;

        piecesUsed += parseInt(stateValue);
    });

    updateTotalPiecesDisplay(piecesUsed);
}

function updateTotalPiecesDisplay(piecesUsed) {
    let totalPiecesDiv = document.querySelector(TOTAL_PIECES_ID);
    totalPiecesDiv.innerHTML = DEPLOYMENT_PIECES_MAX - piecesUsed;

    if (piecesUsed > DEPLOYMENT_PIECES_MAX) {
        totalPiecesDiv.style = "color: red";
        submitButton.setAttribute("disabled", "true");
    } else if (piecesUsed === DEPLOYMENT_PIECES_MAX) {
        submitButton.removeAttribute("disabled");
        totalPiecesDiv.style = "color: green";
    } else {
        totalPiecesDiv.style = "color: black";
        submitButton.removeAttribute("disabled");
    }
}


function format_states_for_api() 
{
    let formatted_data = [];
    
    for (const state in state_deployments) {
        let pieces = state_deployments[state];
        
        formatted_data.push({
            "state": state,
            "pieces": pieces
        });
    }
    
    return formatted_data;
}

function checkAlreadyDeployed()
{
    let room_code = document.querySelector(common.ROOM_CODE_INPUT_ID).value;
    let game_id = document.querySelector(common.GAME_ID_INPUT_ID).value;
    let player_name = document.querySelector("#player_name").value;
    
    const gameData = {
        "room_code": room_code,
        "game_id": game_id,
        "player_name": player_name
    }
    fetch(`${api_common.API_URL_BASE}/has_deployments.php`, {
		method: "POST",
		headers: {
          'Content-Type': 'application/json'
        },
		body: JSON.stringify(gameData)
	})
    .then(response => response.json())
    .then(data => {
        if (!data.has_errors)
        {
            if (data.data === true)
            {
                checkOtherPlayerDeployed();
            }
        }
        else
        {
            console.log(data.err_msg);
        }
    })
    .catch((error) => {
        console.log('Error:', error);
    });
}

function checkOtherPlayerDeployed()
{
    console.log("checking other deployed");
    common.show(deploymentsWaitingArea);
    hideDeploymentsArea()
    let room_code = document.querySelector(common.ROOM_CODE_INPUT_ID).value;
    let game_id = document.querySelector(common.GAME_ID_INPUT_ID).value;
    
    const gameData = {
        "room_code": room_code,
        "game_id": game_id
    }
    fetch(`${api_common.API_URL_BASE}/all_deployments_ready.php`, {
		method: "POST",
		headers: {
          'Content-Type': 'application/json'
        },
		body: JSON.stringify(gameData)
	})
    .then(response => response.json())
    .then(data => {
        if (!data.has_errors)
        {
            if (data.data === true)
            {
                common.hide(deploymentsWaitingArea);
                common.deploy_event(common.CREATE_TURN_EVENT);
            } 
            else
            {
                setTimeout(checkOtherPlayerDeployed, CHECK_OTHER_DEPLOY_RATE);
            }
        }
        else
        {
            console.log(data.err_msg);
        }
    })
    .catch((error) => {
        console.log('Error:', error);
    });
}

function showDeployPiecesArea()
{
    let deployPiecesArea = document.querySelector(DEPLOY_PIECES_AREA_ID);
    common.show(deployPiecesArea);
}

function hideDeployPiecesArea()
{
    let deployPiecesArea = document.querySelector(DEPLOY_PIECES_AREA_ID);
    common.hide(deployPiecesArea);
}

function hideDeploymentsArea()
{
    let deploymentsArea = document.querySelector(DEPLOYMENTS_AREA_ID);
    common.hide(deploymentsArea);
}
