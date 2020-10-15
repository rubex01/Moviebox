<?php

namespace Framework\Commands\Create;

trait TemplateTraits
{
    /**
     * Gets name extension of specific files wich need it
     *
     * @param string $generateType
     * @return false|string
     */
    public function getNameExtension(string $generateType)
    {
        switch ($generateType) {
            case 'migration':
                $extension = date('YmdHis');
                break;
            case 'layout':
                $extension = '.layout';
                break;
            default:
                $extension = '';
        }
        return $extension;
    }
}