<?php

/**
 * Coin-Slider module.
 *
 * Allows to use Coin-Slider as Ready! Slider module.
 */
class ReadySlider_Coin_Module extends Rsc_Mvc_Module /*implements ReadySlider_Slider_Interface*/
{

    const OPT_TRUE  = 'true';
    const OPT_FALSE = 'false';

    /**
     * Module initialization.
     * Loads assets and registers current module as slider.
     */
    public function onInit()
    {
        $dispatcher = $this->getEnvironment()->getDispatcher();

        // Load module assets.
        $dispatcher->on('after_ui_loaded', array($this, 'loadAssets'));
    }

    /**
     * Loads plugin assets.
     *
     * @param ReadySlider_Ui_Module $ui
     */
    public function loadAssets(ReadySlider_Ui_Module $ui)
    {
        $environment = $this->getEnvironment();

        // Disallow to cache assets in development environment.
        $preventCaching = $environment->isDev();

        // Load assets only on plugin pages.
        if (!$environment->isPluginPage()) {
            return;
        }

        $ui->add(
            new ReadySlider_Ui_BackendJavascript(
                'readySlider-coin-js',
                $this->getLocationUrl() . '/assets/js/coin-slider.min.js',
                $preventCaching
            )
        );

        $ui->add(
            new ReadySlider_Ui_BackendStylesheet(
                'readySlider-coin-style',
                $this->getLocationUrl() . '/assets/css/coin-slider-styles.css',
                $preventCaching
            )
        );

        if ($environment->isModule('slider', 'view')) {
            $ui->add(
                new ReadySlider_Ui_BackendJavascript(
                    'readySlider-coin-settings',
                    $this->getLocationUrl() . '/assets/js/settings.js',
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
            'effects'  => array(
                'effect'     => 'random',
                'titleSpeed' => 500,
                'opacity'    => 0.7,
                'delay'      => 3000,
                'hoverPause' => self::OPT_TRUE,
            ),
            'controls' => array(
                'navigation' => self::OPT_TRUE,
                'links'      => self::OPT_FALSE,
            ),
            'properties' => array(
                'width'  => 640,
                'height' => 240,
            ),
        );
    }

    /**
     * Renders specified slider and returns markup.
     *
     * @param object $slider Slider.
     * @return string
     */
    public function render($slider)
    {
        $twig = $this->getEnvironment()->getTwig();

        return $twig->render(
            '@coin/markup.twig',
            array(
                'slider' => $slider
            )
        );
    }

    /**
     * Returns slider name.
     *
     * @return string
     */
    public function getSliderName()
    {
        return 'Coin Slider';
    }

    /**
     * Enqueue javascript.
     */
    public function enqueueJavascript()
    {
        wp_enqueue_script('jquery');

        wp_enqueue_script(
            'readySlider-coinSliderPlugin',
            $this->getLocationUrl() . '/assets/js/coin-slider.min.js',
            array(),
            '1.0.0',
            true
        );

        wp_enqueue_script(
            'readySlider-coinSlider',
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
            'readySlider-coinSliderPluginStyles',
            $this->getLocationUrl() . '/assets/css/coin-slider-styles.css',
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
}