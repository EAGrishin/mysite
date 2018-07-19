<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m180718_062337_create_tables
 */
class m180718_062337_create_tables extends Migration
{
    public function up()
    {
        $this->createTable('{{%user}}', [
            'id' => Schema::TYPE_PK,
            'username' => Schema::TYPE_STRING . ' NOT NULL',
            'balance' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            'last_payment_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
        ]);

        $names = ['Alexander', 'Mike', 'Ivan', 'Irina', 'Marina'];

        foreach ($names as $username) {
            $this->insert('{{%user}}', [
                'username' => $username,
                'balance' => 5000,
                'created_at' => new \yii\db\Expression('NOW()'),
            ]);
        }

        $this->createTable('{{%payment}}', [
            'id' => Schema::TYPE_PK,
            'payer_user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'payee_user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'cost' => Schema::TYPE_INTEGER . ' NOT NULL',
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
            'date_payment' => Schema::TYPE_DATETIME . ' NOT NULL',
        ]);

        $this->addForeignKey('FK_payment_payer_user', '{{%payment}}', 'payer_user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->createIndex('idx_payer_user_id', '{{%payment}}', 'payer_user_id');
    }

    public function down()
    {
        $this->dropTable('{{%payment}}');
        $this->dropTable('{{%user}}');
    }
}
