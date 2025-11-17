document.addEventListener('DOMContentLoaded', function() {
    initAnonymousNameField();
});

function initAnonymousNameField() {
    const nameInput = document.getElementById('anonymous-name');
    if (nameInput && nameInput.value === '') {
        nameInput.value = '匿名-' + Math.random().toString(36).substring(2, 10);
    }
}
