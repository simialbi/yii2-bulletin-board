<?php
/**
 * @package yii2-bulletin-board
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\bulletin\migrations;

use yii\db\Migration;

class m220816_134735_add_cite_function extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%bulletin__post}}',
            'cite_id',
            $this->integer()->unsigned()->null()->defaultValue(null)->after('topic_id')
        );

        $this->addForeignKey(
            '{{%bulletin__post_ibfk_2}}',
            '{{%bulletin__post}}',
            'cite_id',
            '{{%bulletin__post}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritDoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%bulletin__post_ibfk_2}}', '{{%bulletin__post}}');

        $this->dropColumn('{{%bulletin__post}}', 'cite_id');
    }
}
