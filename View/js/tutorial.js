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

    var previousButton = tutorial.querySelector('.previous-button');
    var nextButton = tutorial.querySelector('.next-button');
    if (index === 1) {
        common.hide(previousButton);
    } else {
        common.show(previousButton);
    }
    if (index === parseInt(maxIndex)) {
        common.hide(nextButton);
    } else {
        common.show(nextButton);
    }
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


