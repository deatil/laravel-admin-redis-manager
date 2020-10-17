<?php

namespace Lake\Admin\RedisManager\Controller;

use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

use Lake\Admin\RedisManager\Model\Connection as ConnectionModel;
use Lake\Admin\RedisManager\Action\Conn as ConnAction;

class Connection
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Connection')
            ->description('list')
            ->body($this->grid());
    }

    /**
     * Edit interface.
     *
     * @param int     $id
     * @param Content $content
     *
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Connection')
            ->description('edit')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Connection')
            ->description('create')
            ->body($this->form());
    }

    public function show($id, Content $content)
    {
        return $content
            ->header('Connection')
            ->description('detail')
            ->body(Admin::show(ConnectionModel::findOrFail($id), function (Show $show) {
                $show->id();
                $show->title();
                $show->name();
                $show->description();
                $show->url();
                $show->host();
                $show->password();
                $show->port();
                $show->database();
                $show->status()->as(function($name) {
                    if ($name == 1) {
                        return 'opened';
                    } else {
                        return 'closed';
                    }
                });
                $show->created_at()->as(function($item) {
                    return date('Y-m-d H:i:s', strtotime($item));
                });
                $show->updated_at()->as(function($item) {
                    return date('Y-m-d H:i:s', strtotime($item));
                });
            }));
    }

    public function grid()
    {
        $grid = new Grid(new ConnectionModel());

        $grid->id('ID')->sortable();
        $grid->title();
        $grid->name();
        $grid->column('host')->display(function ($host) {
            return "<code>{$this->host}:{$this->port}</code>";
        });
        $grid->database();
        $grid->created_at()->display(function($item) {
            return date('Y-m-d H:i:s', strtotime($item));
        });
        
        $grid->tools(function ($tools) {
            $tools->append(new ConnAction());
        }); 

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->like('name');
            $filter->like('host');
        });

        return $grid;
    }

    public function form()
    {
        $form = new Form(new ConnectionModel());

        $form->display('id', 'ID');
        $form->text('title')->rules('required');
        $form->text('name')->rules('required|alpha_dash');
        $form->textarea('description');
        $form->text('url');
        $form->text('host')->rules('required');
        $form->text('password');
        $form->text('port')->rules('required');
        $form->text('database')->rules('required|numeric');
        $form->radio('status', '状态')
            ->options([
                '1' => '启用',
                '0' => '禁用',
            ])
            ->default(1)
            ->help('连接信息状态');

        $form->display('created_at');
        $form->display('updated_at');

        return $form;
    }
}
