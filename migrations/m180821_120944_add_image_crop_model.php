<?php

use yii\db\Migration;

/**
 * Class m180821_120944_add_image_crop_model
 */
class m180821_120944_add_image_crop_model extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('image_crop', [
            'id' => $this->primaryKey()->unsigned(),
            'image_id' => $this->integer()->unsigned()->notNull(),
            'aspect_width' => $this->integer()->unsigned()->notNull(),
            'aspect_height' => $this->integer()->unsigned()->notNull(),
            'x' => $this->integer()->unsigned()->notNull(),
            'y' => $this->integer()->unsigned()->notNull(),
            'width' => $this->integer()->unsigned()->notNull(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8');

        $this->createIndex(
            'image_id_aspect_width_aspect_height',
            'image_crop',
            ['image_id', 'aspect_width', 'aspect_height'],
            true
        );
        $this->addForeignKey(
            'image_crop_image_id',
            'image_crop',
            ['image_id'],
            'crud_media',
            ['id'],
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('image_crop_image_id', 'image_crop');
        $this->dropTable('image_crop');
    }
}
