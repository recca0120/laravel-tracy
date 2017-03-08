<?php

namespace Recca0120\LaravelTracy;

use Illuminate\Support\Arr;

class Session
{
    /**
     * isStarted.
     *
     * @return bool
     */
    protected function isStarted()
    {
        return PHP_SESSION_ACTIVE === session_status();
    }

    /**
     * start.
     */
    public function start()
    {
        if ($this->isStarted() === false) {
            ini_set('session.use_cookies', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.use_trans_sid', '0');
            ini_set('session.cookie_path', '/');
            ini_set('session.cookie_httponly', '1');
            session_start();
        }

        if (function_exists('gzcompress') === true) {
            $this->decompressBar();
            $this->compressBar();
        }
    }

    /**
     * getContentId.
     *
     * @return string
     */
    protected function getContentId() {
        $tracyBar = isset($_GET['_tracy_bar']) === true ? $_GET['_tracy_bar'] : '';

        if (strpos($tracyBar, 'content.') === false) {
            return '';
        }

        return str_replace('content.', '', $tracyBar);
    }

    /**
     * compressBar.
     */
    protected function compressBar()
    {
        if (empty($contentId = $this->getContentId()) === false) {
            $tracyBar = Arr::get($_SESSION, 'tracy.bar.'.$contentId);
            if (empty($tracyBar) === false) {
                Arr::set($_SESSION, '_tracy.bar.'.$contentId,
                    $this->decompress(
                        Arr::get( $_SESSION,  'tracy.bar.'.$contentId)
                    )
                );
                Arr::forget($_SESSION, 'tracy.bar.'.$contentId);
            }
        }
    }

    /**
     * decompressBar.
     */
    protected function decompressBar()
    {
        register_shutdown_function(function () {
            $tracyBar = Arr::get($_SESSION, '_tracy.bar', []);
            $keys = array_keys($tracyBar);
            $contentId = array_pop($keys);
            Arr::set($_SESSION, 'tracy.bar.'.$contentId, $this->compress(
                Arr::get($_SESSION, '_tracy.bar.'.$contentId)
            ));
        });
    }

    /**
     * compress.
     *
     * @param mixed $session
     * @return string
     */
    protected function compress($session)
    {
        return gzcompress(serialize($session));
    }

    /**
     * decompress.
     *
     * @param string $plainText
     * @return mixed
     */
    protected function decompress($plainText)
    {
        return unserialize(gzuncompress($plainText));
    }
}
