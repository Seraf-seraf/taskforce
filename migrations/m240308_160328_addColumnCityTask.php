<?php

use yii\db\Migration;

/**
 * Class m240308_160328_addColumnCityTask
 */
class m240308_160328_addColumnCityTask extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('task', 'city_id', $this->integer());
        $this->addForeignKey('city_id', 'task', 'city_id', 'city', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240308_160328_addColumnCityTask cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240308_160328_addColumnCityTask cannot be reverted.\n";

        return false;
    }
    */
}
