<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    public function switchLanguage($lang)
{
    // Validate language code
    if (!in_array($lang, ['en', 'km'])) {
        $lang = 'en'; // Default to English if invalid language
    }
    
    // Store language in session
    session(['locale' => $lang]);
    
    // Set the locale
    App::setLocale($lang);
    
    // Redirect back to the previous page
    return redirect()->back();
}
}
