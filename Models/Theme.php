<?php

namespace Models;

class Theme extends Model {
	protected $table = "themes";
    public $timestamps = false;

    protected $appends = [
        "details"
    ];

    public function getDetailsAttribute() {
        $file = file_get_contents(get_theme($this->name) . "/theme.json");
        if ($file) {
            $json = json_decode($file, true);
            return [
                "description" => $json["description"],
                "zip" => $json["zip"],
                "banner" => $json["banner"]
            ];
        }
        return null;
    }
}
