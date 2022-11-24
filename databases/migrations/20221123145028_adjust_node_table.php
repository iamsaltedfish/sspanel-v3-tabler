<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AdjustNodeTable extends AbstractMigration
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
        $table = $this->table('node');

        $table->changeColumn('type', 'integer', [
            'comment' => '是否显示',
        ])->save();

        $table->changeColumn('info', 'string', [
            'comment' => '公有备注',
        ])->save();

        $table->changeColumn('status', 'string', [
            'comment' => '节点状态',
        ])->save();

        $table->changeColumn('remark', 'string', [
            'comment' => '私有备注',
        ])->save();

        $table->changeColumn('bandwidthlimit_resetday', 'integer', [
            'comment' => '流量重置日',
            'default' => 1,
        ])->save();

        $table->removeColumn('gfw_block')
            ->save();
    }
}
