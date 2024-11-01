<?php

/**
 * Class ReadySlider_Settings_Registry
 *
 * @package ReadySlider\Settings
 * @author Artur Kovalevsky
 */
class ReadySlider_Settings_Registry
{

    /**
     * @var ReadySlider_Settings_SettingsStorageInterface
     */
    private $storage;

    /**
     * @var null|string
     */
    protected $prefix;

    /**
     * Constructor
     *
     * @param string $prefix
     * @param ReadySlider_Settings_SettingsStorageInterface $storage
     */
    public function __construct(
        $prefix = null,
        ReadySlider_Settings_SettingsStorageInterface $storage = null
    ) {
        $this->prefix = $prefix;
        $this->storage = $storage ? $storage : $this->createDefaultStorage();
    }

    /**
     * @param null|string $prefix
     * @return ReadySlider_Settings_Registry
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param \ReadySlider_Settings_SettingsStorageInterface $storage
     * @return ReadySlider_Settings_Registry
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * @return \ReadySlider_Settings_SettingsStorageInterface
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @see ReadySlider_Settings_SettingsStorageInterface::add
     */
    public function add($key, $value)
    {
        $this->storage->add($this->prefix . $key, $value);
    }

    /**
     * @see ReadySlider_Settings_SettingsStorageInterface::get
     */
    public function get($key, $default = null)
    {
        return $this->storage->get($this->prefix . $key, $default);
    }

    /**
     * @see ReadySlider_Settings_SettingsStorageInterface::set
     */
    public function set($key, $value)
    {
        $this->storage->set($this->prefix . $key, $value);
    }

    /**
     * @see ReadySlider_Settings_SettingsStorageInterface::delete
     */
    public function delete($key)
    {
        $this->storage->delete($this->prefix . $key);
    }

    /**
     * @see ReadySlider_Settings_SettingsStorageInterface::all
     */
    public function all()
    {
        return $this->storage->all($this->prefix);
    }

    private function createDefaultStorage()
    {
        return new ReadySlider_Settings_Storage_WordpressStorage();
    }
} 