<?php

use yii\db\Migration;

/**
 * Class m240308_190636_addRowCity
 */
class m240308_190636_addRowCity extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('city', ['name' => 'Москва', 'lat' => 55.7522, 'long' => 37.6156]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240308_190636_addRowCity cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240308_190636_addRowCity cannot be reverted.\n";

        return false;
    }
    */
}
