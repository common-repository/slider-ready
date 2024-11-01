<?php

/**
 * Class ReadySlider_Ui_BackendStylesheet
 *
 * Loads the stylesheet to backend.
 */
class ReadySlider_Ui_BackendStylesheet extends ReadySlider_Ui_Stylesheet
{
    /**
     * {@inheritdoc}
     */
    public function load()
    {
        $request = Rsc_Http_Request::create();

        if (false !== strpos($request->query->get('page'), 'ready-slider')) {
            $this->register('admin_print_styles');
        }
    }
} 