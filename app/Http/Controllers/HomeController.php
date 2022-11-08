<?php

namespace App\Http\Controllers;

use App\Http\Services\SubscriptionService;
use App\Models\User;
use Braintree\Gateway;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(
        public SubscriptionService $subscriptionService
    ) {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home', [
            'clientGatewayToken' => app(Gateway::class)->clientToken()->generate(),
            'activeSubscription'                => auth()->user()->subscription,
        ]);
    }
}
