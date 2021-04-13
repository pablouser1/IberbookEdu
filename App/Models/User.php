<?php
namespace App\Models;

new \Leaf\Database;

class User extends \Leaf\Model {
	protected $table = "users";
    public $timestamps = false;

    protected $hidden = [
        'password'
    ];

    protected $appends = [
        'fullname'
    ];

    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }

    public function getFullNameAttribute() {
        return $this->name . " " . $this->surname;
    }

    public function isMod() {
        return $this->role === "mod";
    }
}
