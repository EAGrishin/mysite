<?php

namespace app\models;


use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property int $balance
 * @property int $last_payment_id
 * @property string $created_at
 *
 * @property Payment[] $payments
 * @property Payment $lastPayment
 */
class User extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }


    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username'], 'required', 'message' => 'Необходимо указать Имя'],
            [['balance'], 'required', 'message' => 'Необходимо указать Баланс'],
            [['balance'], 'integer', 'message' => 'Неверный формат баланса'],
            [['created_at', 'last_payment_id'], 'safe'],
            [['username'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Имя',
            'balance' => 'Баланс, руб.',
            'created_at' => 'Дата создания',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayments()
    {
        return $this->hasMany(Payment::className(), ['payer_user_id' => 'id'])->orderBy(['payment.id' => SORT_DESC]);
    }

    public function getLastPayment()
    {
        return $this->hasOne(Payment::className(), ['id' => 'last_payment_id']);
    }
}
