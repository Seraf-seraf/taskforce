<?php

use yii\db\Migration;

/**
 * Class m240212_155632_addPrimaryKeyInUserSettings
 */
class m240212_155632_addPrimaryKeyInUserSettings extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            'userSettings',
            'id',
            $this->integer()->notNull()
        );

        $this->addPrimaryKey(
            'settings_id',
            'userSettings',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240212_155632_addPrimaryKeyInUserSettings cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240212_155632_addPrimaryKeyInUserSettings cannot be reverted.\n";

        return false;
    }
    */
}
