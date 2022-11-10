import * as common from "./game_common.js";
import * as api_common from "./api_common.js";
import * as gameMap from "./game_map.js";

const IMG_PATH = "images/";

const rollTitleState = document.querySelector("#roll_title_state");
const rollTitleNoPieces = document.querySelector("#roll_title_no_pieces");
const redPipsContainer = document.querySelector("#red_pips");
const bluePipsContainer = document.querySelector("#blue_pips");
const allPips = document.querySelectorAll(".roll-pip");
const rollTitleContainer = document.querySelector("#roll-container"); 
const loadingRoll = document.querySelector("#loading-roll");

const rollArea = document.querySelector("#roll_area");
const diceArea = document.querySelector("#dice_area");
const rollButton = document.querySelector("#roll_button");
const nextButton = document.querySelector("#next_button");
const afterRollButton = document.querySelector("#after_roll_button");
const roundWinnerArea = document.querySelector("#round_winner_area");

const ROUND_CONTAINER_CLASS = "round_container";
const DICE_CONTAINER_CLASS = "dice-roll-container";
const MAX_PIECES = 10;
const WAIT_ROLL_TIME = 1000;

let currentRoll = 0;
let numRounds = 0;
let winner_color = null;
let state_abbrev = null;
let state_EVs = null;
let winner_name = null;
let prevRoll = null;

function setupRoll()
{
    setupRollEventListener();
    setupRollButton();
    setupNextButton();
    setupAfterRollButton();
}
setupRoll();

function setupRollEventListener()
{
    document.addEventListener(common.ROLL_EVENT, function(event)
    {
        startRoll();
    });
    
    document.addEventListener(common.START_EVENT, function(event)
    {
        hideRollArea();
    })
}

function startRoll()
{
    winner_color = null;
    state_abbrev = null;
    state_EVs = null;
    winner_name = null;
    currentRoll = 1;
    prevRoll = null;
    
    common.show(rollTitleContainer);
    common.show(loadingRoll);

    let room_code = document.querySelector("#room_code").value;
    let player_name = document.querySelector("#player_name").value;
    let game_id = document.querySelector("#game_id").value;
    let url = `${api_common.API_URL_BASE}/get_turn.php?game_id=${game_id}&turn_num=${common.get_api_turn_number()}`;
    common.get_request(url)
    .then(res => {
        const current_state = res.data.state_abbrev;
        common.show_turn_number();
        
        let url = `${api_common.API_URL_BASE}/get_deployments.php?room_code=${room_code}&game_id=${game_id}`;
        common.get_request(url)
        .then(res => {
            setupRollTitle(current_state, res.data);
            common.show(rollArea);
            roll_area.classList.remove("hidden");
        });
    });
    
    // only the blue player is in charge of rolling
    let color_url = `${api_common.API_URL_BASE}/get_player_color.php?room_code=${room_code}&game_id=${game_id}&player_name=${player_name}`;
    common.get_request(color_url)
    .then(res => {
        if (res.has_errors)
        {
            console.log(res.err_msg);
            return;
        }
        const colorID = res.data.color_id;
        if (colorID === gameMap.BLUE_DB_ID)
        {
            let room_code = document.querySelector("#room_code").value;
            let game_id = document.querySelector("#game_id").value;
            
            let url = `${api_common.API_URL_BASE}/roll_for_turn.php?room_code=${room_code}&game_id=${game_id}&turn_num=${common.get_api_turn_number()}`;
            
            common.get_request(url)
           .then(res => {
               let data = res.data;
               if (data.has_errors)
               {
                    console.log(data.error_msg);
                    return;
               }
               waitForRoll();
           });
        }
        else
        {
            waitForRoll();
        }
    });
    
    diceArea.innerHTML = "";
}

function showRollButton()
{
    common.hide(loadingRoll);
    common.show(rollButton);
    common.setContinueButton(roll_button);
}

function waitForRoll()
{
    let room_code = document.querySelector("#room_code").value;
    let game_id = document.querySelector("#game_id").value;
    
    let url = `${api_common.API_URL_BASE}/get_rolls_for_turn.php?room_code=${room_code}&game_id=${game_id}&turn_num=${common.get_api_turn_number()}`;
    
    common.get_request(url)
   .then(res => {
       let data = res.data;
       if (data.has_errors || !data.winner_name)
       {
            setTimeout(waitForRoll, WAIT_ROLL_TIME);
            return;
       }
       showRollButton();
   });
}

