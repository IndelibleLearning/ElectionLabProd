import * as tutorial from "./tutorial.js";
import * as common from "./game_common.js"
import * as tabs from "./tabs.js"

const gameTutorials = document.querySelector("#game-tutorials");
const tutorialOverview = document.querySelector("#tutorial-overview");
const tutorialDeployments = document.querySelector("#tutorial-deployments");
const tutorialDice = document.querySelector("#tutorial-dice");
const tutorialCloseButton = document.querySelector("#close-tutorials");

export function setupTutorials() {
    tutorial.initializeTutorial(tutorialOverview);
    tutorial.initializeTutorial(tutorialDeployments);
    tutorial.initializeTutorial(tutorialDice);
    setupTutorialClose();
    setupTabs();
}

export function showTutorial()
{
    common.show(gameTutorials);
}

export function hideTutorials()
{
    common.hide(gameTutorials);
}

function setupTutorialClose()
{
    tutorialCloseButton.addEventListener("click", () => {
        hideTutorials();
    });
}

function setupTabs()
{
    tabs.initTabs(gameTutorials);
}