const mask = document.querySelector('#modal-mask');
const dialog = document.querySelector('#modal-dialog');
const messageElement = document.querySelector('#modal-message');
const cancel = document.querySelector('#modal-cancel');
const ok = document.querySelector('#modal-ok');

let callback;

function onCancel() {
    mask.classList.add('hidden');
    dialog.classList.add('hidden');
    if (callback) callback(false);
    cancel.removeEventListener('click', onCancel);
    ok.removeEventListener('click', onOk);
}

function onOk() {
    mask.classList.add('hidden');
    dialog.classList.add('hidden');
    if (callback) callback(true);
    cancel.removeEventListener('click', onCancel);
    ok.removeEventListener('click', onOk);
}

function showModal(type, message, cb) {
    if (!mask || !dialog || !messageElement || !cancel || !ok) {
        throw new Error('Modal elements not found!');
    }

    callback = cb;

    mask.classList.remove('hidden');
    dialog.classList.remove('hidden');
    messageElement.textContent = message;

    if (type === 'confirm') {
        cancel.classList.remove('hidden');
    } else {
        cancel.classList.add('hidden');
    }

    cancel.addEventListener('click', function (event) {
        event.stopPropagation();
        onCancel();
    });

    ok.addEventListener('click', function (event) {
        event.stopPropagation();
        onOk();
    });
}


export function customAlert(message, cb) {
    showModal('alert', message, function (result) {
        if (cb)
        {
            cb();
        }
    });
}

export function customConfirm(message, cb) {
    showModal('confirm', message, function (result) {
        if (cb)
        {
            cb(result);
        }
    });
}
