<?php

namespace Controllers;

use Models\Theme;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ThemeController extends \Leaf\ApiController {
	public function all() {
        $themes = Theme::all();
        response($themes);
	}

    public function one($id) {
        try {
            $theme = Theme::findOrFail($id);
            response($theme);
        }
        catch (ModelNotFoundException $e) {
            throwErr("Theme not found", 404);
        }
    }
}
