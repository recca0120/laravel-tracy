<?php

namespace Recca0120\LaravelTracy;

use Exception;
use Tracy\Helpers;
use ErrorException;
use Tracy\Debugger;

class BlueScreen
{
    /**
     * render.
     *
     * @method render
     *
     * @param \Exception $exception
     *
     * @return string
     */
    public function render(Exception $exception, $error = null)
    {
        $error = is_null($error) === true ? error_get_last() : $error;
        $exception = $this->fixStack($exception, $error);

        ob_start();
        Helpers::improveException($exception);
        Debugger::getBlueScreen()->render($exception);

        return ob_get_clean();
    }

    /**
     * render.
     *
     * @method render
     *
     * @param \Exception $exception
     * @param array      $error
     *
     * @return \Exception
     */
    protected function fixStack($exception, $error)
    {
        if (in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE, E_RECOVERABLE_ERROR, E_USER_ERROR], true) === true) {
            $exception = Helpers::fixStack(new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']));
        }

        return $exception;
    }
}
