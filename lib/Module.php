<?php

namespace TobiasKrais\D2UNews;

/**
 * Class managing modules published by www.design-to-use.de.
 *
 * @author Tobias Krais
 */
class Module
{
    /**
     * Get modules offered by this addon.
     * @return \TobiasKrais\D2UHelper\Module[] Modules offered by this addon
     */
    public static function getModules()
    {
        $modules = [];
        $modules[] = new \TobiasKrais\D2UHelper\Module('40-1',
            'D2U News - Ausgabe News',
            7);
        $modules[] = new \TobiasKrais\D2UHelper\Module('40-2',
            'D2U News - Ausgabe Messen',
            1);
        $modules[] = new \TobiasKrais\D2UHelper\Module('40-3',
            'D2U News - Ausgabe News und Messen',
            6);
        return $modules;
    }
}
