<?php

use App\Model\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

function showPrice($price, $money = ' تومان')
{
    return number_format($price) . $money;
}


function filterInteger($value)
{
    return (int)$value;
}


function zeroHandler($num)
{
    if ($num <= 0)
        return 0;
    else
        return $num;
}


function getUrl($id, $type = 'product')
{
    if ($type == 'product')
        return "/product/" . $id;
}

function covertShamsiToMiladi($date)
{
    $exploded = explode("-", $date);
    $shamsiArray = \Morilog\Jalali\jDateTime::toGregorian($exploded[0], $exploded[1], $exploded[2]); // [2016, 5, 7]
    $shamsi = $shamsiArray[0] . "-" . $shamsiArray[1] . "-" . $shamsiArray[2] . " 00:00:00";
    return $shamsi;
}

function getBasket()
{
    $cartToken = false;
    $userCartByTokenStatus = false;

    if (Illuminate\Support\Facades\Auth::check()) {

        /* Get Cookie By User Id */
        $cart = App\Model\Cart::with(["products" => function ($query) {
            $query->with(["product" => function ($query) {
                $query->with("media");
            }]);
        }])->where("user_id", Illuminate\Support\Facades\Auth::user()->id)->first();

    } else {
        /* Get Cookie By Cookie Token */
        if (Illuminate\Support\Facades\Cookie::has("btk")) {
            $cartToken = Illuminate\Support\Facades\Cookie::get("btk");
        }
        if ($cartToken) {
            $userCartByTokenStatus = true;
        }
        $cart = App\Model\Cart::with(["products" => function ($query) {
            $query->with(["product" => function ($query) {
                $query->with("media");
            }]);
        }])->where("cart_token", $cartToken)->first();
    }

    return $cart;
}


function getBasketWithCookie()
{

    /* Get Cookie By Cookie Token */
    if (Illuminate\Support\Facades\Cookie::has("btk")) {
        $cartToken = Illuminate\Support\Facades\Cookie::get("btk");

        $cart = App\Model\Cart::with(["products" => function ($query) {
            $query->with(["product" => function ($query) {
                $query->with("media");
            }]);
        }])->where("cart_token", $cartToken)->first();

    } else {
        $cart = null;
    }


    return $cart;
}


function deleteUserCart()
{
    if (Illuminate\Support\Facades\Auth::check()) {

        /* Get Cookie By User Id */
        $cart = App\Model\Cart::where("user_id", Illuminate\Support\Facades\Auth::user()->id)->first();

    }
}

function getDiscount($price, $offer, $type = 1)
{
    if ($type == 1) {
        return ($price / 100) * $offer;
    }
    return $price - (($price / 100) * $offer);
}

function thumb($mediaObject, $size = 1, $realPreviewStatus = true)
{



    if ($size == 1) {
        $width = "150px";
    } else if ($size == 2) {
        $width = "300px";
    } else if ($size == 3) {
        $width = "600px";
    } else if ($size == 4) {
        $width = "";
    } else if ($size == 5) {
        $width = "";
    }

    if (!is_null($mediaObject)) {

        $date = explode(" ", $mediaObject->created_at);
        $date = explode("-", $date[0]);
        $m = $date[1];
        $d = $date[2];
        $y = $date[0];

        if (in_array($mediaObject->extension, ["png", "jpg", "jpeg", "gif"])) {
            $mediaTags = '<img style="width:' . $width . ' !important;" src="/media/' . $y . '/' . $m . '/' . $d . '/' . $mediaObject->hash . '.' . $mediaObject->extension . '" class="img-responsive fullwidth" alt="' . $mediaObject->name . '">';
        } else if (in_array($mediaObject->extension, ["mp4"])) {
            if ($realPreviewStatus) {
                $mediaTags = '<video controls> <source src="/media/' . $y . '/' . $m . '/' . $d . '/' . $mediaObject->hash . '.' . $mediaObject->extension . '" type="video/mp4"> </video> ';
            } else {
                $mediaTags = '<a href="/media/' . $y . '/' . $m . '/' . $d . '/' . $mediaObject->hash . '.' . $mediaObject->extension . '" download="/media/' . $y . '/' . $m . '/' . $d . '/' . $mediaObject->hash . '.' . $mediaObject->extension . '"><img src="/assets/img/video.png" class="img-responsive fullwidth" alt="' . $mediaObject->name . '"></a>';

            }
        } else if (in_array($mediaObject->extension, ["mp3"])) {
            if ($realPreviewStatus) {
                $mediaTags = '<audio controls> <source src="/media/' . $y . '/' . $m . '/' . $d . '/' . $mediaObject->hash . '.' . $mediaObject->extension . '" type="audio/mpeg"> </audio> ';
            } else {
                $mediaTags = '<a href="/media/' . $y . '/' . $m . '/' . $d . '/' . $mediaObject->hash . '.' . $mediaObject->extension . '" download="/media/' . $y . '/' . $m . '/' . $d . '/' . $mediaObject->hash . '.' . $mediaObject->extension . '"><img src="/assets/img/music.png" class="img-responsive fullwidth" alt="' . $mediaObject->name . '"></a>';
            }
        } else if (in_array($mediaObject->extension, ["pdf"])) {
            $mediaTags = '<a href="/media/' . $y . '/' . $m . '/' . $d . '/' . $mediaObject->hash . '.' . $mediaObject->extension . '" download="/media/' . $y . '/' . $m . '/' . $d . '/' . $mediaObject->hash . '.' . $mediaObject->extension . '"><img src="/assets/img/pdf.png" class="img-responsive fullwidth" alt="' . $mediaObject->name . '"></a>';
        } else if (in_array($mediaObject->extension, ["doc", "docx"]) != -1) {
            $mediaTags = '<a href="/media/' . $y . '/' . $m . '/' . $d . '/' . $mediaObject->hash . '.' . $mediaObject->extension . '" download="/media/' . $y . '/' . $m . '/' . $d . '/' . $mediaObject->hash . '.' . $mediaObject->extension . '"><img src="/assets/img/word.png" class="img-responsive fullwidth" alt="' . $mediaObject->name . '"></a>';
        }


        if ($size == 5) {
            $mediaTags = '/media/' . $y . '/' . $m . '/' . $d . '/' . $mediaObject->hash . '.' . $mediaObject->extension;

            return $mediaTags;

        } else {
            return $mediaTags;
        }

    } else {
        return '<img style="width:' . $width . ' !important;" src="/media/default.jpg" class="img-responsive fullwidth" alt="فایل چند رسانه ای یافت نشد">';
    }


}


