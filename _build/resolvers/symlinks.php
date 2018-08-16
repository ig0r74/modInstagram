<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;

    $dev = MODX_BASE_PATH . 'Extras/modInstagram/';
    /** @var xPDOCacheManager $cache */
    $cache = $modx->getCacheManager();
    if (file_exists($dev) && $cache) {
        if (!is_link($dev . 'assets/components/modinstagram')) {
            $cache->deleteTree(
                $dev . 'assets/components/modinstagram/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_ASSETS_PATH . 'components/modinstagram/', $dev . 'assets/components/modinstagram');
        }
        if (!is_link($dev . 'core/components/modinstagram')) {
            $cache->deleteTree(
                $dev . 'core/components/modinstagram/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_CORE_PATH . 'components/modinstagram/', $dev . 'core/components/modinstagram');
        }
    }
}

return true;