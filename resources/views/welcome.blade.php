<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Gestión</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://printjs-4de6.kxcdn.com/print.min.css">
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    </head>
    <body style="background:#E5E7E9">
        <div id="app">

        </div>
    </body>
        <script src="https://kit.fontawesome.com/0856a1cba4.js" crossorigin="anonymous"></script>
        <script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>


        {{-- <script type="text/javascript">
            window.onload = function()
            {
                console.log("soy el onload")
                 document.getElementById("lol").focus();
            }
        </script> --}}
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
        <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

        <script src="{{ mix('v/12/public/js/app.js') }}" type="text/javascript"></script>
</html>
