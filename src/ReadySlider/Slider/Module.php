<?php


class ReadySlider_Slider_Module extends ReadySlider_Core_BaseModule
{

    /** Config item with the available sliders. */
    const AVAILABLE_SLIDERS = 'plugin_sliders';

    /**
     * Module initialization.
     */
    public function onInit()
    {
        /** @var ReadySlider_Slider_Controller $controller */
        $controller = $this->getController();
        $dispatcher = $this->getEnvironment()->getDispatcher();

        // Loads module assets.
        $dispatcher->on('after_ui_loaded', array($this, 'loadAssets'));

        // Find all sliders after all modules has been loaded.
        $dispatcher->on('after_modules_loaded', array($this, 'findSliders'));

        // Load twig extensions.
        $dispatcher->on('after_modules_loaded', array($this, 'loadExtensions'));

        // If one of the photo will be removed from database
        // we'll remove it from the slider automatically.
        $dispatcher->on(
            'photos_delete_by_id',
            array($controller->getModel('resources'), 'deletePhotoById')
        );

        // Add shortcode
        add_shortcode('ready-slider', array($this, 'render'));

        // Register menu items.
        $dispatcher->on(
            'ui_menu_items',
            array($this, 'addNewSliderMenuItem'),
            1,
            0
        );
    }

    /**
     * Loads module assets.
     * @param ReadySlider_Ui_Module $ui UI Module.
     */
    public function loadAssets(ReadySlider_Ui_Module $ui)
    {
        $environment = $this->getEnvironment();
        $preventCaching = $environment->isDev();

        $ui->add(
            new ReadySlider_Ui_Javascript(
                'readySlider-slider-frontend',
                $this->getLocationUrl() . '/assets/js/frontend.js',
                $preventCaching
            )
        );

        if (!$environment->isModule('slider')) {
            return;
        }

        $ui->add(
            new ReadySlider_Ui_BackendStylesheet(
                'readySlider-slider-styles',
                $this->getLocationUrl() . '/assets/css/slider.css',
                $preventCaching
            )
        );

        if ($environment->isAction('index')) {
            $ui->add(
                new ReadySlider_Ui_BackendJavascript(
                    'readySlider-slider-index',
                    $this->getLocationUrl() . '/assets/js/index.js',
                    $preventCaching
                )
            );
        }

        if ($environment->isAction('add')) {
            $ui->add(
                new ReadySlider_Ui_BackendJavascript(
                    'readySlider-slider-add',
                    $this->getLocationUrl() . '/assets/js/add.js',
                    $preventCaching
                )
            );
        }

        if ($environment->isAction('view')) {
            $ui->add(
                new ReadySlider_Ui_BackendStylesheet(
                    'wp-color-picker'
                )
            );

            $ui->add(
                new ReadySlider_Ui_BackendJavascript(
                    'readySlider-slider-frontend',
                    $this->getLocationUrl() . '/assets/js/frontend.js',
                    $preventCaching
                )
            );

            $ui->add(
                new ReadySlider_Ui_BackendJavascript(
                    'readySlider-slider-settingsTabs',
                    $this->getLocationUrl() . '/assets/js/settings-tabs.js',
                    $preventCaching
                )
            );

            $ui->add(
                new ReadySlider_Ui_BackendJavascript(
                    'readySlider-slider-settings',
                    $this->getLocationUrl() . '/assets/js/settings.js',
                    $preventCaching
                )
            );

            $ui->add(
                new ReadySlider_Ui_BackendJavascript(
                    'readySlider-slider-view',
                    $this->getLocationUrl() . '/assets/js/view.js',
                    $preventCaching
                )
            );

            $ui->add(
                new ReadySlider_Ui_BackendJavascript(
                    'readySlider-slider-viewToolbar',
                    $this->getLocationUrl() . '/assets/js/view-toolbar.js',
                    $preventCaching
                )
            );

            $ui->add(
                new ReadySlider_Ui_BackendJavascript(
                    'readySlider-slider-sorting',
                    $this->getLocationUrl() . '/assets/js/sorting.js',
                    $preventCaching
                )
            );

            $ui->add(
                new ReadySlider_Ui_BackendJavascript(
                    'readySlider-slider-preview',
                    $this->getLocationUrl() . '/assets/js/preview.js',
                    $preventCaching
                )
            );

            // Visual Editor.
            $veditor = new ReadySlider_Ui_BackendJavascript(
                'readySlider-slider-veditor',
                $this->getLocationUrl() . '/assets/js/visual-editor.js',
                $preventCaching
            );

            $veditor->setDeps(array('wp-color-picker'));

            $ui->add($veditor);
        }
    }

    /**
     * Loads Twig extensions.
     */
    public function loadExtensions()
    {
        $twig = $this->getEnvironment()->getTwig();

        $twig->addExtension(new ReadySlider_Slider_Twig_Attachment());
    }

    public function render($attributes)
    {
        if (!isset($attributes['id'])) {
            // @TODO: Maybe we need to show error message here.
            return;
        }

        /** @var string|int $id */
        $id = $attributes['id'];
        /** @var ReadySlider_Slider_Controller $controller */
        $controller = $this->getController();
        /** @var ReadySlider_Slider_Model_Sliders $sliders */
        $sliders = $controller->getModel('sliders');
        $slider  = $sliders->getById((int)$id);

        if (!$slider) {
            // @TODO: Maybe we need to show error message here.
            return;
        }

        if (isset($attributes['width'])) {
            $slider->settings['properties']['width'] = $attributes['width'];
        }

        if (isset($attributes['height'])) {
            $slider->settings['properties']['height'] = $attributes['height'];
        }

        /** @var ReadySlider_Slider_Interface $module */
        $module = $this->getEnvironment()->getModule($slider->plugin);

        if (!$module) {
            return;
        }

        add_action('wp_footer', array($module, 'enqueueJavascript'));
        add_action('wp_footer', array($module, 'enqueueStylesheet'));

        return $module->render($slider);
    }

    /**
     * Finds all modules that implement ReadySlider_Slider_Interface
     * and registers as sliders.
     */
    public function findSliders()
    {
        $environment = $this->getEnvironment();

        $config  = $environment->getConfig();
        $modules = $environment
            ->getResolver()
            ->getModules();

        if (!$config->has(self::AVAILABLE_SLIDERS)) {
            $config->add(self::AVAILABLE_SLIDERS, array());
        }

        $available = $config->get(self::AVAILABLE_SLIDERS);

        if ($modules->isEmpty()) {
            return;
        }

        foreach ($modules as $module) {
            if ($module instanceof ReadySlider_Slider_Interface) {
                $available[] = $module;
            }
        }

        $config->set(self::AVAILABLE_SLIDERS, $available);
    }

    /**
     * Returns array with the available sliders.
     *
     * @return ReadySlider_Slider_Info[]
     */
    public function getAvailableSliders()
    {
        return $this->getEnvironment()
            ->getConfig()
            ->get(
                self::AVAILABLE_SLIDERS,
                array()
            );
    }

    public function addNewSliderMenuItem()
    {
        $menu = $this->getEnvironment()->getMenu();

        $submenu = $menu->createSubmenuItem();
        $submenu->setCapability('manage_options')
            ->setMenuSlug('ready-slider-new')
            ->setMenuTitle('New slider')
            ->setPageTitle('New slider')
            ->setModuleName('slider');

        $menu->addSubmenuItem('newSlider', $submenu);
//            ->register();
    }
}
