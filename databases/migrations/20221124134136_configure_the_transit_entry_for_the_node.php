<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ConfigureTheTransitEntryForTheNode extends AbstractMigration
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
        $table->addColumn('transit_json', 'text', [
            'comment' => '中转入口',
            'after' => 'online',
            'default' => null,
            'null' => true,
        ])->update();

        $table->addColumn('transit_enable', 'integer', [
            'comment' => '功能开关',
            'after' => 'transit_json',
            'default' => 0,
        ])->update();

        $table->addColumn('parsing_mode', 'text', [
            'comment' => '解析模式',
            'after' => 'transit_enable',
            'default' => 'v2ray_ws',
        ])->update();

        $table->addColumn('add_in', 'integer', [
            'comment' => '是否加入订阅',
            'after' => 'parsing_mode',
            'default' => 0,
        ])->update();
    }
}
