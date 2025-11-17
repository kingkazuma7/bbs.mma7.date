document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('anonymous-name');
    if (nameInput.value === '') {
        nameInput.value = '匿名-' + Math.random().toString(36).substring(2, 10);
    }
});
