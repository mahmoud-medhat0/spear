@php
use Picqer\Barcode\BarcodeGeneratorPNG;
$generator = new BarcodeGeneratorPNG();
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('police/style3.css') }}">
    <title>{{ $title }}</title>
</head>

<body>
    <div class="a4-size">
        @foreach ($orders as $order)
        <div class="table-1">
            <h1><i>MarExpress</i></h1>
            <table class="content-1" style="direction: rtl;">
                <tr class="title-1">
                    <td>
                        <h2>اسم العميل</h2>
                        <h2>Client Name</h2>
                    </td>
                    <td>
                        <h2>التاريخ</h2>
                        <h2>Date</h2>
                    </td>
                    <td>
                        <h2>رقم هاتف العميل</h2>
                        <h2>Client- Phone Number</h2>
                    </td>
                    <td>
                        <h2>رقم هاتف العميل</h2>
                        <h2>Client- Phone Number</h2>
                    </td>
                    <td>
                        <h2>العنوان المرسل الينا</h2>
                        <h2>Address</h2>
                    </td>
                </tr>
                <tr class="input-1">
                    <td>{{ $order->name_client }}</td>
                    <td>{{ $order->date }}</td>
                    <td>{{ $order->phone }}</td>
                    <td>{{ $order->phone2 }}</td>
                    <td>{{ $order->address }}</td>
                </tr>
            </table>

            <table class="content-2" style="direction: rtl;">
                <tr class="title-2">
                    <td>
                        <h2>باركود بولصة الشحن</h2>
                        <h2>Policy barcode</h2>
                    </td>
                </tr>
                <tr class="input-2">
                    <td>
                        <h2>{{ $order->id_police }}</h2>
                        @php
                            $barcode = $generator->getBarcode($order->id_police, $generator::TYPE_CODE_128);
                        @endphp
                        <h2><img src="data:image/png;base64,{{ base64_encode($barcode) }}" alt="Barcode"></h2>
                    </td>
                </tr>
            </table>

            <table class="content-3" style="direction: rtl;">
                <tr class="title-3">
                    <td>
                        <h2>الراسل</h2>
                        <h2>Sender</h2>
                    </td>
                    <td>
                        <h2>اسم المنتج</h2>
                        <h2>Product Name</h2>
                    </td>
                    <td>
                        <h2>السعر</h2>
                        <h2>Price</h2>
                    </td>
                </tr>
                <tr class="input-3">
                    <td>{{ $order->sender }}</td>
                    <td>{{ $order->name_product }}</td>
                    <td>{{ $order->cost }}</td>
                </tr>
            </table>

            <table class="content-4" style="direction: rtl;">
                <tr class="title-4">
                    <td>
                        <h2>الملاحظات</h2>
                        <h2>Notes</h2>
                    </td>
                    <td>
                        <h2>التعليمات</h2>
                        <h2>Instructions</h2>
                    </td>
                    <td>
                        <h2>التعليمات</h2>
                        <h2>Instructions</h2>
                    </td>
                </tr>
                <tr class="input-4">
                    <td>{{ $order->notes }}</td>
                    <td>{{ $order->special_intructions }}</td>
                    <td>{{ $order->special_intructions2 }}</td>
                </tr>
            </table>
        </div>
        <p>----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------</p>
        @endforeach
    </div>
</body>

</html>