function setupRollButton()
{
    rollButton.addEventListener("click", function(e){
        common.hide(roll_button);
        common.hide(rollTitleContainer);        
        diceArea.innerHTML = "Loading...";
        let room_code = document.querySelector("#room_code").value;
        let game_id = document.querySelector("#game_id").value;
        
        let url = `${api_common.API_URL_BASE}/get_rolls_for_turn.php?room_code=${room_code}&game_id=${game_id}&turn_num=${common.get_api_turn_number()}`;
        
        common.get_request(url)
       .then(res => {
           let data = res.data;
           if (data.has_errors)
           {
               console.log(data.error_msg);
               return;
           }
           let rolls = data.rolls;
           console.log(data);
           
           diceArea.innerHTML = "";
           
           numRounds = rolls.length;
           
           for(let i=0; i<rolls.length;i++)
           {
              let round = document.createElement("div");
              round.setAttribute("id", `round_${i}`);
              if (i !== 0)
              {
                  round.classList.add("hidden");
              }
              else
              {
                  prevRoll = round;
              }
              diceArea.appendChild(round);
              
              // round title
              let title = document.createElement("h3");
              title.innerHTML = `Round ${i + 1}`;
              round.appendChild(title);
              
              // dice rolls
              let round_container = document.createElement("div");
              round_container.classList.add(ROUND_CONTAINER_CLASS);
              round.appendChild(round_container);
              for (const [id, player_rolls] of Object.entries(rolls[i])) 
              {
                  let color = data["color_map"][id];
                  let color_img_id = covert_color_id_to_img_name(color);
                  let dice_container = document.createElement("div");
                  dice_container.classList.add(DICE_CONTAINER_CLASS)
                  for (let j=0; j < player_rolls.length; j++) 
                  {
                      var dice_img = new Image (48,48);
                      dice_img.src = `${IMG_PATH}${color_img_id}${player_rolls[j].die_face}.svg`;
                      dice_container.appendChild(dice_img);
                  }
                  round_container.appendChild(dice_container);
                  //dice_area.innerHTML += "<br/>";
              }
           }
           
           winner_color = data.color_map[data.winner_id];
           state_abbrev = data.state_abbrev;
           state_EVs = data.EVs;
           winner_name = data.winner_name;
           
           common.hide(roll_button);
            common.hide(rollTitleContainer);    
        
           if (numRounds == 1)
           {
               updateMapAfterRoll(winner_color, state_abbrev, state_EVs, winner_name);
                showAfterRollButton();
           }
           else
           {
               common.show(nextButton);
               common.setContinueButton(nextButton);
           }
        })
        .catch((error) => {
          console.error('Error:', error);
        });
    });
}

function setupNextButton()
{
    nextButton.addEventListener("click", function(e)
    {
        if (currentRoll < numRounds)
        {
            let roundElement = document.querySelector(`#round_${currentRoll}`);
            common.show(roundElement);
            if (prevRoll)
            {
                common.hide(prevRoll);
            }
            prevRoll = roundElement;
            currentRoll++;
            common.setContinueButton(nextButton);
        }
        
        if (currentRoll >= numRounds)
        {
            // we are finished
            nextButton.classList.add("hidden");
            updateMapAfterRoll(winner_color, state_abbrev, state_EVs, winner_name);
            showAfterRollButton();
        }
    });
}

function setupAfterRollButton()
{
    afterRollButton.addEventListener("click", function(event)
    {
        goToCreateTurn();
    })
}

function setupRollTitle(currentStateAbbrev, deploymentData)
{
    const stateImg = document.createElement("img");
    stateImg.setAttribute("src", getStateBgImg(currentStateAbbrev));
    stateImg.classList.add("state_bg");
    rollTitleNoPieces.innerHTML = "";
    resetRollPips();
    
    let totalPieces = 0;
    
    for (const playerName in deploymentData) {
        let data = deploymentData[playerName];
        let deployments = data.deployments;
        let numPiecesForState = 0;
        
        deployments.every(deployment => {
            if (deployment.state_abbrev === currentStateAbbrev)
            {
                numPiecesForState = deployment.num_pieces;
                totalPieces += numPiecesForState;
                return false;
            }
            return true;
        });
        
        updateRollPips(data.color_id, numPiecesForState);
    }
    
    rollTitleState.innerHTML = "";
    rollTitleState.appendChild(stateImg);
    
    // add a message if neither side deployed pieces
    if (totalPieces ==0)
    {
        let noPieces = document.createElement("div");
        noPieces.innerHTML = "No campaign resources from either side";
        rollTitleNoPieces.appendChild(noPieces);
    }
}

function getStateBgImg(stateAbbrev)
{
    return `${IMG_PATH}${stateAbbrev}_bg.png`;
}

function resetRollPips()
{
    allPips.forEach(pip => {
        pip.classList.add("hidden");
    });
}

function updateRollPips(colorId, numPieces)
{
    let pipsContainer = null;
    if (colorId == common.RED_DB_ID)
    {
        pipsContainer = redPipsContainer;
    }
    else if (colorId == common.BLUE_DB_ID)
    {
        pipsContainer = bluePipsContainer;
    }
    
    if (pipsContainer)
    {
        let pips = pipsContainer.querySelectorAll(".roll-pip");
        for(let i=0; i<numPieces;i++)
        {
            pips[i].classList.remove("hidden");
        }
    }
}

function covert_color_id_to_img_name(color_id)
{
    return color_id == common.RED_DB_ID ? "R" : "B";
}

function updateMapAfterRoll(winner_color, state_abbrev, state_EVs, winner_name)
{
    // update color map
    common.add_won_state(state_abbrev, winner_color);
    
    // update player score map
    common.add_to_player_score_map(winner_name, state_EVs, winner_color);
    
    // updates the visuals
    common.update_map();
    
}

function showRoundWinner()
{
    common.show(roundWinnerArea);
    roundWinnerArea.innerHTML = `${winner_name} won ${state_abbrev}`;
}

function showAfterRollButton()
{
    // hide the roll button
    common.hide(rollButton);
    
    common.show(afterRollButton);
    showRoundWinner();
    common.refresh_event_cards();
    
    common.setContinueButton(after_roll_button);
}

function hideRollArea()
{
    common.hide(rollArea);
    common.hide(afterRollButton);
    common.hide(roundWinnerArea);
    
}

function goToCreateTurn()
{
    hideRollArea();
    
    // increase turn counter
    common.incrementTurn();
    
    common.deploy_event(common.CREATE_TURN_EVENT);
}