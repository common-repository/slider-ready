<?php

/**
 * Twig Extension for working with attachments.
 */
class ReadySlider_Slider_Twig_Attachment extends Twig_Extension
{

    /**
     * @var ReadySlider_Slider_Attachment
     */
    protected $helper;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->helper = new ReadySlider_Slider_Attachment();
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'Ready! Slider Attachments';
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('image_size', array(
                $this,
                'getImageSizeFilter'
            )),
            new Twig_SimpleFilter('attachment_size', array(
                $this->helper,
                'getSize'
            )),
        );
    }

    /**
     * Returns requested size for the specified image.
     *
     * @param array|object $image Image entity.
     * @param int $width Requested width.
     * @param int $height Requested height.
     * @return string
     */
    public function getImageSizeFilter($image, $width, $height = null)
    {
        if (is_array($image) && isset($image['attachment'])) {
            return $this->helper->getSize(
                $image['attachment']['id'],
                $width,
                $height
            );
        }

        if (is_object($image) && property_exists($image, 'attachment')) {
            return $this->helper->getSize(
                $image->attachment['id'],
                $width,
                $height
            );
        }

        return $this->helper->getSize(null, $width, $height);
    }
}