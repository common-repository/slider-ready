<?php

/**
 * Handles sliders settings.
 */
class ReadySlider_Slider_Model_Settings extends ReadySlider_Core_BaseModel implements Rsc_Environment_AwareInterface
{

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTable($this->db->prefix . 'rs_settings_sets');
    }

    /**
     * Returns settings by their id.
     *
     * @param int $id Settings identifier.
     * @return object|null
     */
    public function getById($id)
    {
        $query = $this->getQueryBuilder()
            ->select('*')
            ->from($this->getTable())
            ->where('id', '=', (int)$id);

        $settings = $this->db->get_row($query->build());

        if (!$settings) {
            return null;
        }

        $settings->data = unserialize($settings->data);

        return $settings;
    }

    /**
     * Stores settings in the database.
     *
     * @param array $data An array of the settings
     * @return bool
     */
    public function insert(array $data)
    {
        $query = $this->getQueryBuilder()
            ->insertInto($this->getTable())
            ->fields('data')
            ->values(serialize($data));

        if (!$this->db->query($query->build())) {
            $this->setLastError($this->db->last_error);

            return false;
        }

        $insertId = $this->db->insert_id;
        $this->setInsertId($insertId);

        return true;
    }

    /**
     * Updates selected settings.
     *
     * @param int $id Settings identifier.
     * @param array $data An array of the settings.
     * @return bool
     */
    public function update($id, array $data)
    {
        $query = $this->getQueryBuilder()
            ->update($this->getTable())
            ->where('id', '=', (int)$id)
            ->fields('data')
            ->values(serialize($data));

        if (false === $this->db->query($query->build())) {
            $this->setLastError($this->db->last_error);

            return false;
        }

        return true;
    }

    /**
     * Adds the settings to the slider object.
     *
     * @param object $slider
     *
     * @throws LogicException
     * @throws InvalidArgumentException
     * @return object
     */
    public function getSliderSettings($slider)
    {
        if (!is_object($slider)) {
            throw new InvalidArgumentException(sprintf(
                'Parameter 1 must be an object, %s given.',
                gettype($slider)
            ));
        }

        if (!property_exists($slider, 'settings_id')) {
            throw new InvalidArgumentException('Invalid slider object given.');
        }

        $plugin = $slider->plugin;
        /** @var ReadySlider_Slider_Interface $module */
        $module = $this->environment->getModule($plugin);

        if ($slider->settings_id == 0) {

            if (null === $module) {
                return $slider;
            }

            if (!$module instanceof ReadySlider_Slider_Interface) {
                throw new LogicException(sprintf(
                    'Instance of %s must implement ReadySlider_Slider_Interface.',
                    get_class($module)
                ));
            }

            $slider->settings = $module->getDefaults();

            return $slider;
        }

        $settings = $this->getById($slider->settings_id);

        if (!$settings) {
            return $slider;
        }

        $slider->settings = array_merge(
            $module->getDefaults(),
            $settings->data
        );

        return $slider;
    }

    /**
     * Sets the environment.
     *
     * @param Rsc_Environment $environment
     */
    public function setEnvironment(Rsc_Environment $environment)
    {
        $this->environment = $environment;
    }
}