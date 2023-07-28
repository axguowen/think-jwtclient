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

namespace think;

use think\helper\Arr;
use think\exception\InvalidArgumentException;
use think\jwtclient\Platform;

/**
 * Jwt客户端
 */
class JwtClient extends Manager
{
	/**
     * 默认驱动
     * @access public
     * @return string|null
     */
    public function getDefaultDriver()
    {
        return $this->getConfig('default');
    }

	/**
     * 获取jwt客户端配置
     * @access public
     * @param null|string $name 配置名称
     * @param mixed $default 默认值
     * @return mixed
     */
    public function getConfig($name = null, $default = null)
    {
        if (!is_null($name)) {
            return $this->app->config->get('jwtclient.' . $name, $default);
        }

        return $this->app->config->get('jwtclient');
    }

	/**
     * 获取指定平台配置
     * @param string $platform 平台名称
     * @param null|string $name 配置名称
     * @param null|string $default 默认值
     * @return array
     */
    public function getPlatformConfig($platform, $name = null, $default = null)
    {
		// 读取平台配置文件
        if ($config = $this->getConfig('platforms.' . $platform)) {
            return Arr::get($config, $name, $default);
        }
		// 平台不存在
        throw new \InvalidArgumentException('平台 [' . $platform . '] 配置不存在.');
    }

    /**
     * 获取驱动类型
     * @param string $name 平台名称
     * @return mixed
     */
    protected function resolveType(string $name)
    {
        return \think\jwtclient\Handler::class;
    }

	/**
     * 获取平台配置
     * @param string $name 平台名称
     * @return mixed
     */
    protected function resolveConfig($name)
    {
        return $this->getPlatformConfig($name);
    }

	/**
     * 选择或者切换平台
     * @access public
     * @param string $name 平台的配置名
     * @param array $options 自定义平台配置
     * @return \think\jwtclient\Platform
     */
    public function platform($name = null, array $options = [])
    {
        return $this->driver($name)->setConfig($options);
    }

	/**
     * 颁发token
     * @access public
     * @param array $data 数据
     * @return array
     */
    public function issue(array $data)
    {
        return $this->platform()->issue($data);
    }

	/**
     * 解析token
     * @access public
     * @param string $token token值
     * @return array
     */
    public function parse(string $token)
    {
        return $this->platform()->parse($token);
    }

	/**
     * 验证token是否有效
     * @access public
     * @param string $token token值
     * @return array
     */
    public function validate(string $token)
    {
        return $this->platform()->validate($token);
    }
}
