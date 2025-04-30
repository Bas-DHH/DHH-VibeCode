<?php

namespace App\Http\Controllers;

use App\Services\LanguageService;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'language' => ['required', 'string', 'in:nl,en'],
        ]);

        LanguageService::setUserLanguage(auth()->user(), $request->language);

        return back();
    }
} 