<?php

namespace Fsmdev\LaravelPageAttributes\Models;


use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

class PageAttributes
{
    protected $context = null;
    protected $contextInit = false;

    protected $attributes = [];
    protected $attributesContext = [];
    protected $attributesDefault = [];

    /**
     * PageAttributes constructor.
     */
    public function __construct()
    {
        $this->attributesDefault = config('page_attributes.default');
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return void
     */
    public function set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function get($name)
    {
        $this->contextInit();

        # Attribute priority: 1) set; 2) context; 3) default;

        $value = isset($this->attributes[$name]) ? $this->attributes[$name] : null;

        if (!$value && isset($this->attributesContext[$name])) {
            $value = $this->attributesContext[$name];
        }

        if (!$value && isset($this->attributesDefault[$name])) {
            $value = $this->attributesDefault[$name];
        }

        # Canonical URL by set parameters string
        if ($name === 'canonical') {
            $value = $this->getCanonical($value);
        }

        return $value;
    }

    /**
     * @param string $name
     * @return string
     */
    public function html($name)
    {
        $html = '';
        $value = $this->get($name);

        if ($value) {

            $htmlTemplates = [
                'title' => '<title>{value}</title>',
                'description' => '<meta name="description" content="{value}"/>',
                'keywords' => '<meta name="keywords" content="{value}"/>',
                'h1' => '<h1>{value}</h1>',
                'charset' => '<meta charset="{value}">',
                'viewport' => '<meta name="viewport" content="{value}">',
                'canonical' => '<link rel="canonical" href="{value}"/>',
            ];

            $htmlTemplates = array_merge($htmlTemplates, config('page_attributes.html_templates'));

            if (isset($htmlTemplates[$name])) {
                $html = strtr($htmlTemplates[$name], ['{value}' => $value]);
            } else {
                $html = $value;
            }
        }

        return $html;
    }

    /**
     * @param null $context
     */
    public function context($context = null)
    {
        $this->context = $context;
        $this->contextInit = false;
        $this->attributesContext = [];
    }

    /**
     * Init attributes array by context
     */
    protected function contextInit()
    {
        if ($this->contextInit || !$this->context) {
            return;
        }

        $this->contextInit = true;

        $pageAttributes = PageAttribute::where('context', $this->context);

        if (config('page_attributes.multi_language')) {
            $pageAttributes->where('language', App::getLocale());
        }

        $this->attributesContext = $pageAttributes->pluck('value', 'name')->toArray();
    }

    /**
     * @param string $value
     * @return string
     */
    protected function getCanonical($value)
    {
        $params = explode(',', $value);

        asort($params);
        $get = [];

        foreach($params as $param) {
            if (isset($_GET[$param])) {
                $get[] = $param.'='.$_GET[$param];
            }
        }

        return URL::current().(count($get) ? '?'.implode('&', $get) : '');
    }
}
