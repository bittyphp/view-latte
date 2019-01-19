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

        if (!empty($options['cacheDir'])) {
            $this->engine->setTempDirectory($options['cacheDir']);
        } else {
            $this->engine->setTempDirectory(sys_get_temp_dir());
        }
        if (isset($options['refresh'])) {
            $this->engine->setAutoRefresh((bool) $options['refresh']);
        }
        if (!empty($options['contentType'])) {
            $this->engine->setContentType($options['contentType']);
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
