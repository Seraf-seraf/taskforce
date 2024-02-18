<?php

use yii\db\Migration;

/**
 * Class m240201_141945_addColumnInTableRating
 */
class m240201_141945_addColumnInTableRating extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('rating', 'failedTasks', 'int');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240201_141945_addColumnInTableRating cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240201_141945_addColumnInTableRating cannot be reverted.\n";

        return false;
    }
    */
}
