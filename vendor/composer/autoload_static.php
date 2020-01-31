<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

use Closure;

class ComposerStaticInit93a3463525d29d6de976e41174c752b1
{
    public static $prefixLengthsPsr4 = array(
        'M' =>
            array(
                'MyCredHooksStorage\\' => 19,
            ),
    );

    public static $prefixDirsPsr4 = array(
        'MyCredHooksStorage\\' =>
            array(
                0 => __DIR__ . '/../..' . '/Inc',
            ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit93a3463525d29d6de976e41174c752b1::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit93a3463525d29d6de976e41174c752b1::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}