<?php

use yii\db\Migration;

/**
 * Class m240229_160138_changeComments
 */
class m240229_160138_changeComments extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('comments_ibfk_2', 'comments');
        $this->addForeignKey('author', 'comments', 'author_id', 'task', 'client_id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240229_160138_changeComments cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240229_160138_changeComments cannot be reverted.\n";

        return false;
    }
    */
}
