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

namespace think\jwtclient;

use think\facade\Config;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\IdentifiedBy;
use Lcobucci\JWT\Validation\Constraint\RelatedTo;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Lcobucci\JWT\Validation\Validator;
use Lcobucci\JWT\UnencryptedToken;

/**
 * JWT操作句柄
 */
class Handler
{
	/**
     * 配置参数
     * @var array
     */
	protected $options = [
        // 签名密钥
        'sign_key' => 'default_sign_key',
        // 有效时间, 单位秒
        'expiration_time' => 900,
        // 指定时间后生效, 单位秒
        'not_before' => 0,
        // 颁发者
        'issuer' => '',
        // 接收者
        'audience' => '',
        // 识别码
        'id' => '',
        // 关联对象
        'subject' => '',
    ];

	/**
     * 架构函数
     * @access public
     * @param array $options 配置参数
     * @return void
     */
    public function __construct(array $options = [])
    {
        $this->setConfig($options);
    }

	/**
     * 动态设置平台配置参数
     * @access public
     * @param array $options 平台配置
     * @return $this
     */
    public function setConfig(array $options)
    {
        // 合并配置
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        // 返回
        return $this;
    }

    /**
     * 颁发令牌
     * @access public
     * @param array $data 数据
     * @param int $expire 过期时间
     * @return array
     */
    public function issue(array $data, $expire = null)
    {
        // 实例化构造器
        $builder = new Builder(new JoseEncoder(), ChainedFormatter::default());
        // 当前时间类
        $now = new \DateTimeImmutable('now', new \DateTimeZone(Config::get('app.default_timezone', 'Asia/Shanghai')));
        // 签名串
        $signKey = InMemory::plainText(hash('md5', $this->options['sign_key']));
        // 过期时间
        if(is_null($expire)){
            $expire = $this->options['expiration_time'];
        }
        // 设置有效时间
        $builder->withClaim('data', $data)
                // 设置颁发时间
                ->issuedAt($now)
                // 设置有效时间
                ->expiresAt($now->modify('+' . $this->options['expiration_time'] . ' seconds'))
                // 设置颁发者
                ->issuedBy($this->options['issuer'])
                // 设置接收者
                ->permittedFor($this->options['audience'])
                // 设置唯一识别码
                ->identifiedBy($this->options['id'])
                // 设置关联对象
                ->relatedTo($this->options['subject'])
                // 设置可以开始使用的时间
                ->canOnlyBeUsedAfter($now->modify('+' . $this->options['not_before'] . ' seconds'));
        // 返回token字符串
        return $builder->getToken(new Sha256(), $signKey)->toString();
    }

    /**
     * 解析令牌数据
     * @access public
     * @param string $token 令牌内容
     * @return array
     */
    public function parse(string $token)
    {
        // 获取解析对象
        $decryptResult = $this->decrypt($token);
        // 如果解析失败
        if(is_null($decryptResult[0])){
            // 返回
            return $decryptResult;
        }
        // 获取解密对象
        $unencryptedToken = $decryptResult[0];
        // 返回结果
        return [$unencryptedToken->claims()->get('data'), null];
    }

    /**
     * 验证令牌是否有效
     * @access public
     * @param string $token 令牌内容
     * @return array
     */
    public function validate(string $token)
	{
        // 获取解析对象
        $decryptResult = $this->decrypt($token);
        // 如果解析失败
        if(is_null($decryptResult[0])){
            // 返回
            return $decryptResult;
        }
        // 获取解密对象
        $unencryptedToken = $decryptResult[0];
        // 实例化验证器
        $validator = new Validator();
        // 尝试验证
        try {
            // 验证是否过期
            $validator->assert($unencryptedToken, new StrictValidAt(new \Lcobucci\Clock\SystemClock(new \DateTimeZone(Config::get('app.default_timezone', 'Asia/Shanghai')))));
            // 校验签名
            $signKey = InMemory::plainText(hash('md5', $this->options['sign_key']));
            $validator->assert($unencryptedToken, new SignedWith(new Sha256(), $signKey));
            // 如果颁发者不为空
            if(!empty($this->options['issuer'])){
                $validator->assert($unencryptedToken, new IssuedBy($this->options['issuer']));
            }
            // 如果接收者不为空
            if(!empty($this->options['audience'])){
                $validator->assert($unencryptedToken, new PermittedFor($this->options['audience']));
            }
            // 如果识别码不为空
            if(!empty($this->options['id'])){
                $validator->assert($unencryptedToken, new IdentifiedBy($this->options['id']));
            }
            // 如果关联对象不为空
            if(!empty($this->options['subject'])){
                $validator->assert($unencryptedToken, new RelatedTo($this->options['subject']));
            }
        } catch (RequiredConstraintsViolated $e) {
            // 返回
            return [null, new \Exception('Token验证失败: ' . $e->getMessage(), 401)];
        }

        return [$unencryptedToken->claims()->get('data'), null];
    }

    /**
     * 解密令牌
     * @access protected
     * @param string $token 令牌内容
     * @return array
     */
    protected function decrypt(string $token)
	{
        // 格式化
        $token = trim(str_ireplace('Bearer', '', $token));
        // 实例化解析器
        $parser = new Parser(new JoseEncoder());
        // 尝试解析
        try {
            $unencryptedToken = $parser->parse($token);
            assert($unencryptedToken instanceof UnencryptedToken);
        } catch (\Throwable $e) {
            return [null, new \Exception('Token解析失败: ' . $e->getMessage(), 400)];
        }

        // 返回解析结果
        return [$unencryptedToken, null];
    }
}
