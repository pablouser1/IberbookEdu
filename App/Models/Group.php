<?php
namespace App\Models;

new \Leaf\Database;

class Group extends \Leaf\Model {
	protected $table = "groups";
    protected $with = ['school'];
    public $timestamps = false;

    public function school() {
        return $this->hasOne(School::class, "id", "school_id");
    }
}
