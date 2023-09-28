import * as common from "./game_common.js";
import {resetTutorials} from "./game_tutorials.js";

export let activeTabIndex = 0;
export let tabsList = [];

export function initTabs(container) {
    const tabs = container.querySelectorAll(".tab-link");
    tabsList = Array.from(tabs); // Convert NodeList to an array.
    tabs.forEach(function(tab, index) {
        tab.addEventListener('click', function() {
            openTutorial(this, container);
            activeTabIndex = index; // Store the current tab index.
        });
    });
}

export function openTutorial(tab, container) {
    resetTutorials();
    activeTabIndex = tabsList.indexOf(tab);
    // Hide all tab contents
    var tabContents = container.querySelectorAll(".tabcontent");
    tabContents.forEach(common.hide);

    // Remove the "current-tab" class of all tab links/buttons
    var tabLinks = container.querySelectorAll(".tab-link");
    tabLinks.forEach(function(link) {
        link.classList.remove("current-tab");
    });

    // Show the specific tab content
    var contentId = tab.getAttribute('data-tab-content');
    common.show(container.querySelector("#" + contentId));

    // Add the "current-tab" class to the button used to open the tab content
    tab.classList.add("current-tab");
    tab.dispatchEvent(new Event('tabOpened', { bubbles: true }));
}

export function hasNextTab() {
    return activeTabIndex < tabsList.length - 1;
}

export function hasPreviousTab() {
    return activeTabIndex > 0;
}

export function isOnLastTab() {
    return activeTabIndex === tabsList.length - 1;
}

export function isOnFirstTab() {
    return activeTabIndex === 0;
}
