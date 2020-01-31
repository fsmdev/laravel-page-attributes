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

    protected $variables = [];
    protected $variableOpen;
    protected $variableClose;

    /**
     * PageAttributes constructor.
     */
    public function __construct()
    {
        $this->attributesDefault = config('page_attributes.default');
        $this->variables = config('page_attributes.default_variables');
        $this->variableOpen = config('page_attributes.variable_open');
        $this->variableClose = config('page_attributes.variable_close');
    }

    /**
     * @param string|array $name
     * @param string $value
     *
     * @return void
     */
    public function set($name, $value)
    {
        if (!is_array($name)) {
            $name = [(string)$name];
        }
        foreach ($name as $string) {
            $this->attributes[$string] = $value;
        }
    }

    /**
     * @param string $name
     * @return string|null
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

        # Replace variables
        $value = strtr($value, $this->prepareVariables());

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
     * @param integer|null $context
     * @param array|null $variables
     */
    public function context($context = null, $variables = [])
    {
        $this->context = $context;
        $this->contextInit = false;
        $this->attributesContext = [];

        if (is_array($variables)) {
            $this->variables($variables);
        }
    }

    /**
     * @param array $variables
     */
    public function variables(array $variables)
    {
        foreach ($variables as $key => $value) {
            $this->variable($key, $value);
        }
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function variable($name, $value)
    {
        $this->variables[trim($name)] = (string)$value;
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

    /**
     * @return array
     */
    protected function prepareVariables()
    {
        $result = [];
        foreach ($this->variables as $key => $value) {
            $result[$this->variableOpen.$key.$this->variableClose] = $value;
        }
        return $result;
    }
}
