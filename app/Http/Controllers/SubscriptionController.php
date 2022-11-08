<?php

namespace App\Http\Controllers;

use App\Http\PaymentMethod;
use App\Http\Services\SubscriptionService;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Enums\Plan;
use InvalidArgumentException;

/**
 * Class SubscriptionController
 */
class SubscriptionController extends Controller
{
    /**
     * SubscriptionController constructor.
     *
     * @param SubscriptionService $service
     */
    public function __construct(
        private SubscriptionService $service
    ) {
    }

    /**
     * Create a subscription
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'payment_method_nonce' => 'required',
            'plan'                 => 'required',
        ]);

        $plan = $this->validatePlan($request);

        $this->service->subscribe(
            $request->user(),
            (new PaymentMethod)->setNonce($request->payment_method_nonce),
            $plan
        );

        return response()->json([
            'success' => true,
            'message' => 'Subscribed successfully',
        ]);
    }

    /**
     * Cancel the current subscription
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function cancel(Request $request)
    {
        $this->service->cancel($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Subscription cancelled successfully',
        ]);
    }

    /**
     * @return Plan
     * @throws \Throwable
     */
    private function validatePlan(Request $request): Plan
    {
        $plan = Plan::tryFrom($request->plan);

        throw_if(!$plan, new InvalidArgumentException('Invalid plan'));

        return $plan;
    }
}
