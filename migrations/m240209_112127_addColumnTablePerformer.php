<?php

use yii\db\Migration;

/**
 * Class m240209_112127_addColumnTablePerformer
 */
class m240209_112127_addColumnTablePerformer extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('performer', 'finishedTasks', 'int');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240209_112127_addColumnTablePerformer cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240209_112127_addColumnTablePerformer cannot be reverted.\n";

        return false;
    }
    */
}
