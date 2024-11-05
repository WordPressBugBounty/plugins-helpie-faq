<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitf46151f5446c5c7c00ed82c4cd28163e
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInitf46151f5446c5c7c00ed82c4cd28163e', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitf46151f5446c5c7c00ed82c4cd28163e', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitf46151f5446c5c7c00ed82c4cd28163e::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
