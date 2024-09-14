<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%users}}`.
 */
class m240913_104343_create_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull(),
            'email' => $this->string()->notNull()->unique(),
            'password_hash' => $this->string()->notNull(),
            'role' => $this->smallInteger()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->execute('
        CREATE OR REPLACE FUNCTION update_updated_at_column()
        RETURNS TRIGGER AS $$
        BEGIN
            NEW.updated_at = CURRENT_TIMESTAMP;
            RETURN NEW;
        END;
        $$ LANGUAGE plpgsql;
    ');

    $this->execute('
        CREATE TRIGGER update_user_updated_at
        BEFORE UPDATE ON "users"
        FOR EACH ROW
        EXECUTE FUNCTION update_updated_at_column();
    ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('DROP TRIGGER IF EXISTS update_user_updated_at ON "users";');

        $this->execute('DROP FUNCTION IF EXISTS update_updated_at_column;');

        $this->dropTable('{{%users}}');
    }
}
