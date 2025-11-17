document.addEventListener('DOMContentLoaded', function() {
    initAnonymousNameField();
    saveAnonymousNameOnSubmit();
});

function initAnonymousNameField() {
    const nameInput = document.getElementById('anonymous-name');
    if (!nameInput) return;

    const savedName = localStorage.getItem('last_anonymous_name');

    if (savedName) {
        nameInput.value = savedName;
    } else if (nameInput.value === '') {
        nameInput.value = '匿名-' + Math.random().toString(36).substring(2, 10);
    }
}

function saveAnonymousNameOnSubmit() {
    const form = document.querySelector('form[action*="/threads/"][action*="/posts"]');
    if (!form) return;

    form.addEventListener('submit', function() {
        const nameInput = document.getElementById('anonymous-name');
        if (nameInput && nameInput.value !== '') {
            localStorage.setItem('last_anonymous_name', nameInput.value);
        }
    });
}
