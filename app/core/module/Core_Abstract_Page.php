<?php

abstract class Core_Abstract_Page implements Core_Page_Interface {

    /**
     * @var Core_Module_Interface Parent module
     */
    protected $parent_module = null;

    /**
     * @var Core_Page_Interface Parent page
     */
    protected $parent_page = null;

    /**
     * @var array Query parameters
     */
    protected $request = array();

    /**
     * @var Core_Page_Builder GUI Builder
     */
    protected $gui_builder = null;

    /**
     * @var Core_Javascript_Builder AngularJS Builder
     */
    protected $js_builder = null;

    /**
     * @var string Page name
     */
    protected $name = '';

    /**
     * @var string Title
     */
    protected $title = '';

    /**
     * @var string Description
     */
    protected $description;

    /**
     * Core_Abstract_Module_Page constructor.
     * @param Core_Module_Interface $parent_module Attached parent module
     * @param string $name Page name
     * @param string $title Page title
     * @param string $description Page subtitle
     */
    public function __construct($parent_module, $name = '', $title = '', $description = '')
    {
        $this->parent_module = $parent_module;

        $this->name = $name;
        $this->title = $title;
        $this->description = $description;

        $this->gui_builder = new Core_Page_Builder($this);
        $this->js_builder = new Core_Javascript_Builder($this);
    }

    /**
     * @param array $query_args Query arguments necessary to page rendering
     */
    public function render($query_args = array()) {

        // Set request parameters
        $this->request = $query_args;

        // Build Page Header
        $this->gui_builder->buildHeader();

        // Render Page Body
        $this->body();

        // Generate AngularJS Controller
        $this->js_builder->buildNGController();

        // Build Page Footer
        $this->gui_builder->buildFooter();
    }

    public function getPageTitle() {
        return $this->title;
    }

    public function getPageDescription()
    {
        return $this->description;
    }

    public function getParent()
    {
        return $this->parent_page;
    }

    public function setParent($parent_page = null)
    {
        if ($parent_page != null && is_a($parent_page, 'Core_Page_Interface')) {
            $this->parent_page = $parent_page;
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getModuleTitle()
    {
        return $this->parent_module->getModuleTitle();
    }

    public function getStylesheets()
    {
        return $this->parent_module->getStylesheets();
    }

    public function getJavascript()
    {
        return $this->parent_module->getJavascript();
    }

    public function getOptions()
    {
        return $this->parent_module->getOptions();
    }

    public function getModuleHRef()
    {
        return $this->parent_module->getHRef();
    }

    public function getHRef()
    {
        return $this->getModuleHRef() . '/' . $this->getName();
    }

    public function getIcon()
    {
        return $this->parent_module->getIcon();
    }
}
