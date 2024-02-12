<?php

namespace luya\yii\helpers;

use ZipArchive;

/**
 * Helper methods when dealing with ZIP Archives.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class ZipHelper
{
    /**
     * Add files and sub-directories in a folder to ZIP file.
     *
     * @param string $folder
     * @param \ZipArchive $zipFile
     * @param integer $exclusiveLength Length of text to be exclusived from the file path.
     */
    private static function folderToZip($folder, &$zipFile, $exclusiveLength)
    {
        $handle = opendir($folder);
        while (false !== $f = readdir($handle)) {
            if ($f != '.' && $f != '..') {
                $filePath = "$folder/$f";
                // Remove prefix from file path before add to ZIP.
                $localPath = substr($filePath, $exclusiveLength);
                if (is_file($filePath)) {
                    $zipFile->addFile($filePath, $localPath);
                } elseif (is_dir($filePath)) {
                    // Add sub-directory.
                    $zipFile->addEmptyDir($localPath);
                    self::folderToZip($filePath, $zipFile, $exclusiveLength);
                }
            }
        }
        closedir($handle);
    }

    /**
     * Zip a folder (include itself).
     *
     * ```php
     * ZipHelper::dir('/path/to/sourceDir', '/path/to/out.zip');
     * ```
     *
     * @param string $sourcePath Path of directory to be zipped.
     * @param string $outZipPath Path of output ZIP file.
     */
    public static function dir($sourcePath, $outZipPath)
    {
        $pathInfo = pathInfo($sourcePath);
        $parentPath = $pathInfo['dirname'];
        $dirName = $pathInfo['basename'];

        $z = new ZipArchive();
        $z->open($outZipPath, ZIPARCHIVE::CREATE);
        $z->addEmptyDir($dirName);
        self::folderToZip($sourcePath, $z, strlen("$parentPath/"));
        $z->close();

        return true;
    }
}
