<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit98ace3775a8e3aa9b99bb90834ea3776
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Moment\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Moment\\' => 
        array (
            0 => __DIR__ . '/..' . '/fightbulc/moment/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit98ace3775a8e3aa9b99bb90834ea3776::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit98ace3775a8e3aa9b99bb90834ea3776::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
