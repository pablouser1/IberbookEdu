<?php
namespace App\Models;

new \Leaf\Database;

class Gallery extends \Leaf\Model {
	protected $table = "gallery";
    protected $with = ['group'];
    public $timestamps = false;

    public function group() {
        return $this->hasOne(Group::class, "id", "group_id");
    }
}
