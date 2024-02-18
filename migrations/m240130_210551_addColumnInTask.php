<?php

use yii\db\Migration;

/**
 * Class m240130_210551_addColumnInTask
 */
class m240130_210551_addColumnInTask extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('task', 'description', 'varchar(128)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240130_210551_addColumnInTask cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240130_210551_addColumnInTask cannot be reverted.\n";

        return false;
    }
    */
}
