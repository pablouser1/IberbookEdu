<?php

namespace Models;

class Profile extends Model {
	protected $table = "profiles";
    public $timestamps = false;
    protected $with = ['group'];

    public function users() {
        return $this->belongsTo(User::class);
    }

    public function group() {
        return $this->hasOne(Group::class, "id", "group_id");
    }
}
