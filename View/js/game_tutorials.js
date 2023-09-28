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

    gameTutorials.addEventListener('tutorialUpdated', (e) => {
        const currentTutorial = e.target;
        updateButtonVisibilityBasedOnTabContext(currentTutorial);
    });

}

export function resetTutorials()
{
    tutorial.resetTutorial(tutorialOverview);
    tutorial.resetTutorial(tutorialDeployments);
    tutorial.resetTutorial(tutorialDice);
}

export function showTutorial()
{
    resetTutorials();
    common.show(gameTutorials);
}

export function hideTutorials()
{
    resetTutorials();
    common.hide(gameTutorials);
}

function setupTutorialClose()
{
    tutorialCloseButton.addEventListener("click", () => {
        hideTutorials();
    });
}

function setupTabs() {
    tabs.initTabs(gameTutorials);

    // Update initially
    updateButtonVisibilityBasedOnTabContext(tutorialOverview);
    updateButtonVisibilityBasedOnTabContext(tutorialDeployments);
    updateButtonVisibilityBasedOnTabContext(tutorialDice);

    gameTutorials.addEventListener('goToPreviousTutorial', () => {
        if (tabs.hasPreviousTab()) {
            tabs.tabsList[tabs.activeTabIndex - 1].click();
            updateButtonVisibilityBasedOnTabContext(tutorialOverview);
            updateButtonVisibilityBasedOnTabContext(tutorialDeployments);
            updateButtonVisibilityBasedOnTabContext(tutorialDice);
        }
    });

    gameTutorials.addEventListener('goToNextTutorial', () => {
        if (tabs.hasNextTab()) {
            tabs.tabsList[tabs.activeTabIndex + 1].click();
            updateButtonVisibilityBasedOnTabContext(tutorialOverview);
            updateButtonVisibilityBasedOnTabContext(tutorialDeployments);
            updateButtonVisibilityBasedOnTabContext(tutorialDice);
        }
    });

    // Add an event listener for the custom event.
    gameTutorials.addEventListener('tabOpened', (e) => {
        const currentTutorial = document.querySelector('.tabcontent:not(.hidden)'); // Replace '.tutorial-class' with the appropriate class or identifier for your tutorial elements.
        updateButtonVisibilityBasedOnTabContext(currentTutorial);
    });
}

function updateButtonVisibilityBasedOnTabContext(currentTutorial) {
    if (!currentTutorial) {
        console.warn("No tutorial element provided.");
        return;
    }

    const currentIndex = tutorial.getCurrentIndex(currentTutorial);
    const showPrev = !(tabs.isOnFirstTab() && currentIndex === 1);
    const showNext = !(tabs.isOnLastTab() && currentIndex === parseInt(currentTutorial.dataset.maxIndex));

    tutorial.setButtonVisibility(currentTutorial, showPrev, showNext);
}

