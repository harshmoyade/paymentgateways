<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit90df1ce32ef11cbd9bba7346ccab996f
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Payments\\' => 9,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Payments\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit90df1ce32ef11cbd9bba7346ccab996f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit90df1ce32ef11cbd9bba7346ccab996f::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}