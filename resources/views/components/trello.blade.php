<!DOCTYPE html>
<html>
<head>
    <title>Authorization Successful</title>
    <script>
        window.onload = function () {

            const fragment = window.location.hash.substring(1);
            const params = new URLSearchParams(fragment);


            const token = params.get('token');


            const queryParams = new URLSearchParams(window.location.search);
            const state = queryParams.get('state');


            console.log({ token, state })
            if (token) {

                fetch('/trello/auth_complete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ token, state }),
                })
                    .then(data => {
                        console.log('Success:', data.ok);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            } else {
                console.error('Token not found.');
            }
        };
    </script>
</head>
<body>
<h1>Авторизація успішна!</h1>
<p>Ваш акаунт Trello успішно підключено. Це вікно можна закрити.</p>
</body>
</html>
