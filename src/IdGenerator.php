<?php
/**
 * Created for id-generator-redis
 * Date: 31.03.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace DiBify\IdGenerator\Redis;


use DiBify\DiBify\Id\Id;
use DiBify\DiBify\Id\IdGeneratorInterface;
use DiBify\DiBify\Model\ModelInterface;
use Redis;

class IdGenerator implements IdGeneratorInterface
{

    private string $key;
    private Redis $redis;

    public function __construct(Redis $redis, $key = 'RedisIdGenerator')
    {
        $this->key = $key;
        $this->redis = $redis;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(ModelInterface $model): Id
    {
        if (!$model->id()->isAssigned()) {
            $model->id()->assign($this->getRedis()->hIncrBy($this->getKey(), $model::getModelAlias(), 1));
        }
        return $model->id();
    }

    /**
     * @param string $modelAlias
     * @param int $current
     */
    public function setCounterValue(string $modelAlias, int $current)
    {
        $this->getRedis()->hSet($this->getKey(), $modelAlias, $current);
    }

    protected function getRedis(): Redis
    {
        return $this->redis;
    }

    protected function getKey(): string
    {
        return $this->key;
    }

}