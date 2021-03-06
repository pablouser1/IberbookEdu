<?php

namespace Helpers;

use ZipArchive;

// ZIP class

// Derivate from https://stackoverflow.com/a/19730838
class Zip {
    /**
     * Add files and sub-directories in a folder to zip file.
     * @param string $folder
     * @param ZipArchive $zipFile
     * @param int $exclusiveLength Number of text to be exclusived from the file path.
     */
    private static function folderToZip($folder, &$zipFile, $exclusiveLength, array $exceptions) {
        $handle = opendir($folder);
        while (false !== $f = readdir($handle)) {
            if ($f != '.' && $f != '..') {
                $filePath = "$folder/$f";
                // Remove prefix from file path before add to zip.
                $localPath = substr($filePath, $exclusiveLength);
                if (is_file($filePath) && !in_array(basename($localPath), $exceptions)  ) {
                    $zipFile->addFile($filePath, $localPath);
                } elseif (is_dir($filePath)) {
                    // Add sub-directory.
                    $zipFile->addEmptyDir($localPath);
                    self::folderToZip($filePath, $zipFile, $exclusiveLength, $exceptions);
                }
            }
        }
        closedir($handle);
    }
    /**
     * Zip a folder (include itself).
     * Usage:
     *   HZip::zipDir('/path/to/sourceDir', '/path/to/out.zip');
     *
     * @param string $sourcePath Path of directory to be zip.
     * @param string $outZipPath Path of output zip file.
     */
    public static function zipDir($sourcePath, $outZipPath, array $exceptions = []) {
        $z = new ZipArchive();
        $z->open($outZipPath, ZIPARCHIVE::CREATE);
        self::folderToZip($sourcePath, $z, strlen("$sourcePath"), $exceptions);
        $z->close();
    }

    public static function zipString(string $text, string $filename, string $outZipPath) {
        $z = new ZipArchive();
        $z->open($outZipPath, ZIPARCHIVE::CREATE);
        $z->addFromString($filename, $text);
        $z->close();
    }
}
