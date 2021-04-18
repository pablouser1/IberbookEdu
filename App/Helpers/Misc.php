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

    // Replace accent marks from string
    public static function eliminar_tildes($string){
        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );

        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string );

        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string );

        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string );

        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string );

        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C'),
            $string
        );

        return $string;
    }

    public static function generate_username(string $name, string $surname, string $birthday) {
        // Name
        $name_lower = self::eliminar_tildes(strtolower($name));

        // Get surname
        $surname_exp = explode(" ", $surname);
        $surname_first = self::eliminar_tildes(strtolower($surname_exp[0]));

        // Date
        $birthday_exp = explode("-", $birthday);
        $birthday_final = [
            "day" => $birthday_exp[2],
            "month" => $birthday_exp[1],
            "year" => substr($birthday_exp[0], -2)
        ];

        $username = $name_lower . "_" . $surname_first . "_" . $birthday_final["day"] . $birthday_final["month"] . $birthday_final["year"];
        return $username;
    }
    /**
     * Generate password from user info
     *
     * @return string Password
    */
    public static function generate_password(string $surname, string $birthday){
        $surname_exp = explode(" ", $surname);
        $first_sur = self::eliminar_tildes(strtolower($surname_exp[0]));
        $birthday_exp = explode("-", $birthday);
        $birthday_final = [
            "day" => $birthday_exp[2],
            "month" => $birthday_exp[1],
            "year" => substr($birthday_exp[0], -2)
        ];
        $password = $first_sur.$birthday_final["day"].$birthday_final["month"].$birthday_final["year"];
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
