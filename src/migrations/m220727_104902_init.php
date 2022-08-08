<?php
/**
 * @package yii2-bulletin-board
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\bulletin\migrations;

use simialbi\yii2\bulletin\rbac\AuthorRule;
use Yii;
use yii\db\Migration;

class m220727_104902_init extends Migration
{
    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function safeUp(): void
    {
        $this->createTable('{{%bulletin__board}}', [
            'id' =>  $this->primaryKey()->unsigned(),
            'title' => $this->string(255)->notNull(),
            'description' => $this->string(512)->null()->defaultValue(null),
            'icon' => $this->string(255)->null()->defaultValue(null),
            'status' => $this->boolean()->notNull()->defaultValue(1),
            'is_public' => $this->boolean()->notNull()->defaultValue(1),
            'created_by' => $this->string(64)->null()->defaultValue(null),
            'updated_by' => $this->string(64)->null()->defaultValue(null),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull()
        ]);
        $this->createTable('{{%bulletin__category}}', [
            'id' => $this->primaryKey()->unsigned(),
            'title' => $this->string(255)->notNull(),
            'description' => $this->string(512)->null()->defaultValue(null),
            'created_by' => $this->string(64)->null()->defaultValue(null),
            'updated_by' => $this->string(64)->null()->defaultValue(null),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull()
        ]);
        $this->createTable('{{%bulletin__topic}}', [
            'id' => $this->primaryKey()->unsigned(),
            'board_id' => $this->integer()->unsigned()->notNull(),
            'title' => $this->string(255)->notNull(),
            'status' => $this->boolean()->notNull()->defaultValue(0),
            'created_by' => $this->string(64)->null()->defaultValue(null),
            'updated_by' => $this->string(64)->null()->defaultValue(null),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull()
        ]);
        $this->createTable('{{%bulletin__post}}', [
            'id' => $this->primaryKey()->unsigned(),
            'topic_id' => $this->integer()->unsigned()->notNull(),
            'title' => $this->string(255)->notNull(),
            'text' => $this->text()->notNull(),
            'status' => $this->boolean()->notNull()->defaultValue(1),
            'created_by' => $this->string(64)->null()->defaultValue(null),
            'updated_by' => $this->string(64)->null()->defaultValue(null),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull()
        ]);
        $this->createTable('{{%bulletin__post_attachment}}', [
            'id' => $this->primaryKey()->unsigned(),
            'post_id' => $this->integer()->unsigned()->notNull(),
            'name' => $this->string(255)->notNull(),
            'path' => $this->string(512)->notNull(),
            'mime_type' => $this->string(255)->notNull()->defaultValue('application/octet-stream'),
            'size' => $this->integer()->unsigned()->notNull(),
            'created_by' => $this->string(64)->null()->defaultValue(null),
            'updated_by' => $this->string(64)->null()->defaultValue(null),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull()
        ]);
        $this->createTable('{{%bulletin__board_user}}', [
            'board_id' => $this->integer()->unsigned()->notNull(),
            'user_id' => $this->string(64)->notNull(),
            'PRIMARY KEY ([[board_id]], [[user_id]])'
        ]);
        $this->createTable('{{%bulletin__topic_category}}', [
            'topic_id' => $this->integer()->unsigned()->notNull(),
            'category_id' => $this->integer()->unsigned()->notNull(),
            'PRIMARY KEY ([[topic_id]], [[category_id]])'
        ]);

        $this->addForeignKey(
            '{{%bulletin__topic_ibfk_1}}',
            '{{%bulletin__topic}}',
            'board_id',
            '{{%bulletin__board}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{%bulletin__post_ibfk_1}}',
            '{{%bulletin__post}}',
            'topic_id',
            '{{%bulletin__topic}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{%bulletin__post_attachment_ibfk_1}}',
            '{{%bulletin__post_attachment}}',
            'post_id',
            '{{%bulletin__post}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{%bulletin__board_user_ibfk_1}}',
            '{{%bulletin__board_user}}',
            'board_id',
            '{{%bulletin__board}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{%bulletin__topic_category_ibfk_1}}',
            '{{%bulletin__topic_category}}',
            'topic_id',
            '{{%bulletin__topic}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{%bulletin__topic_category_ibfk_2}}',
            '{{%bulletin__topic_category}}',
            'category_id',
            '{{%bulletin__category}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $auth = Yii::$app->authManager;
        if ($auth) {
            $createBoard = $auth->createPermission('bulletinCreateBoard');
            $createBoard->description = 'Create a bulletin board';
            $auth->add($createBoard);

            $updateBoard = $auth->createPermission('bulletinUpdateBoard');
            $updateBoard->description = 'Update a bulletin board';
            $auth->add($updateBoard);

            $deleteBoard = $auth->createPermission('bulletinDeleteBoard');
            $deleteBoard->description = 'Delete a bulletin board and all of it\'s topic and posts';
            $auth->add($deleteBoard);

            $createCategory = $auth->createPermission('bulletinCreateCategory');
            $createCategory->description = 'Create a bulletin board category';
            $auth->add($createCategory);

            $updateCategory = $auth->createPermission('bulletinUpdateCategory');
            $updateCategory->description = 'Update a bulletin board category';
            $auth->add($updateCategory);

            $deleteCategory = $auth->createPermission('bulletinDeleteCategory');
            $deleteCategory->description = 'Delete a bulletin board category and all off it\'s contents';
            $auth->add($deleteCategory);

            $createTopic = $auth->createPermission('bulletinCreateTopic');
            $createTopic->description = 'Create a bulletin board topic';
            $auth->add($createTopic);

            $updateTopic = $auth->createPermission('bulletinUpdateTopic');
            $updateTopic->description = 'Update a bulletin board topic';
            $auth->add($updateTopic);

            $deleteTopic = $auth->createPermission('bulletinDeleteTopic');
            $deleteTopic->description = 'Delete a bulletin board topic and all of it\'s contents';
            $auth->add($deleteTopic);

            $createPost = $auth->createPermission('bulletinCreatePost');
            $createPost->description = 'Create a bulletin board post';
            $auth->add($createPost);

            $updatePost = $auth->createPermission('bulletinUpdatePost');
            $updatePost->description = 'Update a bulletin board post';
            $auth->add($updatePost);

            $deletePost = $auth->createPermission('bulletinDeletePost');
            $deletePost->description = 'Delete a bulletin board post with all attachments';
            $auth->add($deletePost);

            $administrator = $auth->createRole('bulletinAdministrator');
            $administrator->description = 'Bulletin board administrator (all permissions)';
            $auth->add($administrator);

            $moderator = $auth->createRole('bulletinModerator');
            $moderator->description = 'Bulletin board moderator';
            $auth->add($moderator);

            $author = $auth->createRole('bulletinAuthor');
            $author->description = 'A normal bulletin board user';
            $auth->add($author);

            $rule = new AuthorRule();
            $auth->add($rule);

            $updateOwnPost = $auth->createPermission('bulletinUpdateOwnPost');
            $updateOwnPost->description = 'Update own bulletin board post';
            $updateOwnPost->ruleName = $rule->name;
            $auth->add($updateOwnPost);

            $deleteOwnPost = $auth->createPermission('bulletinDeleteOwnPost');
            $deleteOwnPost->description = 'Delete own bulletin board post and attachments';
            $deleteOwnPost->ruleName = $rule->name;
            $auth->add($deleteOwnPost);

            $updateOwnTopic = $auth->createPermission('bulletinUpdateOwnTopic');
            $updateOwnTopic->description = 'Update own topic';
            $updateOwnTopic->ruleName = $rule->name;
            $auth->add($updateOwnTopic);

            $auth->addChild($updateOwnPost, $updatePost);
            $auth->addChild($deleteOwnPost, $deletePost);
            $auth->addChild($updateOwnTopic, $updateTopic);
            $auth->addChild($author, $createPost);
            $auth->addChild($author, $updateOwnPost);
            $auth->addChild($author, $deleteOwnPost);
            $auth->addChild($author, $createTopic);
            $auth->addChild($author, $updateOwnTopic);
            $auth->addChild($moderator, $author);
            $auth->addChild($moderator, $updatePost);
            $auth->addChild($moderator, $updateTopic);
            $auth->addChild($moderator, $deleteTopic);
            $auth->addChild($administrator, $moderator);
            $auth->addChild($administrator, $deletePost);
            $auth->addChild($administrator, $createCategory);
            $auth->addChild($administrator, $updateCategory);
            $auth->addChild($administrator, $deleteCategory);
            $auth->addChild($administrator, $createBoard);
            $auth->addChild($administrator, $updateBoard);
            $auth->addChild($administrator, $deleteBoard);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function safeDown(): void
    {

        $this->dropForeignKey('{{%bulletin__category_user_ibfk_1}}', '{{%bulletin__category_user}}');
        $this->dropForeignKey('{{%bulletin__post_attachment_ibfk_1}}', '{{%bulletin__post_attachment}}');
        $this->dropForeignKey('{{%bulletin__post_ibfk_1}}', '{{%bulletin__post}}');
        $this->dropForeignKey('{{%bulletin__topic_ibfk_1}}', '{{%bulletin__topic}}');
        $this->dropForeignKey('{{%bulletin__topic_category_ibfk_1}}', '{{%bulletin__topic_category}}');
        $this->dropForeignKey('{{%bulletin__topic_category_ibfk_2}}', '{{%bulletin__topic_category}}');

        $this->dropTable('{{%bulletin__board_user}}');
        $this->dropTable('{{%bulletin__post_attachment}}');
        $this->dropTable('{{%bulletin__post}}');
        $this->dropTable('{{%bulletin__topic}}');
        $this->dropTable('{{%bulletin__category}}');
        $this->dropTable('{{%bulletin__board}}');

        $auth = Yii::$app->authManager;
        if ($auth) {
            $createBoard = $auth->getPermission('bulletinCreateBoard');
            $updateBoard = $auth->getPermission('bulletinUpdateBoard');
            $deleteBoard = $auth->getPermission('bulletinDeleteBoard');
            $createCategory = $auth->getPermission('bulletinCreateCategory');
            $updateCategory = $auth->getPermission('bulletinUpdateCategory');
            $deleteCategory = $auth->getPermission('bulletinDeleteCategory');
            $createTopic = $auth->getPermission('bulletinCreateTopic');
            $updateTopic = $auth->getPermission('bulletinUpdateTopic');
            $deleteTopic = $auth->getPermission('bulletinDeleteTopic');
            $createPost = $auth->getPermission('bulletinCreatePost');
            $updatePost = $auth->getPermission('bulletinUpdatePost');
            $deletePost = $auth->getPermission('bulletinDeletePost');
            $administrator = $auth->getRole('bulletinAdministrator');
            $moderator = $auth->getRole('bulletinModerator');
            $author = $auth->getRole('bulletinAuthor');
            $rule = $auth->getRule('bulletin_isAuthor');
            $updateOwnPost = $auth->getPermission('bulletinUpdateOwnPost');
            $deleteOwnPost = $auth->getPermission('bulletinDeleteOwnPost');
            $updateOwnTopic = $auth->getPermission('bulletinUpdateOwnTopic');

            $auth->remove($createBoard);
            $auth->remove($updateBoard);
            $auth->remove($deleteBoard);
            $auth->remove($updateOwnPost);
            $auth->remove($deleteOwnPost);
            $auth->remove($updateOwnTopic);
            $auth->remove($rule);
            $auth->remove($createCategory);
            $auth->remove($updateCategory);
            $auth->remove($deleteCategory);
            $auth->remove($createTopic);
            $auth->remove($updateTopic);
            $auth->remove($deleteTopic);
            $auth->remove($createPost);
            $auth->remove($updatePost);
            $auth->remove($deletePost);
            $auth->remove($administrator);
            $auth->remove($moderator);
            $auth->remove($author);
        }
    }
}
