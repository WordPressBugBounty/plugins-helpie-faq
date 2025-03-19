<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit367fd35918c5de4f830e4e0531791706
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Pauple\\Pluginator\\' => 18,
        ),
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Pauple\\Pluginator\\' => 
        array (
            0 => __DIR__ . '/..' . '/pauple/pluginator/src',
        ),
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit367fd35918c5de4f830e4e0531791706::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit367fd35918c5de4f830e4e0531791706::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit367fd35918c5de4f830e4e0531791706::$classMap;

        }, null, ClassLoader::class);
    }
}
