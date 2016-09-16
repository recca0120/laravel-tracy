<?php

namespace Recca0120\LaravelTracy;

use ErrorException;
use Exception;
use Tracy\Debugger;
use Tracy\Helpers;

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
    public function render(Exception $exception)
    {
        $exception = $this->fixStack($exception, error_get_last());

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
     * @param array $error
     *
     * @return \Exception
     */
    protected function fixStack($exception, $error)
    {
        if (in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE, E_RECOVERABLE_ERROR, E_USER_ERROR], true) === true) {
            return Helpers::fixStack(new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']));
        }

        return $exception;
    }
}
