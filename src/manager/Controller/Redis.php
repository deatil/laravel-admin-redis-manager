<?php

namespace Lake\Admin\RedisManager\Controller;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;

use Lake\Admin\RedisManager\RedisManager;

class Redis extends BaseController
{
    /**
     * Index page.
     *
     * @return Content
     */
    public function index(Request $request)
    {
        return Admin::content(function (Content $content) {
            $content->header('Redis manager');
            $content->description('Connections');
            $content->breadcrumb(['text' => 'Redis manager']);
            $connection = request('conn', 'default');
            $manager = $this->manager();
            $variables = [
                'conn'        => $connection,
                'info'        => $manager->getInformation(),
                'connections' => $manager->getConnections(),
                'keys'        => $manager->scan(
                    request('pattern', '*'),
                    request('count', 50)
                ),
            ];
            
            $content->body(view('lake-redis-manager::index', $variables));
        });
    }

    /**
     * Edit page.
     *
     * @param Request $request
     *
     * @return Content
     */
    public function edit(Request $request)
    {
        return Admin::content(function (Content $content) use ($request) {
            $connection = $request->get('conn', 'default');

            $manager = $this->manager();

            $variables = [
                'conn'        => $connection,
                'info'        => $manager->getInformation(),
                'connections' => $manager->getConnections(),
                'data'        => $manager->fetch($request->get('key')),
            ];

            if (empty($variables['data'])) {
                $view = 'lake-redis-manager::edit.nil';
            } else {
                $view = 'lake-redis-manager::edit.'.$variables['data']['type'];
            }
            $content->header('Redis manager');
            $content->description('Connections');
            $content->breadcrumb(
                ['text' => 'Redis manager', 'url' => route('lake-redis-index', ['conn' => $connection])],
                ['text' => 'Edit']
            );
            $content->body(view($view, $variables));
        });
    }

    /**
     * Create page.
     *
     * @param Request $request
     *
     * @return Content
     */
    public function create(Request $request)
    {
        return Admin::content(function (Content $content) use ($request) {
            $connection = $request->get('conn', 'default');

            $manager = $this->manager();

            $vars = [
                'conn'        => $connection,
                'info'        => $manager->getInformation(),
                'connections' => $manager->getConnections(),
                'type'        => $request->get('type'),
            ];

            $view = 'lake-redis-manager::create.'.$vars['type'];

            $content->header('Redis manager');
            $content->description('Connections');
            $content->breadcrumb(
                ['text' => 'Redis manager', 'url' => route('lake-redis-index', ['conn' => $connection])],
                ['text' => 'Create']
            );
            $content->body(view($view, $vars));
        });
    }

    /**
     * zsethot page.
     *
     * @param Request $request
     *
     * @return Content
     */
    public function zsethot(Request $request)
    {
        return Admin::content(function (Content $content) use ($request) {
            $connection = $request->get('conn', 'default');

            $manager = $this->manager();

            $vars = [
                'conn'        => $connection,
                'info'        => $manager->getInformation(),
                'connections' => $manager->getConnections(),
                'key'         => $request->get('key'),
                'order'       => $request->get('order', 'desc'),
                'data'        => $manager->zsetData()->tool($request->get('key'), $request->get('order', 'desc')),
            ];

            $view = 'lake-redis-manager::zsethot';

            $content->header('Redis manager');
            $content->description('ZSet hot');
            $content->breadcrumb(
                ['text' => 'Redis manager', 'url' => route('lake-redis-index', ['conn' => $connection])],
                ['text' => 'ZSet hot']
            );
            $content->body(view($view, $vars));
        });
    }

