<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AllowUserInviteField extends AbstractMigration
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
        $table = $this->table('user');
        $table->addColumn('force_allow_invite', 'integer', [
            'comment' => '强制允许邀请',
            'default' => 0,
        ])->update();
    }
}
