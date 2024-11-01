<?php

/**
 * Class ReadySlider_Ui_AssetsCollection
 */
class ReadySlider_Ui_AssetsCollection extends Rsc_Common_Collection
{
    public function add($key, $value = null)
    {
        if (!$key instanceof ReadySlider_Ui_Asset) {
            throw new InvalidArgumentException('Parameter 1 must be instance of ReadySlider_Ui_Asset.');
        }

        parent::add($key->getHandle(), $key);

        return $this;
    }

    /**
     * Sets the asset
     * @param string $key
     * @param ReadySlider_Ui_Asset $value
     * @throws InvalidArgumentException
     * @return ReadySlider_Ui_AssetsCollection
     */
    public function set($key, $value)
    {
        if (!$value instanceof ReadySlider_Ui_Asset) {
            throw new InvalidArgumentException('Parameter 2 must be instance of ReadySlider_Ui_Asset.');
        }

        if ($key !== $value->getHandle()) {
            throw new InvalidArgumentException('The handle of the asset my be the same as $key value');
        }

        parent::set($key, $value);

        return $this;
    }

    /**
     * Returns the asset by handle
     * @param array|string $key
     * @param null $default
     * @return ReadySlider_Ui_Asset
     */
    public function get($key, $default = null)
    {
        return parent::get($key, $default);
    }

    /**
     * Loads the all registered assets.
     */
    public function load()
    {
        foreach ($this->collection as $asset) {
            $asset->load();
        }
    }

}
