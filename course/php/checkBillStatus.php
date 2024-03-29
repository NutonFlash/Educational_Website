<?php

use Qiwi\Api\BillPayments;
use FaaPz\PDO\Clause\Conditional;
include_once ('DB_Manager.php');
require_once '../vendor/autoload.php';

$PRIVATE_KEY = 'key';

$signFromServer = getallheaders()['X-Api-Signature-SHA256'];
$data = json_decode(file_get_contents('php://input'), true);
$bill = $data['bill'];
$notificationData = [
    'bill' => [
        'siteId' => $bill['siteId'],
        'billId' => $bill['billId'],
        'amount' => ['value' => $bill['amount']['value'], 'currency' => $bill['amount']['currency']],
        'status' => ['value' => $bill['status']['value']]
    ],
];
$billPayments = new BillPayments($PRIVATE_KEY);
if ($billPayments->checkNotificationSignature($signFromServer,$notificationData, $PRIVATE_KEY)) {
    $dbManager = new DB_Manager();
    $db = $dbManager->connectDB();
    if ($bill['status']['value'] === 'PAID') {
        $db->update(['hasAccess' => 'true'])->table('users')->where(new Conditional('email', '=', $bill['customer']['email']))->execute();
        $db->update(['status' => 'PAID'])->table('payments')->where(new Conditional('id','=',$bill['billId']))->execute();
        $ref_code = $db->select(['reference_code'])->from('payments')->where(new Conditional('id','=',$bill['billId']))->execute()->fetch(PDO::FETCH_ASSOC)['reference_code'];
        $cur_number_of_sales = $db->select(['number_of_sales'])->from('merchants')->where(new Conditional('reference_code','=',$ref_code))->execute()->fetch(PDO::FETCH_ASSOC)['number_of_sales'];
        $db->update(['number_of_sales' => $cur_number_of_sales++])->table('merchants')->where(new Conditional('reference_code','=',$ref_code))->execute();
    }
    else
        $db->update(['status' => $bill['status']['value']])->table('payments')->where(new Conditional('id','=',$bill['billId']))->execute();
}

