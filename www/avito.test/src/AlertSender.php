<?php


/**
 * Class AlertSender
 */
class AlertSender
{
    /**
     * @param string $email
     * @param string $product
     * @param string $price
     * @param string $name Name to send email
     * @param bool $firstAlert
     */
    public static function send(string $email, string $product, string $price, $name = "", bool $firstAlert = false) {
        $price = self::checkPrice($price);
        $name = self::checkName($name);
        $to      = $email;
        $subject = "Обновление цены";
        if ($firstAlert == false) {
            $message = ' <p>Здравствуйте' . $name . '!</p> </br>' .
                '<b>Вы запршивали оповещение об изменениях цены объявления ' . $product . '</b> </br>' .
                '<i>Актуальная стоимость: ' . $price . ' </i> </br>';
        } else {
            $message = ' <p>Здравствуйте' . $name . '!</p> </br>' .
                '<b>Вы успешно подписаны на изменения цены в объявлении ' . $product . '</b> </br>' .
                '<i>Актуальная стоимость: ' . $price . ' </i> </br>';
        }
        $headers  = "Content-type: text/html; charset=utf-8 \r\n";
        $headers .= "From: AVITO ALERT <avito.test.galichin@gmail.com>\r\n";
        $headers .= "Reply-To: avito.test.galichin@gmail.com\r\n";
        mail($to, $subject, $message, $headers);
    }

    /**
     * @param $price
     * @return string
     */
    private static function checkPrice($price)
    {
        return (is_null($price) === true) ? "Цена не указана" : $price;
    }


    private static function checkName($name)
    {
        return (empty($name) === true) ? "" : ", " . $name;
    }

}