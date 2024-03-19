<?php

use yii\db\Migration;

/**
 * Class m240312_075404_createOAuth2
 */
class m240312_075404_createOAuth2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('auth', [
            'user_id' => $this->integer(),
            'source' => $this->string(255),
            'source_id' => $this->string(255)
        ]);

        $this->addForeignKey('user_id', 'Auth', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240312_075404_createOAuth2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240312_075404_createOAuth2 cannot be reverted.\n";

        return false;
    }
    */
}
