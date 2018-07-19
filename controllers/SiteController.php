<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Payment;
use yii\web\Controller;
use app\models\UserSearch;
use yii\log\Logger;
use yii\web\ServerErrorHttpException;

class SiteController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new Payment();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $user = User::findOne($model->payer_user_id);
                $user->balance -= $model->cost;
                if ($model->save()) {
                    $user->last_payment_id = $model->id;
                    $user->save();
                }
                $transaction->commit();
                Yii::$app->session->setFlash('success', '✔ Данная сумма списалась с баланса, перевод выполнится в указанное время');
                return $this->refresh();
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR);
                throw new ServerErrorHttpException();
            }

        }
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort->defaultOrder = [
            'id' => SORT_DESC,
        ];

        return $this->render('index', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

}
