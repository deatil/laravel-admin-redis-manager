<?php   

namespace Lake\Admin\RedisManager\Action;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;

class Conn extends AbstractTool
{   
    public function render()
    {
        $url = route('lake-redis-index');
        $html = <<<EOF
<div class="btn-group">   
    <a class="btn btn-sm bg-green" href="{$url}" rel="external nofollow" >
        Manager
    </a> 
</div> 
        
EOF;
        return $html;
    }
} 
