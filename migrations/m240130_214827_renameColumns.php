<?php

use yii\db\Migration;

/**
 * Class m240130_214827_renameColumnTask
 */
class m240130_214827_renameColumns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('task', 'category', 'category_id');
        $this->renameColumn('task', 'task_status', 'taskStatus_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240130_214827_renameColumns cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240130_214827_renameColumns cannot be reverted.\n";

        return false;
    }
    */
}
