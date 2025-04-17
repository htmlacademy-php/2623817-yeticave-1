<?php

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

require 'vendor/autoload.php';
require 'emailConfig.php'; // содержит одну строчку define("EMAIL_DSN" , 'smtp://...');

//Получение данных страницы
$mysqlConnection = db_get_connection();
if (!$mysqlConnection) {
    http_response_code(500); // переделано, чтобы возвращало ошибку
    exit();
}

//Список лотов{
$dbLotsList = db_get_lots_to_set_winner_list($mysqlConnection);
$lotsList = [];
for ($rowIndex = 0; $rowIndex < count($dbLotsList); $rowIndex++) {
    $lotsList[$dbLotsList[$rowIndex]['lot_id']] = [
        'winner_id' => $dbLotsList[$rowIndex]['winner_id'],
        'lot_name' => $dbLotsList[$rowIndex]['lot_name']];
}
//}Список лотов

foreach ($lotsList as $lotId => $lot_data) {
    $queryParam = db_get_set_lot_winner_params($lotId, $lot_data['winner_id']);
    $queryResult = db_set_lot_winner($mysqlConnection, $queryParam);
    if ($queryResult) {
        $dbUserList = db_get_user_by_id($mysqlConnection, [$lot_data['winner_id']]);
        if (count($dbUserList) > 0) {
            //Отправить письмо
            $textData = [
                'lotUrl' => $_SERVER['HTTP_HOST'].'/lot.php?id='.$lotId,
                'myBetsUrl' => $_SERVER['HTTP_HOST'].'/my-bets.php',
                'lotName' => $lot_data['lot_name'],
                'userName' => $dbUserList[0]['name']   
            ];
            $textHTML = include_template('email.php',$textData);
            send_email('keks@phpdemo.ru', $dbUserList[0]['email'], "Ваша ставка победила", $textHTML);
        }
        
        
    }
}

db_close_connection($mysqlConnection);


function send_email(string $from, string $to, string $subject, string $text): bool
{

    // Конфигурация траспорта
    $dsn = EMAIL_DSN;
    $transport = Transport::fromDsn($dsn);

    // Формирование сообщения
    $message = new Email();
    $message->to($to);
    $message->from($from);
    $message->subject($subject);
    $message->html($text);

    // Отправка сообщения
    $mailer = new Mailer($transport);
    try {
        $mailer->send($message);
    } catch (Exception $e) {
        //игнорируем
        return false;
    }
    ;

    return true;

}

?>