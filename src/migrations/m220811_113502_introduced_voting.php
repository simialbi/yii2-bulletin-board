<?php
/**
 * @package yii2-bulletin-board
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\bulletin\migrations;

use yii\db\Migration;

class m220811_113502_introduced_voting extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%bulletin__voting}}', [
            'id' => $this->primaryKey()->unsigned(),
            'topic_id' => $this->integer()->unsigned()->notNull(),
            'question' => $this->string(255)->notNull(),
            'multiple_answers_allowed' => $this->boolean()->notNull()->defaultValue(0),
            'created_by' => $this->string(64)->null()->defaultValue(null),
            'updated_by' => $this->string(64)->null()->defaultValue(null),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull()
        ]);
        $this->createTable('{{%bulletin__voting_answer}}', [
            'id' => $this->primaryKey()->unsigned(),
            'voting_id' => $this->integer()->unsigned()->notNull(),
            'answer' => $this->string(255)->notNull(),
            'created_by' => $this->string(64)->null()->defaultValue(null),
            'updated_by' => $this->string(64)->null()->defaultValue(null),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull()
        ]);
        $this->createTable('{{%bulletin__voting_user_answer}}', [
            'id' => $this->primaryKey()->unsigned(),
            'answer_id' => $this->integer()->unsigned()->notNull(),
            'user_id' => $this->string(64)->notNull(),
            'created_at' => $this->integer()->unsigned()->notNull()
        ]);
        $this->addColumn(
            '{{%bulletin__topic}}',
            'has_voting',
            $this->boolean()->notNull()->defaultValue(0)->after('title')
        );

        $this->addForeignKey(
            '{{%bulletin__voting_ibfk_1}}',
            '{{%bulletin__voting}}',
            'topic_id',
            '{{%bulletin__topic}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{%bulletin__voting_answer_ibfk_1}}',
            '{{%bulletin__voting_answer}}',
            'voting_id',
            '{{%bulletin__voting}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{%bulletin__voting_user_answer_ibfk_1}}',
            '{{%bulletin__voting_user_answer}}',
            'answer_id',
            '{{%bulletin__voting_answer}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritDoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%bulletin__voting_ibfk_1}}', '{{%bulletin__voting}}');
        $this->dropForeignKey('{{%bulletin__voting_answer_ibfk_1}}', '{{%bulletin__voting_answer}}');
        $this->dropForeignKey('{{%bulletin__voting_user_answer_ibfk_1}}', '{{%bulletin__voting_user_answer}}');

        $this->dropTable('{{%bulletin__voting}}');
        $this->dropTable('{{%bulletin__voting_answer}}');
        $this->dropTable('{{%bulletin__voting_user_answer}}');

        $this->dropColumn('{{%bulletin__topic}}', 'has_voting');
    }
}
