<?php

namespace Devdot\Wordpress\Plugin\BasePlugin;

abstract class Register {
    abstract private string $plugin_name; 

    /**
     * @var array<string,callable>
     */
    abstract private array $hooks;

    public function __invoke(): void
    {
        $this->registerHooks();
    }

    protected function registerHooks(): void
    {
        foreach($this->hooks as $hook => $callable) {
            add_action( $hook, $callable);
        }
    }
}
