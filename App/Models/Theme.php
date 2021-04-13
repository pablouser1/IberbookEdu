<?php
namespace App\Models;

use Leaf\FS;

new \Leaf\Database;

class Theme extends \Leaf\Model {
	protected $table = "themes";
    public $timestamps = false;

    protected $appends = [
        "details"
    ];

    public function getDetailsAttribute() {
        $file = FS::readFile(storage_path("/app/themes/".$this->name."/theme.json"));
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
