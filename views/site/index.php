<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\User;
use app\models\Payment;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = 'My Yii Application';
?>
<div class="site-index">
    <div class="body-content">
        <div class="row">
            <div class="col-lg-4">
                <div class="well">
                    <?php $form = ActiveForm::begin(['id' => 'payment-form']); ?>

                    <?= $form->field($model, 'payer_user_id') ?>

                    <?= $form->field($model, 'payee_user_id') ?>

                    <?= $form->field($model, 'cost') ?>

                    <?= $form->field($model, 'date_payment')->widget(\yii\jui\DatePicker::className(), [
                        'dateFormat' => 'yyyy-MM-dd 00:00:00',
                        'options' => ['class' => 'form-control'],
                    ])->label('Дата и время перевода'); ?>


                    <div class="form-group">
                        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
            <div class="col-lg-7">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'filterSelector' => 'select[name="per-page"]',
                    'tableOptions' => ['class' => 'table table-bordered table-stripped', 'style' => 'margin-top:5px;'],
                    'columns' => [
                        'id',
                        'username',
                        'balance',
                        'created_at',
                        [
                            'label' => 'Последний перевод',
                            'format' => 'raw',
                            'value' => function (User $data) {
                                /** @var User $user */
                                $out = '';
                                if ($data->lastPayment) {
                                    $out .= $data->lastPayment->cost . " руб. <br>";
                                    if ($data->lastPayment->status == Payment::STATUS_NEW) {
                                        $status = "Ожидает перевода";
                                    } else {
                                        $status = Html::tag('span', 'Превод выполнен', ['style' => 'color: #3e8f3e']);
                                    }
                                    $out .= $status . "<br> UserId (" . $data->lastPayment->payee_user_id . ") <br>"
                                        . $data->lastPayment->date_payment;
                                }
                                return $out;
                            }
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
