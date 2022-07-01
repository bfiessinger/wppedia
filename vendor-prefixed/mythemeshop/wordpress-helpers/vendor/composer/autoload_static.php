<?php

// autoload_static.php @generated by Composer
namespace WPPedia_Vendor\Composer\Autoload;

class ComposerStaticInit65a0993a63c917989297698ea08a315e
{
    public static $prefixLengthsPsr4 = array('M' => array('MyThemeShop\\Helpers\\' => 20));
    public static $prefixDirsPsr4 = array('MyThemeShop\\Helpers\\' => array(0 => __DIR__ . '/../..' . '/src'));
    public static $classMap = array('WPPedia_Vendor\\Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php', 'WPPedia_Vendor\\MyThemeShop\\Admin\\List_Table' => __DIR__ . '/../..' . '/src/admin/class-list-table.php', 'WPPedia_Vendor\\MyThemeShop\\Admin\\Page' => __DIR__ . '/../..' . '/src/admin/class-page.php', 'WPPedia_Vendor\\MyThemeShop\\Database\\Clauses' => __DIR__ . '/../..' . '/src/database/class-clauses.php', 'WPPedia_Vendor\\MyThemeShop\\Database\\Database' => __DIR__ . '/../..' . '/src/database/class-database.php', 'WPPedia_Vendor\\MyThemeShop\\Database\\Escape' => __DIR__ . '/../..' . '/src/database/class-escape.php', 'WPPedia_Vendor\\MyThemeShop\\Database\\GroupBy' => __DIR__ . '/../..' . '/src/database/class-groupby.php', 'WPPedia_Vendor\\MyThemeShop\\Database\\Joins' => __DIR__ . '/../..' . '/src/database/class-joins.php', 'WPPedia_Vendor\\MyThemeShop\\Database\\OrderBy' => __DIR__ . '/../..' . '/src/database/class-orderby.php', 'WPPedia_Vendor\\MyThemeShop\\Database\\Query_Builder' => __DIR__ . '/../..' . '/src/database/class-query-builder.php', 'WPPedia_Vendor\\MyThemeShop\\Database\\Select' => __DIR__ . '/../..' . '/src/database/class-select.php', 'WPPedia_Vendor\\MyThemeShop\\Database\\Translate' => __DIR__ . '/../..' . '/src/database/class-translate.php', 'WPPedia_Vendor\\MyThemeShop\\Database\\Where' => __DIR__ . '/../..' . '/src/database/class-where.php', 'WPPedia_Vendor\\MyThemeShop\\Helpers\\Arr' => __DIR__ . '/../..' . '/src/helpers/class-arr.php', 'WPPedia_Vendor\\MyThemeShop\\Helpers\\Attachment' => __DIR__ . '/../..' . '/src/helpers/class-attachment.php', 'WPPedia_Vendor\\MyThemeShop\\Helpers\\Conditional' => __DIR__ . '/../..' . '/src/helpers/class-conditional.php', 'WPPedia_Vendor\\MyThemeShop\\Helpers\\DB' => __DIR__ . '/../..' . '/src/helpers/class-db.php', 'WPPedia_Vendor\\MyThemeShop\\Helpers\\HTML' => __DIR__ . '/../..' . '/src/helpers/class-html.php', 'WPPedia_Vendor\\MyThemeShop\\Helpers\\Param' => __DIR__ . '/../..' . '/src/helpers/class-param.php', 'WPPedia_Vendor\\MyThemeShop\\Helpers\\Str' => __DIR__ . '/../..' . '/src/helpers/class-str.php', 'WPPedia_Vendor\\MyThemeShop\\Helpers\\Url' => __DIR__ . '/../..' . '/src/helpers/class-url.php', 'WPPedia_Vendor\\MyThemeShop\\Helpers\\WordPress' => __DIR__ . '/../..' . '/src/helpers/class-wordpress.php', 'WPPedia_Vendor\\MyThemeShop\\Json_Manager' => __DIR__ . '/../..' . '/src/class-json-manager.php', 'WPPedia_Vendor\\MyThemeShop\\Notification' => __DIR__ . '/../..' . '/src/class-notification.php', 'WPPedia_Vendor\\MyThemeShop\\Notification_Center' => __DIR__ . '/../..' . '/src/class-notification-center.php');
    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit65a0993a63c917989297698ea08a315e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit65a0993a63c917989297698ea08a315e::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit65a0993a63c917989297698ea08a315e::$classMap;
        }, null, ClassLoader::class);
    }
}
