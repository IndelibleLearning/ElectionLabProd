const registeredDivs = new Set();

export function resizeFontToFit(div) {
    const step = 0.5;  // Adjust for faster/slower increments
    const safetyLimit = 100;  // To avoid infinite loops in edge cases

    if (!div) return;

    let count = 0;
    div.style.fontSize = "10px";  // Starting baseline. Adjust as necessary.

    while (!isOverflowing(div) && count < safetyLimit) {
        const currentSize = parseFloat(window.getComputedStyle(div, null).getPropertyValue('font-size'));
        div.style.fontSize = (currentSize + step) + "px";
        count++;
    }

    // Decrease font size once to correct the potential overflow
    const currentSize = parseFloat(window.getComputedStyle(div, null).getPropertyValue('font-size'));
    div.style.fontSize = (currentSize - step) + "px";
}

function isOverflowing(element) {
    return element.scrollHeight > element.clientHeight ||
        element.scrollWidth > element.clientWidth;
}

export function registerDivForResize(divOrId) {
    if (typeof divOrId === 'string') {
        const div = document.getElementById(divOrId);
        if (div) {
            registeredDivs.add(div);
            resizeFontToFit(div);
        }
    } else if (divOrId && divOrId.nodeType === 1) { // Check if it's an HTMLElement
        registeredDivs.add(divOrId);
        resizeFontToFit(divOrId);
    }
}

window.addEventListener('resize', function() {
    for (let div of registeredDivs) {
        resizeFontToFit(div);
    }
});