<?php

/**
 * Class GirdGallery_Installer_Module
 */
class ReadySlider_Installer_Module extends ReadySlider_Core_Module
{
    const LAST_REVISION = 'ready_slider_last_revision';

    /**
     * @var GirdGallery_Installer_Model
     */
    protected $model;

    /**
     * {@inheritdoc}
     */
    public function onInit()
    {
        $config    = $this->getEnvironment()->getConfig();

        // Current revision.
        $revision  = $config->get('revision');

        // Installed revision.
        $installed = null;

        if (!$installed = get_option(self::LAST_REVISION)) {
            $installed = $revision;
            update_option(self::LAST_REVISION, $installed);

            self::executeUpdate($revision);
        }

        if (version_compare($current, $installed, '>')) {
            self::executeUpdate($revision);

            update_option(self::LAST_REVISION, $current);
        }
    }

    /**
     * {@inhertidoc}
     */
    public function onInstall()
    {
        parent::onInstall();

        $model   = self::getModel();
        $queries = self::getSchema();

        if (!$model->install($queries)) {
            wp_die ('Failed to update database.');
        }
    }

    public function onDeactivation()
    {
        $response = $this->getController()->askUninstallAction();

        if (!is_bool($response)) {
            exit($response);
        }

        if ($response) {
            $model   = self::getModel();
            $queries = self::getSchema();

            $model->drop($queries);
        }
    }

    protected static function executeUpdate($revision)
    {
        self::getModel()->update(self::getUpdates($revision));
    }

    /**
     * Returns the database schema queries.
     * @return array|null
     */
    protected static function getSchema()
    {
        if (!is_file($file = dirname(__FILE__) . '/Schema.php')) {
            return null;
        }

        return include $file;
    }

    protected static function getUpdates($revision)
    {
        $filename = sprintf('/Updates/rev_%d.php', (int)$revision);

        if (!is_file($file = dirname(__FILE__) . $filename)) {
            return null;
        }

        return include $file;
    }

    protected static function getModel()
    {
        return new ReadySlider_Installer_Model();
    }

}
