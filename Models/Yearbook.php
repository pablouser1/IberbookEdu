<?php

namespace Models;

class Yearbook extends Model {
	protected $table = "yearbooks";
    public $timestamps = false;
    protected $with = ['group'];

    protected $appends = [
        'zip'
    ];

    public function group() {
        return $this->hasOne(Group::class, "id", "group_id");
    }

    public function getZipAttribute() {
        $zip = storage_path("app/yearbooks/".$this->id."/yearbook.zip");
        if (is_file($zip)) {
            return true;
        }
        return false;
    }
}
