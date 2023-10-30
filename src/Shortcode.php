<?php

namespace Devdot\Wordpress\Plugin\BasePlugin;

abstract class Shortcode {
    public function __invoke(array $attributes = [], ?string $content = null, string $name = ''): string
    {
        return $this->render($attributes, $content);
    }

    abstract protected function render(array $attributes = [], ?string $content = null): string;
}
