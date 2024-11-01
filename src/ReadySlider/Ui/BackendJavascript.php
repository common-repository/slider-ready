<?php

/**
 * Class ReadySlider_Ui_BackendJavascript
 */
class ReadySlider_Ui_BackendJavascript extends ReadySlider_Ui_Javascript
{
    /**
     * {@inheritdoc}
     */
    public function load()
    {
        $request = Rsc_Http_Request::create();

        if (false !== strpos($request->query->get('page'), 'ready-slider')) {
            $this->register('admin_print_scripts');
        }
    }
}