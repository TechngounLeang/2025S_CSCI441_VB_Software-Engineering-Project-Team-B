<?php
// Written & debugged by: Tech Ngoun Leang & Ratanakvesal Thong
// Tested by: Tech Ngoun Leang
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switchLang(string $lang)
    {
        // Check if the language is available in the config
        if (array_key_exists($lang, config('app.available_locales'))) {
            Session::put('locale', $lang);
            app()->setLocale($lang);
        }
        
        return back();
    }
}