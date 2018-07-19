<?php

namespace app\commands;

use yii\console\Controller;
use app\models\Payment;
use app\models\User;

class CronController extends Controller
{

    public function actionPayment()
    {
        $payments = Payment::find()->where([
            'status' => Payment::STATUS_NEW,
        ])->andWhere('date_payment <= CURRENT_TIMESTAMP + INTERVAL 25 MINUTE')->all();

        /** @var Payment $payment */
        foreach ($payments as $payment) {
            $user = User::findOne($payment->payee_user_id);
            $user->balance += $payment->cost;
            $payment->status = Payment::STATUS_APPROVED;
            $payment->save(false, ['status']);
            $user->save(false, ['balance']);
        }
    }

}