    /**
     * setdata page.
     *
     * @param Request $request
     *
     * @return Content
     */
    public function setdata(Request $request)
    {
        return Admin::content(function (Content $content) use ($request) {
            $connection = $request->get('conn', 'default');

            $manager = $this->manager();
            
            $data = [];
            $params = $request->all();
            if (isset($params['key1']) 
                && isset($params['key2']) 
                && isset($params['action'])
            ) {
                if (in_array($params['action'], ['sdiffstore', 'sinterstore', 'sunionstore'])) {
                    return redirect(route('lake-redis-set-data'));
                }
                
                $data = $manager->setData()->tool($params);
            }

            $vars = [
                'conn'        => $connection,
                'info'        => $manager->getInformation(),
                'connections' => $manager->getConnections(),
                'params'      => $params,
                'data'        => $data,
            ];

            $view = 'lake-redis-manager::setdata';

            $content->header('Redis manager');
            $content->description('Set data');
            $content->breadcrumb(
                ['text' => 'Redis manager', 'url' => route('lake-redis-index', ['conn' => $connection])],
                ['text' => 'Set data']
            );
            $content->body(view($view, $vars));
        });
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function setdataStore(Request $request)
    {
        $params = $request->all();
        if (!(isset($params['key1']) 
            && isset($params['key2']) 
            && isset($params['action'])
            && in_array($params['action'], ['sdiffstore', 'sinterstore', 'sunionstore'])
        )) {
            return redirect(route('lake-redis-set-data'));
        }
        
        return $this->manager()->setData()->tool($params);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function store(Request $request)
    {
        $type = $request->get('type');

        $this->manager()->{$type.'Data'}()->store($request->all());
        
        $redirect = $request->get('redirect');
        if ($redirect == 1) {
            return redirect(route('lake-redis-index', [
                'conn' => request('conn'),
            ]));
        }
    }

    /**
     * @param Request $request
     *
     * @return int
     */
    public function destroy(Request $request)
    {
        return $this->manager()->del($request->get('key'));
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function fetch(Request $request)
    {
        return $this->manager()->fetch($request->get('key'));
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function remove(Request $request)
    {
        $type = $request->get('type');

        return $this->manager()->{$type.'Data'}()->remove($request->all());
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function update(Request $request)
    {
        $this->manager()->update($request);
        
        $redirect = $request->get('redirect');
        if ($redirect == 1) {
            return redirect(route('lake-redis-index', [
                'conn' => request('conn'),
            ]));
        }
    }

    /**
     * Redis console interface.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function console(Request $request)
    {
        return Admin::content(function (Content $content) use ($request) {
            $connection = $request->get('conn', 'default');

            $manager = $this->manager();

            $vars = [
                'conn'        => $connection,
                'info'        => $manager->getInformation(),
                'connections' => $manager->getConnections(),
            ];

            $view = 'lake-redis-manager::console';

            $content->header('Redis manager');
            $content->description('Connections');
            $content->breadcrumb(
                ['text' => 'Redis manager', 'url' => route('lake-redis-index', ['conn' => $connection])],
                ['text' => 'Console']
            );
            $content->body(view($view, $vars));
        });
    }

    /**
     * Execute a redis command.
     *
     * @param Request $request
     *
     * @return bool|string
     */
    public function execute(Request $request)
    {
        $command = $request->get('command');

        try {
            $result = $this->manager()->execute($command);
        } catch (\Exception $exception) {
            return $this->renderException($exception);
        }

        if (is_string($result) && Str::startsWith($result, ['ERR ', 'WRONGTYPE '])) {
            return $this->renderException(new \Exception($result));
        }

        return $this->getDumpedHtml($result);
    }

    /**
     * Render exception.
     *
     * @param \Exception $exception
     *
     * @return string
     */
    protected function renderException(\Exception $exception)
    {
        return sprintf(
            "<div class='callout callout-warning'><i class='icon fa fa-warning'></i>&nbsp;&nbsp;&nbsp;%s</div>",
            str_replace("\n", '<br />', $exception->getMessage())
        );
    }

    /**
     * Get html of dumped variable.
     *
     * @param mixed $var
     *
     * @return bool|string
     */
    protected function getDumpedHtml($var)
    {
        ob_start();

        dump($var);

        $content = ob_get_contents();

        ob_get_clean();

        return substr($content, strpos($content, '<pre '));
    }

    /**
     * Get the redis manager instance.
     *
     * @return RedisManager
     */
    protected function manager($conn = null)
    {
        if (!$conn) {
            $conn = request()->get('conn', 'default');
        }

        return RedisManager::instance($conn);
    }
}
