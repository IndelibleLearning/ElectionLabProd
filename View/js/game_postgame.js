import * as common from "./game_common.js";
import * as api_common from "./api_common.js";

const postGameArea = document.querySelector("#post-game-area");
const strategies = document.querySelector("#strategies");
const answerQuestionButton = document.querySelector("#answer-question");
const radioChoices = document.querySelectorAll(".strategy-radio");
const blankStrat = document.querySelector("#blank-strategy");

let strategiesData = setupStrategies();
let fake = fakeData();

const BAR_UNIT = 40;
const BAR_HEIGHT_MAP = {
    0: 8,
    40: 168,
    80: 128,
    120: 88,
    160: 48,
    200: 8
}
const STRATEGY_IMAGE_PREFIX = "#strategy-"
const STRATEGY_CONTAINER_PREFIX = "#strategy-container-"

let correctAnswer = null;

function setupPostGame()
{
    setupPostgameEventListener();
    setupSubmitPostGameAnswerButton();
}
setupPostGame();

function setupPostgameEventListener()
{
    document.addEventListener(common.POST_GAME_EVENT, function(event)
    {
        showPostGame();
    });
}

function setupSubmitPostGameAnswerButton()
{
    answerQuestionButton.addEventListener("click", e => {
        common.hide(answerQuestionButton);
        let chosenAnswer = null;
        
        for (let i = 0; i < radioChoices.length; i++) {
           const radio = radioChoices[i];
           if (radio.checked)
            {
                chosenAnswer = radio.value;
            }
            common.hide(radio);
        }
        
        let correct = document.querySelector(`${STRATEGY_CONTAINER_PREFIX}${correctAnswer}`);
        correct.classList.add("correct");
        //replaceStrategy("B", fake);
        submitAnswer(correctAnswer, chosenAnswer);
    })
}

function submitAnswer(correctAnswer, chosenAnswer)
{
    let player_name = document.querySelector("#player_name").value;
    let game_id = document.querySelector("#game_id").value;
    let url = `${api_common.API_URL_BASE}/histogram_answer.php?player_name=${player_name}&game_id=${game_id}&correct_answer=${correctAnswer}&player_answer=${chosenAnswer}`;
    common.get_request(url)
    .then(res=>{
        if (res.has_errors){
            console.log(res.error_msg);
            return;
        }
        console.log("Success")
    });
}

function showPostGame()
{
    let room_code = document.querySelector("#room_code").value;
    let player_name = document.querySelector("#player_name").value;
    let game_id = document.querySelector("#game_id").value;
    let url = `${api_common.API_URL_BASE}/get_initial_deployments.php?room_code=${room_code}&game_id=${game_id}`;
    common.get_request(url)
    .then(res=>{
        common.show(postGameArea);
        const deployments = res.data;
        
        substituteClosestStrategy(deployments[player_name]["deployments"]);
    });
}

function substituteClosestStrategy(playerDeployments)
{
    const closestStrategyLetter = findClosest(playerDeployments);
    //const stratData = strategiesData[closestStrategyLetter];
    correctAnswer = closestStrategyLetter;
    
    replaceStrategy(closestStrategyLetter, playerDeployments);
}

function replaceStrategy(closestLetter, playerDeployments)
{
    const replaceStrat = document.querySelector(`${STRATEGY_IMAGE_PREFIX}${closestLetter}`);
    
    blankStrat.setAttribute("id", `${STRATEGY_IMAGE_PREFIX}${closestLetter}`);
    
    for(let i=0; i < playerDeployments.length; i++)
    {
        const deployment = playerDeployments[i];
        const bar = blankStrat.querySelector(`#bar${deployment["state_abbrev"]}`);
        const height = BAR_UNIT * deployment["num_pieces"];
        bar.setAttribute("height", `${height}px`);
        const y = BAR_HEIGHT_MAP[height];
        bar.setAttribute("y", `${y}px`);
    }
    
    replaceStrat.replaceWith(blankStrat);
}

function findClosest(playerDeployments)
{
    let smallestDifference = null;
    let closestStratLetter = null;
    
    for(const stratLetter in strategiesData)
    {
        const stratData = strategiesData[stratLetter];
        const difference = findDifference(stratData, playerDeployments);
        
        if (smallestDifference == null || difference < smallestDifference)
        {
            smallestDifference = difference;
            closestStratLetter = stratLetter;
        }
    }
    
    return closestStratLetter;
}

