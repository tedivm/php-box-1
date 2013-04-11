<?php

namespace Herrera\Box\Tests;

use Herrera\Box\StubGenerator;
use Herrera\PHPUnit\TestCase;
use Phar;

class StubGeneratorTest extends TestCase
{
    /**
     * @var StubGenerator
     */
    private $generator;

    public function testAlias()
    {
        $this->generator->alias('test.phar');

        $this->assertEquals(
            'test.phar',
            $this->getPropertyValue($this->generator, 'alias')
        );
    }

    public function testCreate()
    {
        $this->assertInstanceOf(
            'Herrera\\Box\\StubGenerator',
            StubGenerator::create()
        );
    }

    public function testIndex()
    {
        $this->generator->index('index.php');

        $this->assertEquals(
            'index.php',
            $this->getPropertyValue($this->generator, 'index')
        );
    }

    public function testIntercept()
    {
        $this->generator->intercept(true);

        $this->assertTrue(
            $this->getPropertyValue(
                $this->generator,
                'intercept'
            )
        );
    }

    public function testGenerate()
    {
        $this->generator
             ->alias('test.phar')
             ->index('index.php')
             ->intercept(true)
             ->mimetypes(array('phtml' => Phar::PHPS))
             ->mung(array('REQUEST_URI'))
             ->notFound('not_found.php')
             ->rewrite('rewrite')
             ->web(true);

        $phps = Phar::PHPS;

        $this->assertEquals(
            <<<STUB
#!/usr/bin/env php
<?php

/**
 * Generated by Box.
 *
 * @link https://github.com/herrera-io/php-box/
 */
Phar::webPhar('test.phar', 'index.php', 'not_found.php', array (
  'phtml' => $phps,
), 'rewrite');
Phar::interceptFileFuncs();
Phar::mungServer(array (
  0 => 'REQUEST_URI',
));
__HALT_COMPILER();
STUB
            ,
            $this->generator->generate()
        );
    }

    /**
     * @depends testGenerate
     */
    public function testGenerateMap()
    {
        $this->generator->alias('test.phar');

        $this->assertEquals(
            <<<STUB
#!/usr/bin/env php
<?php

/**
 * Generated by Box.
 *
 * @link https://github.com/herrera-io/php-box/
 */
Phar::mapPhar('test.phar');
__HALT_COMPILER();
STUB
            ,
            $this->generator->generate()
        );
    }

    public function testMimetypes()
    {
        $map = array('php' => Phar::PHPS);

        $this->generator->mimetypes($map);

        $this->assertEquals(
            $map,
            $this->getPropertyValue($this->generator, 'mimetypes')
        );
    }

    public function testMung()
    {
        $list = array('REQUEST_URI');

        $this->generator->mung($list);

        $this->assertEquals(
            $list,
            $this->getPropertyValue($this->generator, 'mung')
        );
    }

    public function testMungInvalid()
    {
        $this->setExpectedException(
            'Herrera\\Box\\Exception\\InvalidArgumentException',
            'The $_SERVER variable "test" is not allowed.'
        );

        $this->generator->mung(array('test'));
    }

    public function testNotFound()
    {
        $this->generator->notFound('not_found.php');

        $this->assertEquals(
            'not_found.php',
            $this->getPropertyValue($this->generator, 'notFound')
        );
    }

    public function testRewrite()
    {
        $this->generator->rewrite('rewrite()');

        $this->assertEquals(
            'rewrite()',
            $this->getPropertyValue($this->generator, 'rewrite')
        );
    }

    public function testWeb()
    {
        $this->generator->web(true);

        $this->assertTrue($this->getPropertyValue($this->generator, 'web'));
    }

    protected function setUp()
    {
        $this->generator = new StubGenerator();
    }
}
