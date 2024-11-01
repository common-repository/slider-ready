<?php

/**
 * Class ReadySlider_Settings_Model_Settings
 *
 * @package ReadySlider\Settings\Model
 * @author Artur Kovalevsky
 */
class ReadySlider_Settings_Model_Settings extends ReadySlider_Core_BaseModel
{

    /**
     * @var ReadySlider_Settings_Registry
     */
    private $registry;

    /**
     * Sets the Settings Registry object
     *
     * @param \ReadySlider_Settings_Registry $registry
     * @return ReadySlider_Settings_Model_Settings
     */
    public function setRegistry($registry)
    {
        $this->registry = $registry;
        return $this;
    }

    public function save(Rsc_Http_Request $request)
    {
        foreach ($request->post as $field => $value) {
            $this->registry->set($field, $value);
        }
    }
} 