function status($status)
{
    if ($status == 0)
        return "<span class='label label-danger'>غیر فعال</span>";
    elseif ($status == 1)
        return "<span class='label label-success'>فعال</span>";
//    elseif ($status == 2)
//        return "<span class='label label-warning'>در انظار تایید</span>";
}

function offStatus($status)
{
    if ($status == 1)
        return "<span class='label label-success'>مبلغ</span>";
    elseif ($status == 2)
        return "<span class='label label-success'>درصد</span>";
}

function orderStatus($status)
{
    if ($status == 0)
        return "<span class='label label-danger'>در انتظار پرداخت</span>";
    elseif ($status == 1)
        return "<span class='label label-success'>پرداخت شده</span>";
    elseif ($status == 2)
        return "<span class='label label-warning'>تایید سفارش</span>";
    elseif ($status == 3)
        return "<span class='label label-warning'>عدم تایید سفارش</span>";
    elseif ($status == 4)
        return "<span class='label label-warning'>در انتظار ارسال</span>";
    elseif ($status == 5)
        return "<span class='label label-warning'>ارسال شده</span>";
    elseif ($status == 6)
        return "<span class='label label-warning'>تحویل داده شده</span>";
    elseif ($status == 10)
        return "<span class='label label-warning'>مرجوع شده</span>";

}


function time_elapsed_string($ptime)
{
    $etime = time() - $ptime;

    if ($etime < 1) {
        return '0 ثانبه';
    }

    $a = array(365 * 24 * 60 * 60 => 'سال',
        30 * 24 * 60 * 60 => 'ماه',
        24 * 60 * 60 => 'روز',
        60 * 60 => 'ساعت',
        60 => 'دقیقه',
        1 => 'ثانیه'
    );

    foreach ($a as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            return $r . ' ' . ($str) . ' قبل';
        }
    }
}

function prdOptionType($id)
{
    if ($id == 1)
        return "<span class='label label-danger'>رنگ</span>";
    elseif ($id == 2)
        return "<span class='label label-success'>عکس</span>";
    elseif ($id == 3)
        return "<span class='label label-warning'>نوشته</span>";

}


function getCurrentCartForCurrentUser(){


    $userLoginStatus = false;
    /* Cart Token Will Stored Here */
    $cartToken = null;
    $userCartByTokenStatus = false;

    if(Auth::check()){
        $userLoginStatus = true;
    }

    if($userLoginStatus === true){

        /* Get Cookie By User Id */
        $cart = Cart::with(["products"=>function($query){
            $query->with(["product"=>function($query){
                $query->with("media");
            }]);
        }])->where("user_id",Auth::user()->id)->first();

    }else {

        /* Get Cookie By Cookie Token */
        if(Cookie::has("btk")){
            $cartToken = Cookie::get("btk");
        }

        if($cartToken){
            $userCartByTokenStatus = true;
        }
        $cart = Cart::with(["products"=>function($query){
            $query->with(["product"=>function($query){
                $query->with("media");
            }]);
        }])->where("cart_token",$cartToken)->first();
    }

    return $cart;

}

function offerDay($start, $end)
{


    $diff = strtotime($end) - strtotime($start);


    $years = floor($diff / (365 * 60 * 60 * 24));
    $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
    $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

    $hours = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24) / (60 * 60));

    $minuts = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);

    $seconds = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60 - $minuts * 60));

//    $r = printf("%d years, %d months, %d days, %d hours, %d minuts\n, %d seconds\n", $years, $months, $days, $hours, $minuts, $seconds);


    $output = '<div class="timing-wrapper">
                <div class="box-wrapper">
                    <div class="date box">

                                                                                    <span class="key">' . $days . '</span>
                                                                                    <span class="value">Days</span>
                                                                                </div>
                                                                            </div>

                                                                            <div class="box-wrapper">
                                                                                <div class="hour box">
                                                                                    <span class="key">' . $hours . '</span>
                                                                                    <span class="value">HRS</span>
                                                                                </div>
                                                                            </div>

                                                                            <div class="box-wrapper">
                                                                                <div class="minutes box">
                                                                                    <span class="key">' . $minuts . '</span>
                                                                                    <span class="value">MINS</span>
                                                                                </div>
                                                                            </div>

                                                                            <div class="box-wrapper hidden-md">
                                                                                <div class="seconds box">
                                                                                    <span class="key">' . $seconds . '</span>
                                                                                    <span class="value">SEC</span>
                                                                                </div>
                                                                            </div>
                                                                        </div>';

    return ($output);
}


function offerType($type, $offer)
{

    if ($type == 1) {
        return number_format($offer) ." ". 'تومان';
    } elseif ($type == 2) {
        return $offer . '%';
    }
}


function offVal($type,$value,$price){
    if($type==1 ){
        return $value;
    }else
        return (($value*$price)/100);
}