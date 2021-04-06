<?php
/**
 * Created for id-generator-redis
 * Date: 03.04.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace DiBify\IdGenerator\Redis;

use DiBify\DiBify\Id\Id;
use DiBify\DiBify\Model\ModelInterface;
use PHPUnit\Framework\TestCase;
use Redis;

class IdGeneratorTest extends TestCase
{

    private Redis $redis;

    private string $key;

    private IdGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->redis = new Redis();
        $this->redis->connect('127.0.0.1', 6379);
        $this->redis->select(0);

        $this->key = 'IdGenerator';
        $this->redis->del($this->key);

        $this->generator = new IdGenerator(
            $this->redis,
            $this->key
        );
    }

    public function test__invoke()
    {
        $foo_1 = $this->getFooModel();
        $this->assertFalse($foo_1->id()->isAssigned());
        $id = ($this->generator)($foo_1);
        $this->assertSame($foo_1->id(), $id);
        $this->assertSame('1', (string) $id);

        $foo_2 = $this->getFooModel();
        $this->assertFalse($foo_2->id()->isAssigned());
        $id = ($this->generator)($foo_2);
        $this->assertSame($foo_2->id(), $id);
        $this->assertSame('2', (string) $id);

        $bar_1 = $this->getBarModel();
        $this->assertFalse($bar_1->id()->isAssigned());
        $id = ($this->generator)($bar_1);
        $this->assertSame($bar_1->id(), $id);
        $this->assertSame('1', (string) $id);

        $bar_2 = $this->getBarModel();
        $this->assertFalse($bar_2->id()->isAssigned());
        $id = ($this->generator)($bar_2);
        $this->assertSame($bar_2->id(), $id);
        $this->assertSame('2', (string) $id);
    }

    public function testSetCounterValue()
    {
        $foo = $this->getFooModel();
        $this->generator->setCounterValue($foo::getModelAlias(), 100);
        $this->assertFalse($foo->id()->isAssigned());
        $id = ($this->generator)($foo);
        $this->assertSame($foo->id(), $id);
        $this->assertSame('101', (string) $id);
    }

    private function getFooModel(): ModelInterface
    {
        return new class() implements ModelInterface {

            /** @var Id */
            private $id;

            public function __construct()
            {
                $this->id = new Id();
            }

            public function id(): Id
            {
                return $this->id;
            }

            public static function getModelAlias(): string
            {
                return 'foo';
            }
        };
    }

    private function getBarModel(): ModelInterface
    {
        return new class() implements ModelInterface {

            /** @var Id */
            private $id;

            public function __construct()
            {
                $this->id = new Id();
            }

            public function id(): Id
            {
                return $this->id;
            }

            public static function getModelAlias(): string
            {
                return 'bar';
            }
        };
    }

}
