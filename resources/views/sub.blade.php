<div class="subscribe-section ">
    <div class="row">You are not subscribed yet!!!</div><br/>
    <div class="row">
        <form method="post" id="payment-form" action="<?php echo route('subscribe'); ?>">
            @csrf
            <section>
                <div>Choose a Plan</div>
                <div class="col-md-6">
                    <input id="plan-monthly"  type="radio" name="plan" value="monthly-1" checked="checked"/>
                    <label for="html">Monthly</label><br>
                    <input id="plan-monthly"  type="radio" name="plan" value="yearly-1"/>
                    <label for="html">Yearly</label><br>
                </div>
                <div class="col-md6"></div><br/>
                <div class="bt-drop-in-wrapper">
                    <div id="bt-dropin"></div>
                </div>
            </section>

            <input id="nonce" name="payment_method_nonce" type="hidden" />
            <button id ="#submit-payment" class="button" type="submit">Submit Payment</button>
            <div id="loading-msg" style="display:none">In progress....</div>
        </form>

        <script src="https://js.braintreegateway.com/web/dropin/1.33.5/js/dropin.min.js"></script>
        <script>
            var form = document.querySelector('#payment-form');
            var client_token = "{{$clientGatewayToken}}";

            braintree.dropin.create({
                authorization: client_token,
                selector: '#bt-dropin',
                paypal: {
                    flow: 'vault'
                }
            }, function (createErr, instance) {
                if (createErr) {
                    console.log('Create Error', createErr);
                    return;
                }
                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    document.getElementById('loading-msg').style.display = "block";

                    instance.requestPaymentMethod(async function (err, payload) {
                        if (err) {
                            document.getElementById('loading-msg').style.display = "none";
                            console.log('Request Payment Method Error', err);
                            return;
                        }

                        document.querySelector('#nonce').value = payload.nonce;

                        data = {
                            payment_method_nonce : form.querySelector('input[name="payment_method_nonce"]').value,
                            plan: document.querySelector('input[name="plan"]:checked').value
                        }

                        let response = await fetch('<?php echo route('subscribe'); ?>', {
                            method: 'POST', // or 'PUT'
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': 'Bearer {{ auth()->user()->api_token }}'
                            },
                            body: JSON.stringify(data),
                        })

                        document.getElementById('loading-msg').style.display = "none";

                        const resp = JSON.parse(await response.text());
                        alert(resp.message)

                        location.reload();
                    });
                });
            });
        </script>
    </div>
</div>
