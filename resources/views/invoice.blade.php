<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('Invoice') }}</title>
</head>

<body onload="window.print()" onfocus="window.close()">
    <div id="main">
        {!! $html !!}
    </div>
</body>

</html>
