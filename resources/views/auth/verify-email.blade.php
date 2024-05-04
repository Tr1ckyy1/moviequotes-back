<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MovieQuotes API</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .main-container {
            width: 100%;
            height:auto;
            min-height:100vh;
            background: linear-gradient(to bottom, #181623 0%, #191725 50%, #0D0B14 100%);
            padding:2rem;
        }

        .desktop-logo{
            display:none;
        }

        .img-container{
            margin:0 auto;
            width:fit-content;
        }
     

        .content-container{
            margin:2rem auto;
            color:white;
        }

        .content-container p{
            margin-bottom:2rem;
            color:white;
        }

        .content-container .link{
            display:block;
            background-color:#E31221;
            color:white;
            width:fit-content;
            padding:7px 13px;
            border-radius:4px;
            cursor:pointer;
            white-space:nowrap;
            text-decoration:none;
            margin-bottom:2rem;
        }

        .content-container .copy-link{
            color:#DDCCAA;
            display:block;
            word-wrap: break-word;
            text-decoration: none
        }

       .content-container a{
            color:inherit;
            background-color:inherit;
        }

    
        @media(min-width:600px){
            .main-container{
                padding:6rem 12rem;
            }   
            .mobile-logo{
                display:none;
            }
            .desktop-logo{
                display:block;
            }
        }
    </style>
</head>
<body>
<span style="opacity: 0"> {{ now() }} </span>
    <div class="main-container">
        <div class="img-container">
            <img class="desktop-logo" src="{{asset('desktop-logo.png')}}"/>
            <img class="mobile-logo" src="{{asset('mobile-logo.png')}}"/>
        </div>
        <div class="content-container">
            <p>{{__('email-verification.hello')}} {{$user}}!</p>
            <p>{{$text}}</p>
            <a class="link" href="{{$url}}">{{$linkText}}</a>
            <p>{{__('email-verification.copy_text')}}</p>
            <p class="copy-link">{{$url}}</p>
            <p>{{__('email-verification.support_us_text')}} support@moviequotes.ge</p>
            <p>{{__('email-verification.footer_text')}}</p>
        </div>
    </div>
    <span style="opacity: 0"> {{ now() }} </span>
</body>
</html>