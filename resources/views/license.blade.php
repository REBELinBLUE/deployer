<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>{{ Lang::get('app.name') }}</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
        <link rel="author" href="humans.txt" />

        <!-- Style -->
        <link href="{{ elixir('css/vendor.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ elixir('css/app.css') }}" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="{{ elixir('js/ie.js') }}"></script>
        <![endif]-->
    </head>
    <body class="login-page">

    <div class="login-box">
        <div class="login-logo">
            <b>{{ Lang::get('app.name') }}</b>
        </div>

        <div class="alert alert-danger">
            Your license has expired!
        </div>


        <div class="login-box-body">
            <p class="login-box-msg">In order to continue using Deployer please renew your annual subscription. The subscription charge is <strong>&pound;29.99</strong> per month.</p>
            <form action="{{ url('login') }}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <div class="form-group has-feedback">
                    <input type="text" class="form-control" size="20" placeholder="Card Number" name="card" value="{{ old('card') }}" required />
                    <span class="fa fa-credit-card form-control-feedback"></span>
                </div>

                <div class="form-group has-feedback">
                    <input type="text" class="form-control" size="4" placeholder="CVC" name="cvc" value="{{ old('cvc') }}" required />
                    <span class="fa fa-lock form-control-feedback"></span>
                </div>

                <div class="form-group">
                    <input type="text" size="6" placeholder="MM" />
                    <span> / </span>
                    <input type="text" size="6" placeholder="YYYY"/>
                </div>

                <div class="row">
                    <div class="col-xs-6">
                        <button type="button" class="btn btn-success btn-block btn-flat" onclick="alert('Nope!')">Submit Payment</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
        <script src="{{ elixir('js/vendor.js') }}"></script>
        <script src="{{ elixir('js/app.js') }}"></script>
    </body>
</html>
