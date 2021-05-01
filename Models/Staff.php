<?php

namespace Models;

class Staff extends Model {
	protected $table = "staff";
    public $timestamps = false;
    protected $hidden = [
        'password'
    ];
}
