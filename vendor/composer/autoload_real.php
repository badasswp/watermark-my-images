<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitaeb7e483ee53c7f43e235a7b2c47a2fb
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

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInitaeb7e483ee53c7f43e235a7b2c47a2fb', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitaeb7e483ee53c7f43e235a7b2c47a2fb', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitaeb7e483ee53c7f43e235a7b2c47a2fb::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}