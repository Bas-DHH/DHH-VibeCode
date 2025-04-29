<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\BusinessRegistrationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class RegisteredBusinessController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/RegisterBusiness');
    }

    public function store(BusinessRegistrationRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'admin',
            ]);

            $now = Carbon::now();
            $business = Business::create([
                'business_name' => $request->business_name,
                'created_by' => $user->id,
                'trial_starts_at' => $now,
                'trial_ends_at' => $now->copy()->addDays(14),
            ]);

            DB::commit();

            event(new Registered($user));
            Auth::login($user);

            return redirect()->route('admin.dashboard');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Business registration failed: ' . $e->getMessage());
            
            return back()->withInput($request->except('password', 'password_confirmation'));
        }
    }
} 