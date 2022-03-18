<?php


namespace Project\Traits;

use Project\Core\Utils\Color;

trait functionsTraits
{
    /**
     * Shows a success notification
     *
     * @param string $message
     */
    protected function notifySuccess($message)
    {
        print Color::success($message);
    }
}