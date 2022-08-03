<?php

namespace Wengg\WebmanMigrations;

class Install
{
    const WEBMAN_PLUGIN = true;

    /**
     * @var array
     */
    protected static $pathRelation = array(
        'config/plugin/wen-gg/webman-migrations' => 'config/plugin/wen-gg/webman-migrations',
    );

    /**
     * Install
     * @return void
     */
    public static function install()
    {
        copy(__DIR__ . '/migrate', base_path() . '/migrate');
        chmod(base_path() . '/migrate', 0755);
        $arr = [
            base_path() . '/database/migrations',
            base_path() . '/database/seeders',
        ];
        foreach ($arr as $value) {
            if (!file_exists($value)) {
                mkdir($value, 0755, true);
            }
        }
        static::installByRelation();
    }

    /**
     * Uninstall
     * @return void
     */
    public static function uninstall()
    {
        if (file_exists(base_path() . '/migrate')) {
            unlink(base_path() . '/migrate');
        }
        echo "为了数据安全，请自行删除相应迁移目录！", PHP_EOL;
        self::uninstallByRelation();
    }

    /**
     * installByRelation
     * @return void
     */
    public static function installByRelation()
    {
        foreach (static::$pathRelation as $source => $dest) {
            if ($pos = strrpos($dest, '/')) {
                $parent_dir = base_path() . '/' . substr($dest, 0, $pos);
                if (!is_dir($parent_dir)) {
                    mkdir($parent_dir, 0777, true);
                }
            }
            //symlink(__DIR__ . "/$source", base_path()."/$dest");
            copy_dir(__DIR__ . "/$source", base_path() . "/$dest");
            echo "Create $dest", PHP_EOL;
        }
    }

    /**
     * uninstallByRelation
     * @return void
     */
    public static function uninstallByRelation()
    {
        foreach (static::$pathRelation as $source => $dest) {
            $path = base_path() . "/$dest";
            if (!is_dir($path) && !is_file($path)) {
                continue;
            }
            echo "Remove $dest", PHP_EOL;
            if (is_file($path) || is_link($path)) {
                unlink($path);
                continue;
            }
            remove_dir($path);
        }
    }

}
