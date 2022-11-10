import * as common from "./game_common.js";
import * as api_common from "./api_common.js";

const TOTAL_PIECES_ID = "#total_pieces";

const DEPLOYMENTS_AREA_ID = "#deployments_area";
const DEPLOY_PIECES_AREA_ID = "#deploy_pieces_area";
const NEXT_AREA_ID = "#create_turn_area";
const CHECK_OTHER_DEPLOY_RATE = 2000;

const deploymentsWaitingArea = document.querySelector("#waiting_depoyments_area");

// deployment pieces
const DEPLOYMENT_PIECES_MAX = 24;
let piecesUsed = 0;

// all the state deployments
let state_deployments = {};

function setupDeployment()
{
    setupDeploymentEventListener();
    setupSubmit();
}
setupDeployment();

function setupDeploymentEventListener()
{
    let deploymentArea = document.querySelector(DEPLOYMENTS_AREA_ID);
    document.addEventListener(common.DEPLOYMENT_EVENT, function(event)
    {
        deploymentArea.classList.remove("hidden");
        checkAlreadyDeployed();
    });
}

function setupSubmit() {
    var submitButton = document.querySelector("#submit_deployments");
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
                checkOtherPlayerDeployed();
            }
            else
            {
                alert(data.err_msg);
                submitButton.removeAttribute("disabled");
            }
        })
        .catch((error) => {
          alert('Error:', error);
        });
    });
}

function deployPieces(elem_ID) {
  var x = document.getElementById(elem_ID);
  document.getElementById("demo").innerHTML = "You selected: "+ elem_ID + " " + x.value;
}

var list = document.querySelector("#input-state-deployments");
list.addEventListener("change", function(e){
	if(e.target.className == "state-slider"){
		var li = e.target.parentElement;
		update_deployment_data();
	}
})

function update_deployment_data() {
    piecesUsed = 0;
    
	let dNV = document.getElementById("NV_pieces").value;
	let dAZ = document.getElementById("AZ_pieces").value;
	let dCO = document.getElementById("CO_pieces").value;
	let dMN = document.getElementById("MN_pieces").value;
	let dWI = document.getElementById("WI_pieces").value;
	let dMI = document.getElementById("MI_pieces").value;
	let dFL = document.getElementById("FL_pieces").value;
	let dGA = document.getElementById("GA_pieces").value;
	let dNC = document.getElementById("NC_pieces").value;
	let dVA = document.getElementById("VA_pieces").value;
	let dPA = document.getElementById("PA_pieces").value;
	let dNH = document.getElementById("NH_pieces").value;
	
	document.getElementById("dNV").innerText = dNV;
	document.getElementById("dAZ").innerText = dAZ;
	document.getElementById("dCO").innerText = dCO;
	document.getElementById("dMN").innerText = dMN;
	document.getElementById("dWI").innerText = dWI;
	document.getElementById("dMI").innerText = dMI;
	document.getElementById("dFL").innerText = dFL;
	document.getElementById("dGA").innerText = dGA;
	document.getElementById("dNC").innerText = dNC;
	document.getElementById("dVA").innerText = dVA;
	document.getElementById("dAZ").innerText = dAZ;
	document.getElementById("dPA").innerText = dPA;
	document.getElementById("dNH").innerText = dNH;
	
	let state_input_list = document.querySelectorAll("#input-state-deployments li input");
	
	state_input_list.forEach((state_input) => {
	    state_deployments[state_input.getAttribute("data-abbrev")] = state_input.value;
	    
	    piecesUsed += parseInt(state_input.value);
	});
	
	let totalPiecesDiv = document.querySelector(TOTAL_PIECES_ID);
	totalPiecesDiv.innerHTML = DEPLOYMENT_PIECES_MAX - piecesUsed;
	
	if (piecesUsed > DEPLOYMENT_PIECES_MAX)
	{
	    totalPiecesDiv.style = "color: red";
	}
	else if (piecesUsed === DEPLOYMENT_PIECES_MAX)
	{
	    totalPiecesDiv.style = "color: green";
	}
	else
	{
	    totalPiecesDiv.style = "color: black";
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
            else
            {
                showDeployPiecesArea();
            }
        }
        else
        {
            alert(data.err_msg);
        }
    })
    .catch((error) => {
      alert('Error:', error);
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
            alert(data.err_msg);
        }
    })
    .catch((error) => {
      alert('Error:', error);
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
