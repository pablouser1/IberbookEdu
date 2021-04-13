<?php
namespace App\Models;

new \Leaf\Database;

class Staff extends \Leaf\Model {
	protected $table = "staff";
    public $timestamps = false;
    protected $hidden = [
        'password'
    ];
}
