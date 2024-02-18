<?php

use yii\db\Migration;

/**
 * Class m240131_170105_addColumnDateCreateTask
 */
class m240131_170105_addColumnDateCreateTask extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('task', 'dateCreate', 'datetime');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240131_170105_addColumnDateCreateTask cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240131_170105_addColumnDateCreateTask cannot be reverted.\n";

        return false;
    }
    */
}
