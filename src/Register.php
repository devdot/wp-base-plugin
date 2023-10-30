<?php

namespace Devdot\Wordpress\Plugin\BasePlugin;

abstract class Register {
    protected string $pluginName = '';
    protected string $pluginFile = '';

    /**
     * @var array<string,callable>
     */
    protected array $hooks = [];

    public function __construct(string $pluginFile)
    {
        $this->pluginFile = $pluginFile;
        $this->pluginName = basename($pluginFile, '.php');
    }

    public function __invoke(): void
    {
        $this->registerHooks();
        $this->registerMagicHooks();
    }

    protected function registerHooks(): void
    {
        foreach($this->hooks as $hook => $callable) {
            add_action($hook, $callable);
        }
    }

    private function registerMagicHooks(): void
    {
        if(method_exists($this, 'activate')) {
            register_activation_hook($this->pluginFile, [$this, 'activate']);
        }

        if(method_exists($this, 'deactivate')) {
            register_deactivation_hook($this->pluginFile, [$this, 'deactivate']);
        }
    }
}
