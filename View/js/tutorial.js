import * as common from "./game_common.js";
import {registerDivForResize, resizeFontToFit} from "./resizeFontToFit.js";
export function initializeTutorial(tutorial) {
    var breadcrumbs = tutorial.querySelectorAll('.breadcrumb');
    tutorial.dataset.maxIndex = breadcrumbs.length;

    tutorial.querySelector('.previous-button').addEventListener('click', function() {
        var currentIndex = parseInt(tutorial.querySelector('.navigation-section > .current').id.split('-')[1]);
        updateTutorial(tutorial, currentIndex - 1);
    });

    tutorial.querySelector('.next-button').addEventListener('click', function() {
        var currentIndex = parseInt(tutorial.querySelector('.navigation-section > .current').id.split('-')[1]);
        updateTutorial(tutorial, currentIndex + 1);
    });

    breadcrumbs.forEach(function(breadcrumb, index) {
        breadcrumb.addEventListener('click', function() {
            updateTutorial(tutorial, index + 1);
        });
    });

    const paragraphs = tutorial.querySelectorAll('.text-section  p');
    paragraphs.forEach(function(paragraph) {
        registerDivForResize(paragraph);
    });
}

function resizeParagraphs(tutorial)
{
    const paragraphs = tutorial.querySelectorAll('.text-section  p');
    paragraphs.forEach(function(paragraph) {
        resizeFontToFit(paragraph);
    });
}

function toggleParagraphsOpacity(tutorial, opacity)
{
    const paragraphs = tutorial.querySelectorAll('.text-section  p');
    paragraphs.forEach(function(paragraph) {
        paragraph.style.opacity = opacity;
    });
}

export function updateTutorial(tutorial, index) {
    var maxIndex = tutorial.dataset.maxIndex;
    // Check for out-of-bounds navigation for individual tutorials
    if (index < 1) {
        tutorial.dispatchEvent(new Event('goToPreviousTutorial', { bubbles: true }));
        return;
    } else if (index > maxIndex) {
        tutorial.dispatchEvent(new Event('goToNextTutorial', { bubbles: true }));
        return;
    }

    var currentImage = tutorial.querySelector('.image-section > :not(.hidden)');
    var currentText = tutorial.querySelector('.text-section > :not(.hidden)');
    common.hide(currentImage);
    common.hide(currentText);
    common.show(tutorial.querySelector('.image-' + index));
    common.show(tutorial.querySelector('.text-' + index));
    requestAnimationFrame(() => {
        toggleParagraphsOpacity(tutorial, 0);
        requestAnimationFrame(() => {
            resizeParagraphs(tutorial);
            requestAnimationFrame(() => {
                toggleParagraphsOpacity(tutorial, 1);
            });
        });
    });

    updateBreadcrumbs(tutorial.querySelector('.navigation-section'), 'step-' + index);

    tutorial.dispatchEvent(new Event('tutorialUpdated', { bubbles: true }));
}

export function resetTutorial(tutorial)
{
    updateTutorial(tutorial, 1);
}


function updateBreadcrumbs(navigationSection, currentStepId) {
    var breadcrumbs = navigationSection.querySelectorAll('.breadcrumb');
    breadcrumbs.forEach(function(breadcrumb) {
        breadcrumb.classList.remove('current');
    });
    var currentBreadcrumb = navigationSection.querySelector('#' + currentStepId);
    currentBreadcrumb.classList.add('current');
}

export function setButtonVisibility(tutorial, prevVisible, nextVisible) {
    const previousButton = tutorial.querySelector('.previous-button');
    const nextButton = tutorial.querySelector('.next-button');
    if (prevVisible) {
        common.show(previousButton);
    } else {
        common.hide(previousButton);
    }

    if (nextVisible) {
        common.show(nextButton);
    } else {
        common.hide(nextButton);
    }
}

export function getCurrentIndex(tutorial) {
    return parseInt(tutorial.querySelector('.navigation-section > .current').id.split('-')[1]);
}




