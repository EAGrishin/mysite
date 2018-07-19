<?php

namespace app\models;


/**
 * This is the model class for table "payment".
 *
 * @property int $id
 * @property int $payer_user_id
 * @property int $payee_user_id
 * @property int $cost
 * @property int $status
 * @property string $date_payment
 *
 * @property User $payerUser
 */
class Payment extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 0;
    const STATUS_APPROVED = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payer_user_id', 'payee_user_id', 'cost', 'date_payment'], 'required'],
            [['payer_user_id', 'payee_user_id', 'status'], 'integer'],
            [['status'], 'in', 'range' => [Payment::STATUS_NEW, Payment::STATUS_APPROVED]],
            [['payee_user_id'], 'compare', 'compareAttribute'=>'payer_user_id', 'operator' => '!=', 'message' => 'Выберите другого пользователя'],
            [['cost'], 'integer', 'message' => 'Сумма должна быть целым числом'],
            [['cost'], 'balanceValidation'],
            [['date_payment'], 'date', 'format' => 'php:Y-m-d H:i:s',
                'min' => (new \DateTime('now', new \DateTimeZone('Asia/Novosibirsk')))->format('Y-m-d H:i:s')],
            [['payer_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(),
                'targetAttribute' => ['payer_user_id' => 'id'], 'message' => 'Указанный пользователь не существует'],
            [['payee_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(),
                'targetAttribute' => ['payee_user_id' => 'id'], 'message' => 'Указанный пользователь не существует'],
        ];
    }

    public function balanceValidation($attribute, $params)
    {
        if (($user = User::findOne($this->payer_user_id)) !== null) {
            if ($user->balance < $this->$attribute) {
                $this->addError($attribute, 'Сумма перевода не должна превышать баланс пользователя');
                return;
            }
        }
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'payer_user_id' => 'Плательщик (User ID)',
            'payee_user_id' => 'Получатель платежа (User ID)',
            'cost' => 'Сумма перевода, руб.',
            'status' => 'Статус перевода',
            'date_payment' => 'Время перевода',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayerUser()
    {
        return $this->hasOne(User::className(), ['id' => 'payer_user_id']);
    }
}
