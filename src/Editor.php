<?php

namespace Recca0120\LaravelTracy;

class Editor
{
    /**
     * Editor protocol.
     *
     * Usage:
     *      Editor::openWith('vscode')
     *
     * https://tracy.nette.org/en/open-files-in-ide
     *
     * @param string $editor (sublime|subl, phpstorm, vscode, macvim|mvim, textmate|txmt)
     * @return string
     */
    public static function openWith($editor = 'subl')
    {
        switch (strtolower($editor)) {
            case 'phpstorm':
                return 'phpstorm://open?file=%file&line=%line';

            case 'vscode':
                return 'vscode://file/%file:%line';

            case 'mvim':
            case 'macvim':
                return 'mvim://open/?url=file://%file&line=%line';

            case 'txmt':
            case 'textmate':
                return 'txmt://open/?url=file://%file&line=%line';

            case 'subl':
            case 'sublime':
                return 'subl://open?url=file://%file&line=%line';
        }

        return 'editor://open/?file=%file&line=%line';
    }
}
