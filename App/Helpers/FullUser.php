<?php
namespace App\Helpers;
use App\Models\User;
use App\Models\Profile;

class FullUser {
    private static function getUser($userid) {
        $user = User::where("id", "=", $userid)->first();
        return $user;
    }
    public static function basic($group) {
        $full = [];
        $profiles = Profile::where("group_id", "=", $group)->get();
        foreach ($profiles as $profile) {
            $user = self::getUser($profile->user_id);
            $full[] = [
                "name" => $user->name,
                "surname" => $user->surname,
                "type" => $user->type,
                "subject" => $profile->subject
            ];
        }
        return $full;
    }

    public static function full($group) {
        $full = [];
        $profiles = Profile::all()->where("group_id", "=", $group);
        foreach ($profiles as $profile) {
            $user = self::getUser($profile->user_id);
            $full[] = [
                "id" => $profile->id,
                "name" => $user->name,
                "surname" => $user->surname,
                "type" => $user->type,
                "subject" => $profile->subject,
                "photo" => $profile->photo,
                "video" => $profile->video,
                "link" => $profile->link,
                "quote" => $profile->quote,
                "subject" => $profile->subject
            ];
        }
        return $full;
    }
}
