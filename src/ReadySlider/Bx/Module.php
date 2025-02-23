<?php

/**
 * Adds the bxSlider to the Ready Slider plugin.
 */
class ReadySlider_Bx_Module extends Rsc_Mvc_Module implements ReadySlider_Slider_Interface
{

    const OPT_TRUE = 'true';
    const OPT_FALSE = 'false';

    /**
     * Module initialization.
     */
    public function onInit()
    {
        $dispatcher = $this->getEnvironment()->getDispatcher();

        // Load module assets.
        $dispatcher->on('after_ui_loaded', array($this, 'loadAssets'));
    }

    /**
     * Loads module assets.
     *
     * @param ReadySlider_Ui_Module $ui UI module.
     */
    public function loadAssets(ReadySlider_Ui_Module $ui)
    {
        $environment    = $this->getEnvironment();
        $preventCaching = $environment->isDev();

        if (!$environment->isModule('slider')) {
            return;
        }

        if ($environment->isAction('view')) {
            $ui->add(
                new ReadySlider_Ui_BackendStylesheet(
                    'readySlider-bx-css',
                    $this->getLocationUrl() . '/assets/css/jquery.bxslider.css',
                    $preventCaching
                )
            );

            $ui->add(
                new ReadySlider_Ui_BackendJavascript(
                    'readySlider-bx-fv',
                    $this->getLocationUrl() . '/assets/plugins/jquery.fitvids.js',
                    $preventCaching
                )
            );

            $ui->add(
                new ReadySlider_Ui_BackendJavascript(
                    'readySlider-bx-js',
                    $this->getLocationUrl() . '/assets/js/jquery.bxslider.js',
                    $preventCaching
                )
            );

            $ui->add(
                new ReadySlider_Ui_BackendJavascript(
                    'readySlider-bx-preview',
                    $this->getLocationUrl() . '/assets/js/frontend.js',
                    $preventCaching
                )
            );
        }
    }

    /**
     * Returns default slider settings.
     *
     * @return array
     */
    public function getDefaults()
    {
        return array(
            'general'    => array(
                'mode'        => 'horizontal',
                'speed'       => 500,
                'slideMargin' => 0,
                'captions'    => self::OPT_TRUE,
                'easing'      => 'linear',
            ),
            'touch'      => array(
                'enabled'        => self::OPT_TRUE,
                'oneToOne'       => self::OPT_TRUE,
                'swipeThreshold' => 55,
            ),
            'pager'      => array(
                'enabled' => self::OPT_FALSE,
                'type'    => 'full',
            ),
            'properties' => array(
                'width'  => 640,
                'height' => 240,
            ),
        );
    }

    /**
     * Returns slider name.
     *
     * @return string
     */
    public function getSliderName()
    {
        return 'BxSlider';
    }

    /**
     * Enqueue javascript.
     */
    public function enqueueJavascript()
    {
        wp_enqueue_scripts('jquery');

        // Allows to load assets without overloading.
        $dispatcher = $this->getEnvironment()->getDispatcher();
        $dispatcher->dispatch('bx_enqueue_javascript');

        wp_enqueue_script(
            'readySlider-bxSliderPlugin',
            $this->getLocationUrl() . '/assets/js/jquery.bxslider.js',
            array(),
            '1.0.0',
            true
        );

        wp_enqueue_script(
            'readySlider-bxSlider',
            $this->getLocationUrl() . '/assets/js/frontend.js',
            array(),
            '1.0.0',
            true
        );
    }

    /**
     * Enqueue stylesheet.
     */
    public function enqueueStylesheet()
    {
        wp_enqueue_style(
            'readySlider-bxSliderStyles',
            $this->getLocationUrl() . '/assets/css/jquery.bxslider.css',
            array(),
            '1.0.0',
            'all'
        );
    }

    /**
     * Is this slider available to use in free version.
     *
     * @return bool
     */
    public function isFree()
    {
        return true;
    }

    /**
     * Renders specified slider and returns markup.
     *
     * @param object $slider Slider.
     * @return string
     */
    public function render($slider)
    {
        return $this->getEnvironment()
            ->getTwig()
            ->render(
                $this->getSliderTemplate(),
                array(
                    'slider' => $slider,
                )
            );
    }

    /**
     * Returns path to the settings template.
     *
     * @return string
     */
    public function getSettingsTemplate()
    {
        return '@bx/settings.twig';
    }

    /**
     * Returns path to the template which be used with short code.
     *
     * @return string
     */
    public function getSliderTemplate()
    {
        return '@bx/markup.twig';
    }
}