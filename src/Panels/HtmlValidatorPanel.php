<?php

namespace Recca0120\LaravelTracy\Panels;

use DOMDocument;
use LibXMLError;

class HtmlValidatorPanel extends AbstractPanel
{
    /**
     * $html.
     *
     * @var string
     */
    protected $html;

    /**
     * $ignoreErrors.
     *
     * @var array
     */
    public static $ignoreErrors = [
        // XML_ERR_ENTITYREF_SEMICOL_MISSING
        23,
        // XML_HTML_UNKNOWN_TAG
        801,
    ];

    /**
     * $severenity.
     *
     * @var array
     */
    public static $severenity = [
        LIBXML_ERR_WARNING => 'Warning',
        LIBXML_ERR_ERROR => 'Error',
        LIBXML_ERR_FATAL => 'Fatal error',
    ];

    /**
     * setHTML.
     *
     * @method setHtml
     *
     * @param string $html
     *
     * @return $this
     */
    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
    }

    /**
     * Removes special controls characters and normalizes line endings and spaces.
     * @param  string  UTF-8 encoding
     * @return string
     */
    public static function normalize($s)
    {
        $s = static::normalizeNewLines($s);
        // remove control characters; leave \t + \n
        $s = preg_replace('#[\x00-\x08\x0B-\x1F\x7F-\x9F]+#u', '', $s);
        // right trim
        $s = preg_replace('#[\t ]+$#m', '', $s);
        // leading and trailing blank lines
        $s = trim($s, "\n");

        return $s;
    }

    /**
     * Standardize line endings to unix-like.
     * @param  string  UTF-8 encoding or 8-bit
     * @return string
     */
    public static function normalizeNewLines($s)
    {
        return str_replace(["\r\n", "\r"], "\n", $s);
    }

    /**
     * getAttributes.
     *
     * @method getAttributes
     *
     * @return array
     */
    public function getAttributes()
    {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->resolveExternals = false;
        $dom->validateOnParse = true;
        $dom->preserveWhiteSpace = false;
        $dom->strictErrorChecking = true;
        $dom->recover = true;

        // set_error_handler(function ($severity, $message) {
        //     restore_error_handler();
        // });

        @$dom->loadHTML($this->normalize($this->html));
        // restore_error_handler();

        $errors = array_filter(libxml_get_errors(), function (LibXMLError $error) {
            return in_array((int) $error->code, static::$ignoreErrors, true) === false;
        });

        libxml_clear_errors();

        return [
            'severenity' => static::$severenity,
            'counter' => count($errors),
            'errors' => $errors,
            'html' => $this->html,
        ];
    }
}
