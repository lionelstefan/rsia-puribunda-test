<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/js/all.min.js" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Document</title>
</head>
<style>
    .centered {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
</style>
<body style="background-color:black">
    <div class="text-center">
        <img style="border-radius: 20%; height: 200%; width: 30%;" src="{{url('/logo_dragon_gold_large.png')}}" class="rounded" alt="logo_dragon_gold_large">
    </div>      
    <div class="d-grid gap-4 col-9 mx-auto centered">
        <a class="btn btn-dark" style="color:#FFD700; padding:5%" href="{{ route('frontend.getPoint') }}"><i class="fa-solid fa-gift"></i> 
            <div>
                Direct to get point page
            </div>
        </a>
        <a class="btn btn-dark" style="color:#FFD700; padding:5%" href="{{ route('frontend.redeemPointPage') }}"><i class="fa fa-percent" aria-hidden="true"></i> 
            <div>
                Direct to redeem point page
            </div>            
        </a>                
        <a class="btn btn-dark" style="color:#FFD700; padding:5%" href="{{ url('logout') }}"><i class="fa-solid fa-right-from-bracket fa-flip-horizontal"></i>
            <div>
                Logout
            </div>
        </a>                
    </div>
</body>
</html>
