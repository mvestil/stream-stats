@extends('layouts.app')

@section('content')
    <div class="container">
        @if ($activeSubscription)
            <div class="row">
                <div class="col-md-4">You are subscribed to the plan <strong>{{ $activeSubscription->plan_id }} </strong></div>
                <div class="col-md-6">
                    <button onclick="cancelSub()">Cancel your subscription</button>
                </div>
            </div>
            <br/>

            <div class="row">
                <span class="border col-md-12">Subscribed Metrics 1 (Placeholder)</span>
                <span class="border col-md-12">Subscribed Metrics 2 (Placeholder)</span>
            </div>
        @else
            @include('sub')
        @endif

    </div>
@endsection
