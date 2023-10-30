<?php

namespace Devdot\Wordpress\Plugin\BasePlugin;

abstract class Register {
    protected string $pluginName = '';
    protected string $pluginFile = '';

    /**
     * @var array<string,callable>
     */
    protected array $hooks = [];

    /**
     * @var array<string,callable|array{class-string,string}>
     */
    protected array $shortcodes = [];

    public function __construct(string $pluginFile)
    {
        $this->pluginFile = $pluginFile;
        $this->pluginName = basename($pluginFile, '.php');
    }

    public function __invoke(): void
    {
        $this->registerHooks();
        $this->registerMagicHooks();
        $this->registerShortcodes();
    }

    /**
     * @param array{class-string,string}|string $callable
     * @return callable
     */
    private function makeCallable(array|string $callable): array|string
    {
        if (is_string($callable) && method_exists($this, $callable))
            return [$this, $callable];

        if (is_callable($callable))
            return $callable;
    }

    protected function registerHooks(): void
    {
        foreach($this->hooks as $hook => $callable) {
            add_action($hook, $this->makeCallable($callable));
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

    protected function registerShortcodes(): void
    {
        foreach($this->shortcodes as $shortcode => $callable) {
            add_shortcode($shortcode, $this->makeCallable($callable));
        }
    }
}
