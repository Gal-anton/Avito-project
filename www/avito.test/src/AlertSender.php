<?php


class AlertSender
{
    public function send($name, $email, $product, $price) {
        $to      = $email;
        $subject = "Обновление цены";
        $message = ' <p>Уважаемый, ' . $name . '!</p> </br>' .
                    '<b>Вы запршивали оповещение об изменениях цены объявления ' . $product .  '</b></br>' .
                    '<i>Актуальная стоимость: ' . $price . ' </i> </br>';
        $headers  = "Content-type: text/html; charset=utf-8 \r\n";
        $headers .= "From: AVITO ALERT <avito.test.galichin@gmail.com>\r\n";
        $headers .= "Reply-To: avito.test.galichin@gmail.com\r\n";

        mail($to, $subject, $message, $headers);
    }


}