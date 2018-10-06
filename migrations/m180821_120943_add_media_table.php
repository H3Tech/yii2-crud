<?php

use yii\db\Migration;

/**
 * Class m181005_211704_add_media_table
 */
class m180821_120943_add_media_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('crud_media',  [
            'id' => $this->primaryKey()->unsigned(),
            'type' => $this->string(25)->notNull(),
            'filename' => $this->string(255)->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('crud_media');
    }
}
