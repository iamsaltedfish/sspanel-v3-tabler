<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\Ann;
use Slim\Http\Request;
use Slim\Http\Response;

class AnnController extends AdminController
{
    /**
     * 后台公告页面
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function index($request, $response, $args)
    {
        $table_config = [
            'total_column' => [
                'op' => '操作',
                'id' => 'ID',
                'date' => '日期',
                'content' => '内容',
            ],
            'default_show_column' => [
                'op', 'id', 'date', 'content',
            ],
            'ajax_url' => 'announcement/ajax',
        ];
        return $response->write(
            $this->view()
                ->assign('table_config', $table_config)
                ->display('admin/announcement/index.tpl')
        );
    }

    /**
     * 后台公告页面 AJAX
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function ajax($request, $response, $args)
    {
        $query = Ann::getTableDataFromAdmin(
            $request,
            static function (&$order_field) {
                if (in_array($order_field, ['op'])) {
                    $order_field = 'id';
                }
            }
        );

        $data = [];
        foreach ($query['datas'] as $value) {
            /** @var Ann $value */

            $tempdata = [];
            $tempdata['op'] = '<a class="btn btn-brand" href="/admin/announcement/' . $value->id . '/edit">编辑</a> <a class="btn btn-brand-accent" id="delete" value="' . $value->id . '" href="javascript:void(0);" onClick="delete_modal_show(\'' . $value->id . '\')">删除</a>';
            $tempdata['id'] = $value->id;
            $tempdata['date'] = $value->date;
            $tempdata['content'] = $value->content;

            $data[] = $tempdata;
        }

        return $response->withJson([
            'draw' => $request->getParam('draw'),
            'recordsTotal' => Ann::count(),
            'recordsFiltered' => $query['count'],
            'data' => $data,
        ]);
    }

    /**
     * 后台公告创建页面
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function create($request, $response, $args)
    {
        return $response->write(
            $this->view()
                ->display('admin/announcement/create.tpl')
        );
    }

    /**
     * 后台添加公告
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function add($request, $response, $args)
    {
        $ann = new Ann();
        $ann->content = $request->getParam('content');
        $ann->markdown = $request->getParam('markdown');
        $ann->date = date('Y-m-d H:i:s');
        $ann->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => '公告添加成功',
        ]);
    }

    /**
     * 后台编辑公告页面
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function edit($request, $response, $args)
    {
        $ann = Ann::find($args['id']);
        return $response->write(
            $this->view()
                ->assign('ann', $ann)
                ->display('admin/announcement/edit.tpl')
        );
    }

    /**
     * 后台编辑公告提交
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function update($request, $response, $args)
    {
        $ann = Ann::find($args['id']);
        $ann->content = $request->getParam('content');
        $ann->markdown = $request->getParam('markdown');
        $ann->date = date('Y-m-d H:i:s');
        if (!$ann->save()) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '修改失败',
            ]);
        }
        return $response->withJson([
            'ret' => 1,
            'msg' => '修改成功',
        ]);
    }

    /**
     * 后台删除公告
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function delete($request, $response, $args)
    {
        $ann = Ann::find($request->getParam('id'));
        if (!$ann->delete()) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '删除失败',
            ]);
        }
        return $response->withJson([
            'ret' => 1,
            'msg' => '删除成功',
        ]);
    }
}
