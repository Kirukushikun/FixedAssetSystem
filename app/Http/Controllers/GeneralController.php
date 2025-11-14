<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class GeneralController extends Controller
{
	/**
	 * Decrypt String using unified encryption key
	 * @param  string $id - any valid encrypted id 
	 * @return string $id - decrypted value of parameter
	 */
    public static function decryptString($id)
    {
		try {
			$id = Crypt::decryptString($id);
		}
		catch (\Throwable $e) {
			return abort(500);
		}

		return $id;
    }
}
