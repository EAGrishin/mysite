<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Payment;
use app\models\User;
use yii\log\Logger;

class CronController extends Controller
{

    public function actionIndex()
    {
        $payments = Payment::find()->where([
            'status' => Payment::STATUS_NEW,
        ])->andWhere('date_payment <= CURRENT_TIMESTAMP + INTERVAL 25 MINUTE')->all();

        /** @var Payment $payment */
        foreach ($payments as $payment) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $user = User::findOne($payment->payee_user_id);
                $user->balance += $payment->cost;
                $payment->status = Payment::STATUS_APPROVED;
                $payment->save(false, ['status']);
                $user->save(false, ['balance']);
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR);
            }
        }
    }

}