<?php

/**
 * Class ReadySlider
 */
class ReadySlider
{
    /**
     * @var Rsc_Environment
     */
    private $environment;

    /**
     * @var array
     */
    private $alerts;

    /**
     * Constructor
     */
    public function __construct()
    {
        if (!class_exists('Rsc_Autoloader', false)) {
            require dirname(dirname(__FILE__)) . '/vendor/Rsc/Autoloader.php';
            Rsc_Autoloader::register();
        }

        /* Create new plugin instance */
        $pluginPath  = dirname(dirname(__FILE__));
        $environment = new Rsc_Environment('rsl', '0.3.7', $pluginPath);

        /* Configure */
        $environment->configure(
            array(
                'environment'      => $this->getPluginEnvironment(),
                'default_module'   => 'slider',
                'default_slider'   => 'bx',
                'lang_domain'      => 'ready-slider',
                'lang_path'        => plugin_basename(dirname(__FILE__)) . '/langs',
                'plugin_prefix'    => 'ReadySlider',
                'plugin_source'    => dirname(dirname(__FILE__)) . '/src',
                'plugin_menu'      => array(
                    'page_title' => __('Ready! Slider', 'ready-slider'),
                    'menu_title' => __('Ready! Slider', 'ready-slider'),
                    'capability' => 'manage_options',
                    'menu_slug'  => 'ready-slider',
                    'icon_url'   => 'dashicons-format-gallery',
                    'position'   => '100.4',
                ),
                'shortcode_name'   => 'ready-slider',
                'db_prefix'        => 'rs_',
                'hooks_prefix'     => 'rs_',
                'ajax_url'         => admin_url('admin-ajax.php'),
                'ajax_action'      => 'ready-slider',
                'admin_url'        => admin_url(),
                'uploads_rw'       => true,
                'jpeg_quality'     => 95,
                'plugin_db_update' => true,
                'revision'         => 29
            )
        );

        $this->environment = $environment;
        $this->alerts      = array();

        $this->initialize();
    }

    /**
     * Run plugin
     */
    public function run()
    {
        $this->environment->run();
        $this->environment->getTwig()->addGlobal('core_alerts', $this->alerts);
    }

    protected function initialize()
    {
        $config = $this->environment->getConfig();
        $logger = null;

        $uploads = wp_upload_dir();

        if (!is_writable($uploads['basedir'])) {
            $this->alerts[] = sprintf(
                '<div class="error">
                    <p>You need to make your "%s" directory writable.</p>
                </div>',
                $uploads['basedir']
            );

            $config->set('uploads_rw', false);
        }

        /* Create the plugin directories if they are does not exists yet. */
        $this->initFilesystem();

        /* Initialize cache null-adapter by default */
        $cacheAdapter = new Rsc_Cache_Dummy();

        /* Initialize the log system first. */
        if (null !== $logDir = $config->get('plugin_log', null)) {
            if (is_dir($logDir) && is_writable($logDir)) {
                $logger = new Rsc_Logger($logDir);
                $this->environment->setLogger($logger);
            }
        }

        /* If it's a production environment and cache directory is OK */
        if ($config->isEnvironment(Rsc_Environment::ENV_PRODUCTION)
            && null !== $cacheDir = $config->get('plugin_cache', null)
        ) {
            if (is_dir($cacheDir) && is_writable($cacheDir)) {
                $cacheAdapter = new Rsc_Cache_Filesystem($cacheDir);
            } else {
                if ($logger) {
                    $logger->error(
                        'Cache directory "{dir}" is not writable or does not exists.',
                        array(
                            'dir' => realpath($cacheDir),
                        )
                    );
                }
            }
        }

        $this->environment->setCacheAdapter($cacheAdapter);
    }

    /**
     * Creates plugin's directories.
     */
    protected function initFilesystem()
    {
        $directories = array(
            'tmp'        => '/ready-slider',
            'log'        => '/ready-slider/log',
            'cache'      => '/ready-slider/cache',
            'twig_cache' => '/ready-slider/cache/twig',
        );

        foreach ($directories as $key => $dir) {
            if (false !== $fullPath = $this->makeDirectory($dir)) {
                $this->environment->getConfig()->add('plugin_' . $key, $fullPath);
            }
        }
    }

    /**
     * Make directory in uploads directory.
     * @param string $directory Relative to the WP_UPLOADS dir
     * @return bool|string FALSE on failure, full path to the directory on success
     */
    protected function makeDirectory($directory)
    {
        $uploads = wp_upload_dir();
        $basedir = $uploads['basedir'];

        if (!is_dir($dir = $basedir . $directory)) {
            if (false === @mkdir($dir, 0777, true)) {
                return false;
            }
        }

        return $dir;
    }

    protected function getPluginEnvironment()
    {
        $environment = Rsc_Environment::ENV_PRODUCTION;

        if (defined('WP_DEBUG') && WP_DEBUG) {
            if (defined('READY_SLIDER_DEBUG') && READY_SLIDER_DEBUG) {
                $environment = Rsc_Environment::ENV_DEVELOPMENT;
            }
        }

        if ($_SERVER['SERVER_NAME'] === 'localhost' && $_SERVER['SERVER_PORT'] === '8001') {
            $environment = Rsc_Environment::ENV_DEVELOPMENT;
        }

        return $environment;
    }
}
