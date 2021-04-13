<?php
namespace App\Helpers;
class Misc {
    static public function recursiveRemove($dir) {
        foreach(glob("{$dir}/*") as $file) {
            if(is_dir($file)) {
                self::recursiveRemove($file);
            }
            else {
                unlink($file);
            }
        }
        rmdir($dir);
    }
    /**
     * A PHP function that will generate a secure random password.
     *
     * @param int $length The length that you want your random password to be
     * @return string The random password.
    */
    public static function random_password($length){
        //A list of characters that can be used in our random password.
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!-.[]?*()';
        //Create a blank string.
        $password = '';
        //Get the index of the last character in our $characters string.
        $characterListLength = mb_strlen($characters, '8bit') - 1;
        //Loop from 1 to the $length that was specified.
        for ($i=0; $i < $length; $i++) {
            $password .= $characters[random_int(0, $characterListLength)];
        }
        return $password;
    }

    // Check if password has some requierements
    public static function isPasswordValid($password) {
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);
        if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
            return false;
        }
        return true;
    }
}
