<?php

namespace Models;

class Group extends Model {
	protected $table = "groups";
    protected $with = ['school'];
    public $timestamps = false;

    public function school() {
        return $this->hasOne(School::class, "id", "school_id");
    }
}
