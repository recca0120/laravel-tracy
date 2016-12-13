<?php

namespace Recca0120\LaravelTracy\Session;

class Compressor
{
    /**
     * compress.
     *
     * @param  mix $data
     *
     * @return string
     */
    public function compress($data)
    {
        $compressor = $this->getCompressor('compress');

        if (is_null($compressor) === true || empty($data) === true) {
            return $data;
        }

        $steps = ['serialize', $compressor, 'base64_encode'];

        return $this->callBySteps($steps, $data);
    }

    /**
     * decompress.
     *
     * @param string $data
     *
     * @return mix
     */
    public function decompress($data)
    {
        $compressor = $this->getCompressor('decompress');

        if (is_null($compressor) === true || empty($data) === true) {
            return $data;
        }

        $steps = ['base64_decode', $compressor, 'unserialize'];

        return $this->callBySteps($steps, $data);
    }

    /**
     * callBySteps.
     *
     * @param  array $steps
     * @param  mix $data
     *
     * @return mix
     */
    protected function callBySteps($steps, $data)
    {
        foreach ($steps as $step) {
            $data = call_user_func_array($step, [$data]);
        }

        return $data;
    }

    /**
     * getCompressor.
     *
     * @param string $type
     *
     * @return string
     */
    protected function getCompressor($type = 'compress')
    {
        $map = [
            'compress' => [
                'gzdeflate',
                'gzcompress',
            ],
            'decompress' => [
                'gzinflate',
                'gzuncompress',
            ],
        ];

        $compressor = null;
        foreach ($map[$type] as $func) {
            if (function_exists($func) === true) {
                $compressor = $func;

                break;
            }
        }

        return $compressor;
    }
}
