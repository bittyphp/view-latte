<?php

namespace Bitty\Tests\View;

use Bitty\View\AbstractView;
use Bitty\View\Latte;
use Latte\Engine;
use Latte\ILoader;
use Latte\IMacro;
use Latte\Loaders\FileLoader;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class LatteTest extends TestCase
{
    /**
     * @var Latte
     */
    private $fixture = null;

    /**
     * @var vfsStreamDirectory
     */
    private $root = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = vfsStream::setup();

        $this->fixture = new Latte(
            __DIR__.'/templates/',
            ['cacheDir' => $this->root->url()]
        );
    }

    public function testInstanceOf(): void
    {
        self::assertInstanceOf(AbstractView::class, $this->fixture);
    }

    public function testCacheDir(): void
    {
        $path = uniqid();
        $dir  = $this->root->url().'/'.$path;

        $this->fixture = new Latte(
            __DIR__.'/templates',
            ['cacheDir' => $dir]
        );

        $this->fixture->render('test.latte', ['name' => uniqid()]);

        $actual = array_diff(scandir($dir) ?: [], ['..', '.']);

        self::assertNotEmpty($actual);
    }

    public function testOptions(): void
    {
        try {
            new Latte(
                uniqid(),
                [
                    'refresh' => rand(0, 1),
                    'cacheDir' => uniqid(),
                    'contentType' => uniqid(),
                ]
            );
        } catch (\Throwable $e) {
            self::fail($e->getMessage());
        }

        self::assertTrue(true);
    }

    public function testInvalidOption(): void
    {
        $name = uniqid();

        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('Invalid option "'.$name.'" given.');

        new Latte(uniqid(), [$name => uniqid()]);
    }

    /**
     * @param string $template
     * @param mixed[] $data
     * @param string $expected
     *
     * @dataProvider sampleRender
     */
    public function testRender(string $template, array $data, string $expected): void
    {
        $actual = $this->fixture->render($template, $data);

        self::assertEquals($expected, $actual);
    }

    public function sampleRender(): array
    {
        $name = uniqid('name');

        return [
            'simple' => [
                'template' => 'test.latte',
                'data' => ['name' => $name],
                'expected' => 'Hello, '.$name.PHP_EOL.PHP_EOL.'Goodbye, '.$name.PHP_EOL,
            ],
            'nested' => [
                'template' => 'parent/test.latte',
                'data' => ['name' => $name],
                'expected' => 'Hello, '.$name.', from parent'.PHP_EOL,
            ],
            'multiple nested' => [
                'template' => 'parent/child/test.latte',
                'data' => ['name' => $name],
                'expected' => 'Hello, '.$name.', from parent/child'.PHP_EOL,
            ],
        ];
    }

    public function testRenderBlock(): void
    {
        $name = uniqid('name');

        $actual = $this->fixture->renderBlock('test.latte', 'hello', ['name' => $name]);

        self::assertEquals('Hello, '.$name.PHP_EOL, $actual);
    }

    public function testAddFilter(): void
    {
        $name     = uniqid('a');
        $expected = uniqid('b');
        $this->fixture->addFilter('testFilter', function ($value) use ($expected) {
            return $expected.$value;
        });

        $actual = $this->fixture->render('filter.latte', ['name' => $name]);

        self::assertEquals('Hello, '.$expected.$name.PHP_EOL, $actual);
    }

    public function testAddNullFilter(): void
    {
        try {
            $this->fixture->addFilter(null, function (): void {
            });
        } catch (\Throwable $e) {
            self::fail($e->getMessage());
        }

        self::assertTrue(true);
    }

    public function testGetLoader(): void
    {
        $actual = $this->fixture->getLoader();

        self::assertInstanceOf(ILoader::class, $actual);
    }

    public function testGetEngine(): void
    {
        $actual = $this->fixture->getEngine();

        self::assertInstanceOf(Engine::class, $actual);
    }
}
