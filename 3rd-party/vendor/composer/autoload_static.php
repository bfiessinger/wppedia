<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit94199a7f865ba6d053aa81a09153f9ff
{
    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit94199a7f865ba6d053aa81a09153f9ff::$classMap;

        }, null, ClassLoader::class);
    }
}