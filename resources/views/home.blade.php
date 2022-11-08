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

            <script>
                function cancelSub() {
                    const url = '{{ route('sub.cancel') }}';

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer {{ auth()->user()->api_token }}'
                        },
                    }).then((response) => response.json())
                        .then(data => {
                            alert(data.message)
                            location.reload()
                        })
                }
            </script>
        @else
            @include('sub')
        @endif

    </div>
@endsection
