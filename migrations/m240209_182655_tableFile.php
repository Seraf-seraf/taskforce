<?php

use yii\db\Migration;

/**
 * Class m240209_182655_tableFile
 */
class m240209_182655_tableFile extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('file', [
            'task_id' => 'int',
            'name'    => 'varchar(128)',
            'path'    => 'varchar(128)',
        ]);

        $this->addForeignKey(
            'FK1',
            'file',
            'task_id',
            'task',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240209_182655_tableFile cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240209_182655_tableFile cannot be reverted.\n";

        return false;
    }
    */
}
