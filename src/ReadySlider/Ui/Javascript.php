<?php

/**
 * Class ReadySlider_Ui_Javascript
 */
class ReadySlider_Ui_Javascript extends ReadySlider_Ui_Asset
{
    /**
     * @var bool
     */
    protected $inFooter;

    /**
     * Constructor
     * @param string $handle
     * @param string $source
     * @param bool $preventCaching
     */
    public function __construct($handle, $source = null, $preventCaching = false)
    {
        parent::__construct($handle, $source, $preventCaching);

        $this->inFooter = false;
    }

    /**
     * Sets the in footer state
     * @param boolean $inFooter
     * @return ReadySlider_Ui_Javascript
     */
    public function setInFooter($inFooter)
    {
        $this->inFooter = (bool) $inFooter;
        return $this;
    }

    /**
     * Returns the in footer state.
     * @return boolean
     */
    public function getInFooter()
    {
        return $this->inFooter;
    }


    /**
     * Enqueue the asset
     */
    public function enqueue()
    {
        wp_enqueue_script(
            $this->handle,
            $this->source,
            $this->deps,
            $this->version,
            $this->inFooter
        );
    }

    /**
     * Loads the asset
     */
    public function load()
    {
        $this->register('wp_print_scripts');
    }
}