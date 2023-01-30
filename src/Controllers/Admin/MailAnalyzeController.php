<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\Statistics;

class MailAnalyzeController extends AdminController
{
    public function index($request, $response, $args)
    {
        $limit = $_ENV['statistics_range']['mail_count'] ?? 30;
        $datas = Statistics::orderBy('id', 'desc')
            ->where('item', 'mail_count')
            ->limit($limit)
            ->get();

        $datas = array_reverse($datas->toArray());

        $chart_content = [];
        $mail_type = ['basic', 'system', 'work_order', 'due_reminder', 'account_security'];
        // general_notice important_notice market traffic_report
        foreach ($mail_type as $type) {
            $set = [];
            foreach ($datas as $data) {
                $record = json_decode($data['value'], true);
                $set[] = $record[$type];
            }
            $chart_content[] = [
                'name' => $type,
                'data' => $set,
            ];
        }
        $categories = [];
        foreach ($datas as $data) {
            // 第x天生成x-1天的数据所以created_at减60秒将数据对应的时间向前推一天
            $timestamp = $data['created_at'] - 60;
            if (date('d', $timestamp) === '1') {
                $categories[] = '"' . date('m-d', $timestamp) . '"';
            } else {
                $categories[] = '"' . date('j', $timestamp) . '"';
            }
        }

        return $response->write(
            $this->view()
                ->assign('categories', $categories)
                ->assign('chart_content', $chart_content)
                ->display('admin/mail/analyze.tpl')
        );
    }
}
