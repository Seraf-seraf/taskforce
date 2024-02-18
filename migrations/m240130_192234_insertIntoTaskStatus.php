<?php

use yii\db\Migration;

/**
 * Class m240130_192234_insertIntoTaskStatus
 */
class m240130_192234_insertIntoTaskStatus extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $values = ['new', 'canceled', 'proceed', 'complete', 'expired'];

        foreach ($values as $value) {
            $this->insert('taskStatus', [
                'name' => $value
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240130_192234_insertIntoTaskStatus cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240130_192234_insertIntoTaskStatus cannot be reverted.\n";

        return false;
    }
    */
}
