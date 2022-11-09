<?php

namespace Tests\Unit;

use App\Http\Repositories\SubscriptionRepositoryInterface;
use App\Http\Services\SubscriptionService;
use App\Http\SubscribeParams;
use App\Models\Subscription;
use App\Models\User;
use PHPUnit\Framework\TestCase;
use App\Http\Enums\Plan;

class SubscriptionServiceTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_subscribe_is_successful()
    {
        $user = $this->createPartialMock(User::class, ['isSubscribed']);
        $user->expects($this->once())->method('isSubscribed')->willReturn(false);

        $newSubscription = new Subscription;
        $newSubscription->id = 2;

        $mockRepo = $this->createMock(SubscriptionRepositoryInterface::class);
        $mockRepo->method('subscribe')->willReturn($newSubscription);

        $service = new SubscriptionService($mockRepo);
        $expectedSub = $service->subscribe($user, new SubscribeParams(Plan::MONTHLY, '123123'));
        $this->assertEquals($expectedSub->id, $newSubscription->id);
    }

    public function test_subscribe_fails_user_is_already_subscribed()
    {
        $user = $this->createPartialMock(User::class, ['isSubscribed']);
        $user->expects($this->once())->method('isSubscribed')->willReturn(true);

        $newSubscription = new Subscription;
        $newSubscription->id = 2;

        $mockRepo = $this->createMock(SubscriptionRepositoryInterface::class);
        $mockRepo->method('subscribe')->willReturn($newSubscription);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User is already subscribed');
        $service = new SubscriptionService($mockRepo);
        $service->subscribe($user, new SubscribeParams(Plan::MONTHLY, '123123'));
    }

    public function test_subscribe_fails_no_payment_method_nonce_or_token_given()
    {
        $user = $this->createPartialMock(User::class, ['isSubscribed']);
        $user->expects($this->once())->method('isSubscribed')->willReturn(false);

        $newSubscription = new Subscription;
        $newSubscription->id = 2;

        $mockRepo = $this->createMock(SubscriptionRepositoryInterface::class);
        $mockRepo->method('subscribe')->willReturn($newSubscription);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Payment method information is required');
        $service = new SubscriptionService($mockRepo);
        $service->subscribe($user, new SubscribeParams(Plan::MONTHLY, null));
    }

    public function test_cancel_subscription_successful()
    {
        $user = $this->createPartialMock(User::class, ['isSubscribed']);
        $user->expects($this->once())->method('isSubscribed')->willReturn(true);

        $mockRepo = $this->createMock(SubscriptionRepositoryInterface::class);
        $mockRepo->expects($this->once())->method('cancel');

        $service = new SubscriptionService($mockRepo);
        $service->cancel($user, new SubscribeParams(Plan::MONTHLY, '123123'));
    }

    public function test_cancel_subscription_fails_user_has_no_active_subscription()
    {
        $user = $this->createPartialMock(User::class, ['isSubscribed']);
        $user->expects($this->once())->method('isSubscribed')->willReturn(false);

        $mockRepo = $this->createMock(SubscriptionRepositoryInterface::class);
        $mockRepo->expects($this->never())->method('cancel');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User is not subscribed. Nothing to cancel');
        $service = new SubscriptionService($mockRepo);
        $service->cancel($user, new SubscribeParams(Plan::MONTHLY, '123123'));
    }
}
