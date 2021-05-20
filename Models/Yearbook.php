<?php

namespace Models;

class Yearbook extends Model {
	protected $table = "yearbooks";
    public $timestamps = false;
    protected $with = ['group'];

    public function group() {
        return $this->hasOne(Group::class, "id", "group_id");
    }
}
