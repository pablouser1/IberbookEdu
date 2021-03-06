<?php
namespace Helpers;

use Models\User;
use Models\Profile;
use Models\Staff;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Leaf\Helpers\Authentication;
use Leaf\Http\Cookie;

/**
 * Personal auth system
 */
class Auth {
    public static function loginUser(string $username, string $password) {
        $user = User::where("username", "=", $username)->first();
        if ($user) {
            $user->makeVisible(['password']);
            if (password_verify($password, $user->password)) {
                $user->makeHidden(['password']);
                $data = [
                    "user" => [
                        "id" => $user->id
                    ]
                ];
                self::setToken($data, "iberbookedu_user", time()+86400);
                return $user;
            }
        }
        return false;
    }

    public static function loginProfile(int $profileid, User $user) {
        try {
            $profile = Profile::findOrFail($profileid)->where("user_id", "=", $user->id)->first();
            $data = [
                "profile" => [
                    "id" => $profile->id
                ]
            ];
            self::setToken($data, "iberbookedu_profile", time()+86400);
            return $profile;
        }
        catch (ModelNotFoundException $e) {
            return false;
        }
    }

    public static function loginStaff(string $username, string $password) {
        $staff = Staff::where("username", "=", $username)->first();
        if ($staff) {
            $staff->makeVisible(['password']);
            if (password_verify($password, $staff->password)) {
                $staff->makeHidden(['password']);
                $data = [
                    "staff" => $staff
                ];
                self::setToken($data, "iberbookedu_staff", time()+86400);
                return $staff;
            }
        }
        return false;
    }

    // Check if user sent valid cookie
    public static function isUserLoggedin(bool $throwError = true) {
        if (isset($_COOKIE["iberbookedu_user"]) && !empty($_COOKIE["iberbookedu_user"])) {
            $token = $_COOKIE["iberbookedu_user"];
            if ($userinfo = self::authJWT($token)) {
                return $userinfo;
            }
        }
        if ($throwError) {
            throwErr("You are not logged in", 401);
        }
        else {
            return false;
        }
    }

    public static function isProfileLoggedin(bool $throwError = true) {
        if (isset($_COOKIE["iberbookedu_profile"]) && !empty($_COOKIE["iberbookedu_profile"])) {
            $token = $_COOKIE["iberbookedu_profile"];
            if ($profileinfo = self::authJWT($token)) {
                return $profileinfo;
            }
        }
        if ($throwError) {
            throwErr("You are not logged in", 401);
        }
        else {
            return false;
        }
    }

    public static function isStaffLoggedin(string $type, bool $throwError = true) {
        if (isset($_COOKIE["iberbookedu_staff"]) && !empty($_COOKIE["iberbookedu_staff"])) {
            $token = $_COOKIE["iberbookedu_staff"];
            if ($staffinfo = self::authJWT($token)) {
                if ($staffinfo->role === $type) {
                    return $staffinfo;
                }
                throwErr("Invalid staff type", 400);
            }
        }
        if ($throwError) {
            throwErr("You are not logged in", 401);
        }
        return false;
    }

    public static function authJWT($token) {
        $secret_key = getenv("JWT_SECRET");
        if ($token && $secret_key) {
            $payload = Authentication::validate($token, getenv("JWT_SECRET"));
            if (!$payload) {
                response()->throwErr(Authentication::errors());
            }
            if (isset($payload->data->user)) {
                $userid = $payload->data->user->id;
                try {
                    $user = User::findOrFail($userid);
                    return $user;
                }
                catch (ModelNotFoundException $e) {
                    return false;
                }
            }
            elseif (isset($payload->data->profile)) {
                $profileid = $payload->data->profile->id;
                try {
                    $profile = Profile::findOrFail($profileid);
                    return $profile;
                }
                catch (ModelNotFoundException $e) {
                    return false;
                }
            }
            elseif (isset($payload->data->staff)) {
                $staffid = $payload->data->staff->id;
                try {
                    $staff = Staff::findOrFail($staffid);
                    return $staff;
                }
                catch (ModelNotFoundException $e) {
                    return false;
                }
            }
            else {
                return false;
            }
        }
    }
    // -- SET JWT TOKENS -- //
    private static function setToken($data, $name, $time) {
        $key = getenv("JWT_SECRET");
        $issuedAt = time();
        $payload = array(
            "iss" => $_SERVER["HTTP_HOST"],
            "iat" => $issuedAt,
            "exp" => $time,
            "nbf" => $issuedAt-5,
            "data" => $data
        );
        $token = Authentication::generateToken($payload, $key);
        self::setCookie($name, $token, $time);
    }

    // -- HELPERS -- //
    private static function setCookie($name, $token, $time) {
        Cookie::set($name, $token, [
            "path" => "/",
            "domain" => $_SERVER["HTTP_HOST"],
            "expire" => $time,
            "httponly" => true,
            "secure" => $_SERVER['REQUEST_SCHEME'] === "https" ? true : false
        ]);
    }

    public static function logout() {
        unset($_COOKIE["iberbookedu_user"]);
        unset($_COOKIE["iberbookedu_profile"]);
        unset($_COOKIE["iberbookedu_staff"]);
        self::setCookie("iberbookedu_user", "", time()-86400);
        self::setCookie("iberbookedu_profile", "", time()-86400);
        self::setCookie("iberbookedu_staff", "", time()-86400);
    }

    public static function amIAllowed($role, $myProfile, $externalProfile) {
        // Is the same profile
        if ($myProfile->id === $externalProfile->id) {
            return true;
        }

        // Is mod of the same group
        if ($role === "mod" && $myProfile->group_id === $externalProfile->group_id) {
            return true;
        }
        return false;
    }
}
