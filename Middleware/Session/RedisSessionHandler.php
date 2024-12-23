<?php
namespace Middleware\Session;

use SessionHandlerInterface;

# @url : https://github.com/snc/SncRedisBundle/blob/master/Session/Storage/Handler/RedisSessionHandler.php
class RedisSessionHandler implements SessionHandlerInterface
{
    /**
     * @var \Predis\Client|\Redis
     */
    protected $redis;

    /**
     * @var int
     */
    protected $ttl;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var int Default PHP max execution time in seconds
     */
    const DEFAULT_MAX_EXECUTION_TIME = 30;

    /**
     * @var bool Indicates an sessions should be locked
     */
    protected $locking;

    /**
     * @var bool Indicates an active session lock
     */
    protected $locked;

    /**
     * @var string Session lock key
     */
    private $lockKey;

    /**
     * @var string Session lock token
     */
    private $token;

    /**
     * @var int Microseconds to wait between acquire lock tries
     */
    private $spinLockWait;

    /**
     * @var int Maximum amount of seconds to wait for the lock
     */
    protected $lockMaxWait;

    /**
     * Redis session storage constructor.
     *
     * @param \Predis\Client|\Redis $redis   Redis database connection
     * @param array                 $options Session options
     * @param string                $prefix  Prefix to use when writing session data
     */
    public function __construct($redis, $ttl = 0, $prefix = 'session', $locking = true, $spinLockWait = 150000)
    {
        $this->redis = $redis;
        $this->ttl = $ttl;
        $this->prefix = $prefix;

        $this->locking = $locking;
        $this->locked = false;
        $this->lockKey = null;
        $this->spinLockWait = $spinLockWait;
        $this->lockMaxWait = ini_get('max_execution_time');
        if (!$this->lockMaxWait) {
            $this->lockMaxWait = self::DEFAULT_MAX_EXECUTION_TIME;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * Lock the session data.
     */
    protected function lockSession($sessionId)
    {
        $attempts = (1000000 / $this->spinLockWait) * $this->lockMaxWait;

        $this->token = uniqid();
        $this->lockKey = $sessionId.'.lock';

        $setFunction = function ($redis, $key, $token, $ttl) {
            if ($redis instanceof \Redis) {
                return $redis->set(
                    $key,
                    $token,
                    ['NX', 'PX' => $ttl]
                );
            } else {
                return $redis->set(
                    $key,
                    $token,
                    'PX',
                    $ttl,
                    'NX'
                );
            }
        };

        for ($i = 0;$i < $attempts;++$i) {

            // We try to aquire the lock
            $success = $setFunction($this->redis, $this->getRedisKey($this->lockKey), $this->token, $this->lockMaxWait * 1000 + 1);
            if ($success) {
                $this->locked = true;

                return true;
            }

            usleep($this->spinLockWait);
        }

        return false;
    }

    /**
     * Unlock the session data.
     */
    private function unlockSession()
    {
        // If we have the right token, then delete the lock
        $script = <<<LUA
if redis.call("GET", KEYS[1]) == ARGV[1] then
    return redis.call("DEL", KEYS[1])
else
    return 0
end
LUA;

        if ($this->redis instanceof \Redis) {
            $this->redis->eval($script, [$this->getRedisKey($this->lockKey), $this->token], 1);
        } else {
            $this->redis->eval($script, 1, $this->getRedisKey($this->lockKey), $this->token);
        }
        $this->locked = false;
        $this->token = null;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        if ($this->locking) {
            if ($this->locked) {
                $this->unlockSession();
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        if ($this->locking) {
            if (!$this->locked) {
                if (!$this->lockSession($sessionId)) {
                    return false;
                }
            }
        }

        // ttl refresh ?
        // $this->redis->expire($this->getRedisKey($sessionId), $this->ttl);
        return $this->redis->get($this->getRedisKey($sessionId)) ?: '';
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $data)
    {
        if (0 < $this->ttl) {
            $this->redis->setex($this->getRedisKey($sessionId), $this->ttl, $data);
        } else {
            $this->redis->set($this->getRedisKey($sessionId), $data);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        $this->redis->del($this->getRedisKey($sessionId));
        $this->close();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function gc($lifetime)
    {
        return true;
    }

    /**
     * Change the default TTL.
     *
     * @param int $ttl
     */
    public function setTtl($ttl)
    {
        $this->ttl = $ttl;
    }

    /**
     * Prepends the given key with a user-defined prefix (if any).
     *
     * @param string $key key
     *
     * @return string prefixed key
     */
    protected function getRedisKey($key)
    {
        if (empty($this->prefix)) {
            return $key;
        }

        return $this->prefix.$key;
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        $this->close();
    }
}
