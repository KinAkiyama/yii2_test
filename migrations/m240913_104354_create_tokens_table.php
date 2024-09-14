<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tokens}}`.
 */
class m240913_104354_create_tokens_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tokens}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'token' => $this->string()->notNull()->unique(),
            'expires_at' => $this->timestamp()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk-token-user_id',
            '{{%tokens}}',
            'user_id',
            '{{%users}}',
            'id',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-token-user_id',
            '{{%tokens}}'
        );

        $this->dropTable('{{%tokens}}');
    }
}
