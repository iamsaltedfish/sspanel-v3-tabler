<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class MailPushTable extends AbstractMigration
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
        $table->addColumn('user_id', 'integer',        ['comment' => '用户id'])
            ->addColumn('basic', 'integer',            ['comment' => '基础'])
            ->addColumn('market', 'integer',           ['comment' => '营销'])
            ->addColumn('due_reminder', 'integer',     ['comment' => '到期提醒'])
            ->addColumn('account_security', 'integer', ['comment' => '账户安全'])
            ->addColumn('work_order', 'integer',       ['comment' => '工单通知'])
            ->addColumn('traffic_report', 'integer',   ['comment' => '流量报告'])
            ->addColumn('general_notice', 'integer',   ['comment' => '一般公告'])
            ->addColumn('important_notice', 'integer', ['comment' => '重要公告'])
            ->addColumn('access_token', 'text',        ['comment' => '访问密钥'])
            ->addColumn('created_at', 'integer',       ['comment' => '创建时间'])
            ->addColumn('updated_at', 'integer',       ['comment' => '更新时间'])
            ->create();
    }
}
