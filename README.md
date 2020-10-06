# Avito-project
Автор: Галичин Антон <br />
Email: galichin-anton@yandex.ru

Сервис подписки на объявления цены

Сервис продоставляет веб интерфейс для подписки на определенное объявление заданное URL.

Сервис развертывется с помощью docker-compose.

Сервис работает в связке php-fpm + nginx + mysql + PHPMyAdmin.

Для корректной работы приложения необходимо изменить строчку в hosts файле:

### 127.0.0.1 avito.test

Во время запуска контейнеров будет автоматически создана база данных для работы сервиса.

### docker-compose up --build
## Описание работы приложения
Пользователь вводит свое имя(необязательно), email и URL объявления, на который он хочет подписаться.

Далее ему отправляется письмо о том, что подписка оформлена успешно, а также оно содежит актуальную цену из объявления.

Пользователь и ID объявления помещаются в базу данных для хранения информации о подписках.

Каждые 30 минут идет проверка изменения цены в объявлениях и оповещение пользователей. Алгоритм работы следующий

1. Подгружаются все объявления, за которые подписывались пользователи;
2. Проверяется отклонение сохраненной цены и актуальной;
3. В случае несоответствия запись в базе данных обновляется, осуществляется выборка пользователей, 
которые подписаны на это объявление и им отправляется уведомление об изменении цены.

Также доступна регистрация по ссылке: 
#### http://avito.test:8000/scripts/register.php?email= YOUR_EMAIL&url=YOUR_URL

## Примеры кода
### Подписка на изменение цены

```php
<?php
$email = (isset($_GET['email']) === true) ? htmlspecialchars($_GET['email']) : ""; //получаем данные
$url   = (isset($_GET['url']) === true) ? htmlspecialchars($_GET['url']) : "";

if (empty(trim($email)) === false && //проверяем на пустые строки
    empty(trim($url))   === false) {

    $record = new RecordMonitor(null, $email, $url); // отправляем обработчику данные
    $record->save(); // сохраняем введенные данные

//отправка первого уведомления об успешной подписке
    $id_product = $record->getIdProductFromUrl(); 
    $price = $record->getPriceByUrl($url);
    $sender = new AlertSender();
    $sender->send($email, $id_product, $price, null, true);
}
echo json_encode(array("status" => true)); //отправялем ответ клиенту
?>
```

### Отслеживание изменений цены

```php
<?php 
//Не получилось определить как формируется ключ, однако был актуален несколько дней пока тестировался.

define('KEY_AVITO', 'af0deccbgcgidddjgnvljitntccdduijhdinfgjgfjir');
$path = 'https://m.avito.ru/api/1/rmp/show/' . $id .   //формируем запрос к серверу, включающий ID объявления и ключ
                '?key=' . KEY_AVITO;
                //отправляем запрос серверу
  $ch = curl_init();
  $timeout = 5;
  curl_setopt ($ch, CURLOPT_URL, $path);
  curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  $response = curl_exec($ch);
  curl_close($ch);

  if ($response !== false) { // если пришел ответ то парсим его как json объект(сразу приводим его к массиву для возможности обработки)
      $response = (array) json_decode($response);
      $result = (array)$response["result"];
      $dfpTargetings = (array)$result["dfpTargetings"];
      return $dfpTargetings["par_price"]; // возвращаем актуальную цену
  }
return false;
            
            
?>
```

### Отправка уведомления на почту
```php
<?php 
$recordMonitor = new RecordMonitor();   //Создаем экземпляр класса обработчика записей
$recordMonitor->createDBConnection();   //Открываем соединение с БД
$products = $recordMonitor->getAllProducts();  //Получаем массив всех объявлений
foreach ($products as $product) {                                                 
    $price = (string)$recordMonitor->getPriceById($product["id_from_url"]);  //Получаем актуальную цену
    
    if ($price != $product["price"]) { // в случае отклонения цены от записанной в БД:
    // обновляем запись в БД
        $recordMonitor->updateProduct($product["id_product"], $product["id_from_url"], $price); 
        $clients = $recordMonitor->getSubscribedClient($product["id_from_url"]); //Получаем всех 
            //пользователей которые следят за этим объявлением
        
        foreach ($clients as $client) { //Каждому отправляем письмо
            AlertSender::send($client['email'], $client["id_from_url"], $price, $client['name']); 
        }
    }
}
?>
```

### Работа с БД (Представлен неизмененный вариант метода сохранения объявления в БД)
```php
<?php 
/**
 * Save product into database
 * @return mixed
 */
private function _saveProduct()
{
//Получаем актуальную цену
    $price  = $this->getPriceById($this->_idProductFromUrl);
//Выполняем запрос к БД
    $result = $this->_link->query("INSERT INTO `Product` (`id_from_url`, `price`) VALUE (" .
                                $this->_sqlStr($this->_idProductFromUrl) . "," .
                                $this->_sqlStr($price) . ")");
//Получаем id последней сохраненной записи
    $id = $this->_link->insert_id;
    if ($result === false) { // если это объявление уже было загружено
//то загружем его ID
        $id = $this->_link->query("SELECT `id_product` FROM `Product` WHERE " .
                                            "`id_from_url` = " . $this->_sqlStr($this->_idProductFromUrl));
        $row = $id->fetch_row();
        $id = array_pop($row);
    }
//Выводим ID
    return $id;
}
?>
```


## Для изменения отправителя нужно править файл /images/php/msmtprs
