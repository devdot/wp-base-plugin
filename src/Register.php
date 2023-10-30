<?php

namespace Devdot\Wordpress\Plugin\BasePlugin;

abstract class Register {
    private static string $plugin_name = ''; 

    /**
     * @var array<string,callable>
     */
    private array $hooks = [];

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
