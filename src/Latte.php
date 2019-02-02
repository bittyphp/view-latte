<?php

namespace Bitty\View;

use Bitty\View\AbstractView;
use Latte\Engine;
use Latte\ILoader;
use Latte\IMacro;
use Latte\Loaders\FileLoader;

/**
 * This acts as a very basic wrapper to implement the Latte templating engine.
 *
 * If more detailed customization is needed, you can access the Latte engine
 * and the loader directly using getEngine() and getLoader(), respectively.
 *
 * @see https://latte.nette.org/
 */
class Latte extends AbstractView
{
    /**
     * @var Engine
     */
    protected $engine = null;

    /**
     * @param string $path
     * @param mixed[] $options
     */
    public function __construct(string $path, array $options = [])
    {
        $this->engine = new Engine();
        $this->engine->setLoader(new FileLoader($path));

        $optionMap = [
            'cacheDir' => 'setTempDirectory',
            'refresh' => 'setAutoRefresh',
            'contentType' => 'setContentType',
        ];

        foreach ($options as $name => $value) {
            if (!isset($optionMap[$name])) {
                throw new \InvalidArgumentException(
                    sprintf('Invalid option "%s" given.', $name)
                );
            }

            /** @var callable */
            $callable = [$this->engine, $optionMap[$name]];

            call_user_func($callable, $value);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function render(string $template, $data = []): string
    {
        return $this->engine->renderToString($template, $data);
    }

    /**
     * Renders a single block from a template using the given context data.
     *
     * @param string $template Template to render.
     * @param string $block Name of block in the template.
     * @param array $data Data to pass to template.
     *
     * @return string
     */
    public function renderBlock(string $template, string $block, array $data = []): string
    {
        return $this->engine->renderToString($template, $data, $block);
    }

    /**
     * Adds a Latte filter.
     *
     * @param string|null $name
     * @param callable $callback
     */
    public function addFilter(?string $name, callable $callback): void
    {
        $this->engine->addFilter($name, $callback);
    }

    /**
     * Gets the Latte loader.
     *
     * This allows for direct manipulation of anything not already defined here.
     *
     * @return ILoader
     */
    public function getLoader(): ILoader
    {
        return $this->engine->getLoader();
    }

    /**
     * Gets the Latte engine.
     *
     * This allows for direct manipulation of anything not already defined here.
     *
     * @return Engine
     */
    public function getEngine(): Engine
    {
        return $this->engine;
    }
}
