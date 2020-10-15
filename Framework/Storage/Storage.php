<?php

namespace Framework\Storage;

class Storage
{
    /**
     * Upload a file
     *
     * @param $file
     * @param bool $public
     * @param string|null $dir
     * @param bool $overwrite
     * @param string|null $customName
     * @return array|false|string
     */
    public static function upload($file, bool $public, string $dir = null, bool $overwrite = false, string $customName = null)
    {
        $targetDirectory = __DIR__ . '/../../'. ($public ? 'public/assets/storage/' : 'Storage/App/') . ($dir ? $dir . '/' : '');

        if(!is_dir($targetDirectory)){
            mkdir($targetDirectory, 0755, true);
        }

        $originalFileName = basename($file["name"]);
        $fileExtension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));

        if ($overwrite === true) {
            if ($customName !== null) {
                $finalNameOfFile = $customName.'.'.$fileExtension;
            }
            else {
                $finalNameOfFile = self::generateName().'.'.$fileExtension;
            }
        }
        else {
            if ($customName !== null) {
                if (file_exists($targetDirectory.$customName.'.'.$fileExtension)) {
                    return 'error';
                }
                $finalNameOfFile = $customName.'.'.$fileExtension;
            }
            else {
                $randomName = self::generateName();
                while (file_exists($targetDirectory.$randomName.'.'.$fileExtension)) {
                    $randomName = self::generateName();
                }
                $finalNameOfFile = $randomName.'.'.$fileExtension;
            }
        }

        $targetFile = $targetDirectory . $finalNameOfFile;

        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return [
                'location' => $targetFile,
                'browserLocation' => ($public ? '/assets/storage/'.$dir.'/'.$finalNameOfFile : 'Unreadable from browser, because this is not stored in public storage.'),
                'cleanLocation' => ($public ? '/public/assets/storage/' : 'Storage/App/').$dir.'/'.$finalNameOfFile,
                'fileName' => $finalNameOfFile,
                'fileExtension' => $fileExtension,
                'fileType' => $file['type']
            ];
        } else {
            return false;
        }
    }

    /**
     * Generates name for file
     *
     * @return string
     */
    public static function generateName() : string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 8; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString . md5(microtime()) . date("Ymdhis");
    }

    /**
     * Deletes file
     *
     * @param string $path
     * @return bool
     */
    public static function delete(string $path) : bool
    {
        return (unlink($path) ? true : false);
    }
}