function findDifference(stratData, playerDeployments)
{
    let totalDiff = 0;
    for(let i=0; i < playerDeployments.length; i++)
    {
        const deployment = playerDeployments[i];
        const playerPieces = deployment["num_pieces"];
        const stratPieces = stratData[deployment["state_abbrev"]];
        
        totalDiff += Math.abs(playerPieces - stratPieces);
    }
    
    return totalDiff;
}


// TODO: EVENTUALLY PUT THESE IN A DATABASE
function setupStrategies()
{
    const strats = {};
    
    strats["A"] = {
        "NH": 0,
        "NV": 1,
        "CO": 1,
        "MN": 1,
        "WI": 1,
        "AZ": 1,
        "VA": 2,
        "NC": 3,
        "MI": 3,
        "GA": 4,
        "PA": 4,
        "FL": 5
    }
    
    strats["B"] = {
        "NH": 1,
        "NV": 1,
        "CO": 1,
        "MN": 1,
        "WI": 2,
        "AZ": 2,
        "VA": 2,
        "NC": 2,
        "MI": 2,
        "GA": 2,
        "PA": 3,
        "FL": 5
    }
    
    strats["C"] = {
        "NH": 2,
        "NV": 2,
        "CO": 2,
        "MN": 2,
        "WI": 2,
        "AZ": 2,
        "VA": 2,
        "NC": 2,
        "MI": 2,
        "GA": 2,
        "PA": 2,
        "FL": 2
    }
    
    strats["D"] = {
        "NH": 4,
        "NV": 4,
        "CO": 3,
        "MN": 3,
        "WI": 2,
        "AZ": 1,
        "VA": 1,
        "NC": 1,
        "MI": 1,
        "GA": 1,
        "PA": 1,
        "FL": 1
    }
    
    return strats;
}

function fakeData()
{
    return [
                {
                    "id": 3069,
                    "player_id": 242,
                    "game_id": 186,
                    "state_id": 28,
                    "num_pieces": 1,
                    "state_abbrev": "NV"
                },
                {
                    "id": 3070,
                    "player_id": 242,
                    "game_id": 186,
                    "state_id": 3,
                    "num_pieces": 5,
                    "state_abbrev": "AZ"
                },
                {
                    "id": 3071,
                    "player_id": 242,
                    "game_id": 186,
                    "state_id": 6,
                    "num_pieces": 5,
                    "state_abbrev": "CO"
                },
                {
                    "id": 3072,
                    "player_id": 242,
                    "game_id": 186,
                    "state_id": 23,
                    "num_pieces": 5,
                    "state_abbrev": "MN"
                },
                {
                    "id": 3073,
                    "player_id": 242,
                    "game_id": 186,
                    "state_id": 49,
                    "num_pieces": 4,
                    "state_abbrev": "WI"
                },
                {
                    "id": 3074,
                    "player_id": 242,
                    "game_id": 186,
                    "state_id": 22,
                    "num_pieces": 1,
                    "state_abbrev": "MI"
                },
                {
                    "id": 3075,
                    "player_id": 242,
                    "game_id": 186,
                    "state_id": 9,
                    "num_pieces": 0,
                    "state_abbrev": "FL"
                },
                {
                    "id": 3076,
                    "player_id": 242,
                    "game_id": 186,
                    "state_id": 10,
                    "num_pieces": 1,
                    "state_abbrev": "GA"
                },
                {
                    "id": 3077,
                    "player_id": 242,
                    "game_id": 186,
                    "state_id": 33,
                    "num_pieces": 0,
                    "state_abbrev": "NC"
                },
                {
                    "id": 3078,
                    "player_id": 242,
                    "game_id": 186,
                    "state_id": 46,
                    "num_pieces": 1,
                    "state_abbrev": "VA"
                },
                {
                    "id": 3079,
                    "player_id": 242,
                    "game_id": 186,
                    "state_id": 38,
                    "num_pieces": 1,
                    "state_abbrev": "PA"
                },
                {
                    "id": 3080,
                    "player_id": 242,
                    "game_id": 186,
                    "state_id": 29,
                    "num_pieces": 0,
                    "state_abbrev": "NH"
                }
            ];
}