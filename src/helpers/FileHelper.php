<?php

namespace luya\yii\helpers;

use Exception;
use Yii;

/**
 * Helper methods when dealing with Files.
 *
 * Extends the {{yii\helpers\FileHelper}} class by some useful functions like:
 *
 * + {{luya\yii\helpers\FileHelper::humanReadableFilesize()}}
 * + {{luya\yii\helpers\FileHelper::ensureExtension()}}
 * + {{luya\yii\helpers\FileHelper::md5sum()}}
 * + {{luya\yii\helpers\FileHelper::writeFile()}}
 * + {{luya\yii\helpers\FileHelper::getFileContent()}}
 * + {{luya\yii\helpers\FileHelper::unlink()}}
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class FileHelper extends \yii\helpers\BaseFileHelper
{
    /**
     * Generate a human readable size informations from provided Byte/s size.
     *
     * @param integer $size The size to convert in Byte
     * @return string The readable size definition
     */
    public static function humanReadableFilesize($size)
    {
        $mod = 1024;
        $units = explode(' ', 'B KB MB GB TB PB');
        for ($i = 0; $size > $mod; ++$i) {
            $size /= $mod;
        }

        return round($size, 2).' '.$units[$i];
    }

    /**
     * Append a file extension to a path/file if there is no or an empty extension provided, this
     * helper methods is used to make sure the right extension existing on files.
     *
     * @param string $file The file where extension should be append if not existing
     * @param string $extension
     * @return string the ensured file/path with extension
     */
    public static function ensureExtension($file, $extension)
    {
        $info = pathinfo($file);
        if (!isset($info['extension']) || empty($info['extension'])) {
            $file = rtrim($file, '.') . '.' . $extension;
        }

        return $file;
    }

    /**
     * Provide class informations from a file path or file content.
     *
     * This is used when working with file paths from Composer, in order to detect class and namespace from a given file.
     *
     * @param string $file The file path to the class into order to get infos from, could also be the content directly from a given file.
     * @return array If the given file path is a file, it will return an array with the keys:
     * + `namespace`: the namespace of the file, `false` if no namespace could have been determined.
     * + `class`: the class name of the file, `false` if no class could have been determined.
     */
    public static function classInfo($file)
    {
        if (is_file($file)) {
            $phpCode = file_get_contents($file);
        } else {
            $phpCode = $file;
        }

        $namespace = false;

        if (preg_match('/^namespace\s+(.+?);(\s+|\r\n)?$/sm', $phpCode, $results)) {
            $namespace = $results[1];
        }

        $classes = self::classNameByTokens($phpCode);

        return ['namespace' => $namespace, 'class' => end($classes)];
    }

    /**
     * Tokenize the PHP code from a given class in in order to determine the class name.
     *
     * @param string $phpCode The PHP code to tokenize and find the class name from
     * @return array
     */
    private static function classNameByTokens($phpCode)
    {
        $classes = [];
        $tokens = token_get_all($phpCode);
        $count = count($tokens);
        for ($i = 2; $i < $count; $i++) {
            if ($tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) {
                $classes[] = $tokens[$i][1];
            }
        }

        return $classes;
    }

    /**
     * Create a unique hash name from a given file.
     *
     * Warning
     * Because PHP's integer type is signed many `crc32()` checksums will result in negative integers on 32-bit platforms. On 64-bit installations all `crc32()` results will be positive integers though.
     * So you need to use the `%u` formatter of `sprintf()` or `printf()` to get the string representation of the unsigned `crc32()` checksum in decimal format.
     *
     * @param string $fileName The file name which should be hashed.
     * @return string
     */
    public static function hashName($fileName)
    {
        return sprintf('%s', hash('crc32b', uniqid($fileName, true)));
    }

    /**
     * Get extension and name from a file for the provided source/path of the file.
     *
     * @param string $sourceFile The path of the file
     * @return object With extension and name keys.
     */
    public static function getFileInfo($sourceFile)
    {
        // pathinfo() always returns an array event the path does not exists
        $path = pathinfo($sourceFile);

        return (object) [
            'extension' => !empty($path['extension']) ? mb_strtolower($path['extension'], 'UTF-8') : false,
            'name' => !empty($path['filename']) ? $path['filename'] : false,
            'source' => $sourceFile,
            'sourceFilename' => !empty($path['filename']) ? $path['dirname'] . DIRECTORY_SEPARATOR . $path['filename'] : false,
        ];
    }

    /**
     * Generate a MD5 hash of a file. This is equal to `md5sum` command.
     *
     * @param string $sourceFile The path to the file
     * @return false|string Returns false or the MD5 hash of this file
     */
    public static function md5sum($sourceFile)
    {
        return file_exists($sourceFile) ? hash_file('md5', $sourceFile) : false;
    }

    /**
     * Basic helper method to write files with exception capture. The filename will auto-wrapped
     * through the `Yii::getAlias()` function.
     *
     * @param string $fileName The path to the file with filename
     * @param string $content The content to store in this file
     * @return boolean
     */
    public static function writeFile($fileName, $content)
    {
        try {
            $response = file_put_contents(Yii::getAlias($fileName), $content);
            if ($response === false) {
                return false;
            }
        } catch (Exception $error) {
            return false;
        }

        return true;
    }

    /**
     * Basic helper to retrieve the content of a file and catched exception. The filename
     * will auto-alias encoded by `Yii::getAlias()` function.
     *
     * @param string $fileName The path to the file to get the content
     * @return string|boolean
     */
    public static function getFileContent($fileName)
    {
        try {
            return file_get_contents(Yii::getAlias($fileName));
        } catch (Exception $error) {
            return false;
        }
    }

    /**
     * Unlink a file, which handles symlinks.
     *
     * @param string $file The file path to the file to delete.
     * @return boolean Whether the file has been removed or not.
     */
    public static function unlink($file)
    {
        // no errors should be thrown, return false instead.
        try {
            if (parent::unlink($file)) {
                return true;
            }
        } catch (\Exception $e) {
        }

        // try to force symlinks
        if (is_link($file)) {
            $sym = @readlink($file);
            if ($sym) {
                if (@unlink($file)) {
                    return true;
                }
            }
        }

        // try to use realpath()
        if (realpath($file) && realpath($file) !== $file) {
            if (@unlink(realpath($file))) {
                return true;
            }
        }

        return false;
    }
}
