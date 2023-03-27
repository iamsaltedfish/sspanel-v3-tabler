<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class LoginSubscribeNotificationSettings extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('mail_push');
        $table->addColumn('login_reminder', 'integer', ['after' => 'important_notice', 'default' => 0, 'comment' => '登录时通知'])
            ->addColumn('sub_reminder', 'integer', ['after' => 'important_notice', 'default' => 0, 'comment' => '订阅时通知'])
            ->update();
    }
}
