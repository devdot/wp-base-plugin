<?php

namespace Devdot\Wordpress\Plugin\BasePlugin;

abstract class AdminPage
{

    const CAPABILITY_EDIT_PAGES = 'edit_pages';
    const CAPABILITY_EDIT_POSTS = 'edit_posts';
    const CAPABILITY_MANAGE_OPTIONS = 'manage_options';

    /**
     * @var class-string
     */
    public static ?string $parent = null;
    public string $slug;
    public string $capability;
    public string $pageTitle;
    public string $menuTitle;
    public ?int $position = null;
    public string $iconUrl = '';

    public function __construct(
        private string $pluginSlug,
    ) {
        // deal with autosetting the slug
        if (!isset($this->slug) || empty($this->slug)) {
            if ($this->hasParent()) {
                $this->slug = strtolower((new \ReflectionClass($this))->getShortName());
            }
            else {
                $this->slug = $this->pluginSlug;
            }
        }

        $this->capability ??= self::CAPABILITY_EDIT_POSTS;
        $this->pageTitle ??= $this->pluginSlug;
        $this->menuTitle ??= $this->pluginSlug;
    }

    public function __invoke(...$args): string
    {
        $query = [];
        parse_str($_SERVER['QUERY_STRING'], $query);

        $data = [];
        parse_str(file_get_contents('php://input'), $data);

        echo '<div>';
        echo $this->render($query, $data);
        echo '</div>';
        return '';
    }

    abstract protected function render(array $query = [], array $data = []): string;

    public function hasParent(): bool
    {
        return static::$parent !== null;
    }
}
