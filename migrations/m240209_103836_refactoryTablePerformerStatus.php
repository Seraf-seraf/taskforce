<?php

use yii\db\Migration;

/**
 * Class m240209_103836_refactoryTablePerformerStatus
 */
class m240209_103836_refactoryTablePerformerStatus extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('performerStatus');
        $this->createTable('performer', [
            'performer_id' => 'int',
            'status_id'    => 'int',
        ]);

        $this->addForeignKey(
            'performer_index',
            'performer',
            'performer_id',
            'user',
            'id'
        );

        $this->createTable(
            'performerStatus',
            [
                'id'   => $this->primaryKey(),
                'name' => 'varchar(64)',
            ]
        );

        $this->addForeignKey(
            'status_id',
            'performer',
            'status_id',
            'performerStatus',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240209_103836_refactoryTablePerformerStatus cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240209_103836_refactoryTablePerformerStatus cannot be reverted.\n";

        return false;
    }
    */
}
