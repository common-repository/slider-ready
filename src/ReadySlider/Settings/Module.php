<?php

/**
 * Class ReadySlider_Settings_Module
 * User settings module
 *
 * @package ReadySlider\Settings
 * @author Artur Kovalevsky
 */
class ReadySlider_Settings_Module extends Rsc_Mvc_Module
{

    /**
     * @var ReadySlider_Settings_Registry
     */
    private $registry;

    /**
     * Returns the Settings Registry
     *
     * @param ReadySlider_Settings_SettingsStorageInterface $storage
     * @return ReadySlider_Settings_Registry
     */
    public function getRegistry(ReadySlider_Settings_SettingsStorageInterface $storage = null)
    {
        if ($this->registry === null) {
            $this->registry = new ReadySlider_Settings_Registry(
                $this->getEnvironment()->getConfig()->get('hooks_prefix'),
                $storage
            );
        }

        return $this->registry;
    }

    /**
     * {@inheritdoc}
     */
    public function onInit()
    {
        $prefix = $this->getEnvironment()->getConfig()->get('hooks_prefix');

        add_action($prefix . 'after_ui_loaded', array(
            $this, 'loadAssets'
        ));

        $dispatcher = $this->getEnvironment()->getDispatcher();
        $dispatcher->on(
            'ui_menu_items',
            array($this, 'registerMenuItem'),
            3,
            0
        );
    }

    public function onInstall()
    {
        parent::onInstall();

        $registry = $this->getRegistry();
        $registry->set('send_stats', 1);
    }

    /**
     * Loads the assets required by the module
     */
    public function loadAssets(ReadySlider_Ui_Module $ui)
    {
        $ui->add(new ReadySlider_Ui_BackendJavascript(
            'gg-settings-save-js',
            $this->getLocationUrl() . '/assets/js/gird-gallery.settings.clearCache.js'
        ));
    }

    public function registerMenuItem()
    {
        $menu = $this->getEnvironment()->getMenu();
        $submenu = $menu->createSubmenuItem();
        $submenu->setCapability('manage_options')
            ->setMenuSlug('gird-gallery-settings')
            ->setMenuTitle('Settings')
            ->setPageTitle('Settings')
            ->setModuleName('settings');

        $menu->addSubmenuItem('settings', $submenu);
    }
}