
function login()  {
    var username = document.getElementById('username').value;
    var password = document.getElementById('password').value;

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/login', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onload = function() {
        var response = JSON.parse(xhr.responseText);
        if (response.success) {
            document.getElementById('message').innerText = 'Успешная авторизация!';
            document.getElementById('username').value = '';
            document.getElementById('password').value = '';
        } else {
            document.getElementById('message').innerText = 'Ошибка: ' + response.message;
        }
    };
    xhr.onerror = function() {
        document.getElementById('message').innerText = 'Ошибка сети';
    };
    xhr.send(JSON.stringify({username: username, password: password}));
}