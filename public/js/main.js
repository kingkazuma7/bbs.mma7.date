document.addEventListener('DOMContentLoaded', function() {
    initAnonymousNameField();
    saveAnonymousNameOnSubmit();
    addReplyButtonListener();
    initSearchRecommendation();
});

function initSearchRecommendation() {
    const searchInput = document.getElementById('search_form');
    const searchRecommend = document.getElementById('search-recommendation');
    
    if (!searchInput || !searchRecommend) return;

    // フォームにフォーカスが当たったらおすすめを表示
    searchInput.addEventListener('focus', function() {
        searchRecommend.style.display = 'block';
    });
    
    // フォーム外をクリックしたらおすすめを非表示
    document.addEventListener('click', function(e) {
        // 検索ボタン（submit）自体をクリックした場合は非表示にしない（そのまま送信されるため）
        const searchBtn = document.getElementById('search_btn');
        if (!searchInput.contains(e.target) && !searchRecommend.contains(e.target) && (!searchBtn || !searchBtn.contains(e.target))) {
            searchRecommend.style.display = 'none';
        }
    });
}

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

function addReplyButtonListener() {
    document.querySelectorAll('.reply-button').forEach(button => {
        button.addEventListener('click', function() {
            const postName = this.dataset.postName;
            const messageTextarea = document.getElementById('post-message');
            if (messageTextarea) {
                messageTextarea.value = `>> ${postName}: ` + messageTextarea.value;
                messageTextarea.focus();
            }
        });
    });
}
