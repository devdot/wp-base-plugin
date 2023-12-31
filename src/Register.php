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
     * @var array<string,Shortcode|callable|array{class-string,string}>
     */
    protected array $shortcodes = [];

    /**
     * @var array<int,class-string<AdminPage>>
     */
    protected array $adminPages = [];

    public function __construct(string $pluginFile)
    {
        $this->pluginFile = $pluginFile;
        $this->pluginName = basename($pluginFile, '.php');
    }

    public function __invoke(): void
    {
        $this->registerMagicHooks();
        $this->registerShortcodes();
        $this->registerAdminPages();

        $this->registerHooks();
    }

    /**
     * @param array{class-string,string}|string $callable
     * @return callable
     */
    private function makeCallable(array|string $callable): callable
    {
        if (is_string($callable) && method_exists($this, $callable))
            return [$this, $callable];

        if (is_callable($callable))
            return $callable;

        throw new \Exception('Failed to create a callback!');
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
            if(is_subclass_of($callable, Shortcode::class)) {
                add_shortcode($shortcode, new $callable);
            }
            else {
                add_shortcode($shortcode, $this->makeCallable($callable));
            }
        }
    }

    protected function registerAdminPages(): void
    {
        if (count($this->adminPages) && !isset($this->hooks['admin_menu'])) {
            $this->hooks['admin_menu'] = [$this, '__callbackRegisterAdminPages'];
        }
    }

    public function __callbackRegisterAdminPages(): void
    {
        foreach($this->adminPages as $class) {
            $page = new $class($this->pluginName);

            if (!$page->hasParent()) {
                add_menu_page($page->pageTitle, $page->menuTitle, $page->capability, $page->slug, $page, $page->iconUrl, $page->position);
            }
            else {
                $parent = new ($class::$parent)($this->pluginName);
                add_submenu_page($parent->slug, $page->pageTitle, $page->menuTitle, $page->capability, $parent->slug . '-' . $page->slug, $page, $page->position);
            }
        }
    }
}
