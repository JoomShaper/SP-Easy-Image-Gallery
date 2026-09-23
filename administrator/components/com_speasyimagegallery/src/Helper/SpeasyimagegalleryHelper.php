<?php
/**
 * @package     SP Easy Image Gallery
 * @subpackage  com_speasyimagegallery
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JoomShaper\Component\Speasyimagegallery\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Access\Access;
use Joomla\CMS\Factory;
use Joomla\CMS\Version;
use Joomla\Registry\Registry;

/**
 * SP Easy Image Gallery helper class.
 */
class SpeasyimagegalleryHelper
{
    /**
     * Component name
     *
     * @var string
     */
    public static string $extension = 'com_speasyimagegallery';

    /**
     * Get user actions
     *
     * @param   int  $messageId  Action id (album id)
     * @return  Registry
     */
    public static function getActions(int $messageId = 0): Registry
    {
        $result = new Registry();

        if (empty($messageId)) {
            $assetName = 'com_speasyimagegallery';
        } else {
            $assetName = 'com_speasyimagegallery.album.' . $messageId;
        }

        $actions = Access::getActionsFromFile(
            JPATH_ADMINISTRATOR . '/components/com_speasyimagegallery/access.xml',
            '/access/section[@name="component"]/'
        );

        $user = Factory::getApplication()->getIdentity();

        foreach ($actions as $action) {
            $result->set($action->name, $user->authorise($action->name, $assetName));
        }

        return $result;
    }

    /**
     * Get Joomla version component
     *
     * @param   string  $type  'major', 'minor', or 'patch'
     * @return  int
     */
    public static function getVersion(string $type = 'major'): int
    {
        $version = new Version();

        switch ($type) {
            case 'minor':
                return $version->getMinor();
            case 'patch':
                return (int) $version->getPatch();
            case 'major':
            default:
                return (int) $version->getMajor();
        }
    }

    /**
     * Create thumbnails
     *
     * @param   string       $src        Source file path
     * @param   array        $sizes      Array of sizes [width, height]
     * @param   string       $folder     Destination folder URL or path
     * @param   string|null  $base_name  Base name
     * @param   string       $ext        Extension
     * @return  array|false
     */
    public static function createThumbs(string $src, array $sizes, string $folder, ?string $base_name, string $ext)
    {
        $info = getimagesize($src);

        if (!$info) {
            return false;
        }

        $originalWidth = $info[0];
        $originalHeight = $info[1];

        if (isset($info['mime'])) {
            $ext = self::mimeToExt($info['mime'], $ext) ?? $ext;
        }

        $img = null;

        switch (strtolower($ext)) {
            case 'bmp':
                $img = imagecreatefromwbmp($src);
                break;
            case 'gif':
                $img = imagecreatefromgif($src);
                break;
            case 'jpg':
            case 'jpeg':
                $img = imagecreatefromjpeg($src);
                break;
            case 'png':
                $img = imagecreatefrompng($src);
                break;
            case 'webp':
                $img = function_exists('imagecreatefromwebp') ? imagecreatefromwebp($src) : null;
                break;
        }

        if (!$img) {
            return false;
        }

        if (count($sizes)) {
            $output = [];

            if ($base_name) {
                $output['original'] = $folder . '/' . $base_name . '.' . $ext;
            }

            foreach ($sizes as $key => $size) {
                $targetWidth = (int) $size[0];
                $targetHeight = (int) $size[1];

                $ratioThumb = $targetWidth / $targetHeight;
                $ratioOriginal = $originalWidth / $originalHeight;

                if ($ratioOriginal >= $ratioThumb) {
                    $height = $originalHeight;
                    $width = (int) ceil(($height * $targetWidth) / $targetHeight);
                    $x = (int) ceil(($originalWidth - $width) / 2);
                    $y = 0;
                } else {
                    $width = $originalWidth;
                    $height = (int) ceil(($width * $targetHeight) / $targetWidth);
                    $x = 0;
                    $y = (int) ceil(($originalHeight - $height) / 2);
                }

                $new = imagecreatetruecolor($targetWidth, $targetHeight);

                if (in_array(strtolower($ext), ['gif', 'png', 'webp'], true)) {
                    imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
                    imagealphablending($new, false);
                    imagesavealpha($new, true);
                }

                imagecopyresampled($new, $img, 0, 0, $x, $y, $targetWidth, $targetHeight, $width, $height);

                if ($base_name) {
                    $dest = dirname($src) . '/' . $base_name . '_' . $key . '.' . $ext;
                    $output[$key] = $folder . '/' . $base_name . '_' . $key . '.' . $ext;
                } else {
                    $dest = $folder . '/' . $key . '.' . $ext;
                }

                switch (strtolower($ext)) {
                    case 'bmp':
                        imagewbmp($new, $dest);
                        break;
                    case 'gif':
                        imagegif($new, $dest);
                        break;
                    case 'jpg':
                    case 'jpeg':
                        imagejpeg($new, $dest, 90);
                        break;
                    case 'png':
                        imagepng($new, $dest, 8);
                        break;
                    case 'webp':
                        if (function_exists('imagewebp')) {
                            imagewebp($new, $dest, 85);
                        }
                        break;
                }

                imagedestroy($new);
            }

            imagedestroy($img);
            return $output;
        }

        imagedestroy($img);
        return false;
    }

    /**
     * Convert mime type to file extension
     *
     * @param   string  $mime         Mime type
     * @param   string  $originalExt  Original extension
     * @return  string|null
     */
    private static function mimeToExt(string $mime, string $originalExt): ?string
    {
        $map = [
            'image/jpeg'     => $originalExt,
            'image/png'      => 'png',
            'image/gif'      => 'gif',
            'image/webp'     => 'webp',
            'image/bmp'      => 'bmp',
            'image/x-ms-bmp' => 'bmp',
        ];

        return $map[strtolower($mime)] ?? null;
    }

    /**
     * Gets the extension of a file name
     *
     * @param   string  $file  The file name
     * @return  string  The file extension
     */
    public static function getExt(string $file): string
    {
        $dot = strrpos($file, '.');

        if ($dot === false) {
            return '';
        }

        $ext = substr($file, $dot + 1);

        if (strpos($ext, '/') !== false || (DIRECTORY_SEPARATOR === '\\' && strpos($ext, '\\') !== false)) {
            return '';
        }

        return $ext;
    }
}
