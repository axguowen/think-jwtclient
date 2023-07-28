<?php
// +----------------------------------------------------------------------
// | ThinkPHP JwtClient [Simple JsonWebToken Client For ThinkPHP]
// +----------------------------------------------------------------------
// | ThinkPHP JwtClient
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: axguowen <axguowen@qq.com>
// +----------------------------------------------------------------------

namespace think\facade;

use think\Facade;

/**
 * @see \think\JwtClient
 * @mixin \think\JwtClient
 */
class JwtClient extends Facade
{
    /**
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'think\JwtClient';
    }
}
