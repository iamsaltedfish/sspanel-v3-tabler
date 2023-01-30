<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateEmailTaskTable extends AbstractMigration
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
        $table = $this->table('email_task');
        $table->addColumn('task_coding', 'string', ['comment' => '任务编号', 'limit' => 16])
            ->addColumn('push_title', 'text', ['comment' => '推送标题'])
            ->addColumn('push_content', 'text', ['comment' => '推送内容'])
            ->addColumn('params', 'text', ['comment' => '推送参数'])
            ->addColumn('error', 'text', ['comment' => '错误日志', 'default' => null, 'null' => true])
            ->addColumn('recipients_count', 'integer', ['comment' => '收件人总数'])
            ->addColumn('created_at', 'integer', ['comment' => '创建时间'])
            ->create();
    }
}
