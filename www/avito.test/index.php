<?php
session_start();
require_once "src/RecordMonitor.php";
if (isset($_POST['send']) === true) {
    $name   = htmlspecialchars($_POST['name']);
    $email  = htmlspecialchars($_POST['email']);
    $url    = htmlspecialchars($_POST['url']);

    $record = new RecordMonitor($name, $email, $url);
    $record->save();

    $_SESSION['flash'] = 'Запись добавлена';
    // обновление страницы
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}
?>
<!doctype html>
<html lang="ru">
    <head>
        <title>Подписка</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
    </head>
    <body>
        <div class="container h-100">
            <div class="row h-100 justify-content-center align-items-center">
                <h2>Оформите подписку на обновление цены в объявлении.</h2>
                <?
                    if (empty($_SESSION['flash']) === false) {
                        print $_SESSION['flash'];
                        unset($_SESSION['flash']);
                    }
                ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Имя</label>
                        <input type="text" class="form-control" id="name" name="name" maxlength="50" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Адрес email</label>
                        <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" required>
                        <div id="emailHelp" class="form-text">Мы не передаем данные третьим лицам.</div>
                    </div>
                    <div class="mb-3">
                        <label for="url" class="form-label">Ссылка на объявление</label>
                        <input type="url" class="form-control" id="url" name="url" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="send">Подписаться</button>
                </form>
            </div>
        </div>
        <!-- Option 1: Bootstrap Bundle with Popper.js -->
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha2/js/bootstrap.bundle.min.js" integrity="sha384-BOsAfwzjNJHrJ8cZidOg56tcQWfp6y72vEJ8xQ9w6Quywb24iOsW913URv1IS4GD" crossorigin="anonymous"></script>
    </body>
</html>
