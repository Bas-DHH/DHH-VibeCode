@extends('layouts.app')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <h2 class="text-2xl font-bold mb-4">Subscription Plans</h2>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ($plans as $planId => $plan)
                <div class="border rounded-lg p-6">
                    <h3 class="text-xl font-semibold mb-2">{{ $plan['description'] }}</h3>
                    <p class="text-gray-600 mb-4">€{{ number_format($plan['amount']->getAmount() / 100, 2) }} / {{ $plan['interval'] }}</p>

                    @if (!$subscription)
                        <form action="{{ route('subscriptions.create') }}" method="POST">
                            @csrf
                            <input type="hidden" name="plan" value="{{ $planId }}">
                            <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                Subscribe
                            </button>
                        </form>
                    @elseif ($subscription->plan === $planId)
                        <div class="text-green-600 font-semibold mb-2">Current Plan</div>
                        @if ($subscription->cancelled())
                            <form action="{{ route('subscriptions.resume') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                                    Resume Subscription
                                </button>
                            </form>
                        @else
                            <form action="{{ route('subscriptions.cancel') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                                    Cancel Subscription
                                </button>
                            </form>
                        @endif
                    @else
                        <form action="{{ route('subscriptions.swap') }}" method="POST">
                            @csrf
                            <input type="hidden" name="plan" value="{{ $planId }}">
                            <button type="submit" class="w-full bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                                Change Plan
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection 