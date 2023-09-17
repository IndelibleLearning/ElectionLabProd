import * as common from "./game_common.js";

export function initTabs(container) {
    var tabs = container.querySelectorAll(".tab-link");
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            openTutorial(this, container);
        });
    });
}

export function openTutorial(tab, container) {
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
}
