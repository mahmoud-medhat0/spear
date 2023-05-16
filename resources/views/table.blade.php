<table>
    <thead>
        <tr>
            <th>payed</th>
            <th>id</th>
            <th>company</th>
            <th>id_police</th>
            <th>name_client</th>
            <th>phone</th>
            <th>phone2</th>
            <th>address</th>
            <th>cost</th>
            <th>agent commision</th>
            <th>total</th>
            <th>status</th>
        </tr>
    </thead>
    <?php
    $total=0;
    $total1=0;
    $total2=0;
    ?>
    <tbody>
        @foreach ($orders as $order)
        <tr>
            <td>@foreach ($r as $r1=>$v )
                @if ($order->id==$v)
                {{ "تم دفعه" }}
                @endif
                @endforeach
            </td>
            <td>{{ $order->id }}</td>
            <td>{{ $order->company_name }}</td>
            <td>{{ $order->id_police }}</td>
            <td>{{ $order->name_client }}</td>
            <td>{{ $order->phone }}</td>
            <td>{{ $order->phone2 }}</td>
            <td>@isset($order->address)
                {{ $order->address }}
                @endisset
            </td>
            <td>{{ $order->cost }}
                <?php $total+=$order->cost;?>
            </td>
            <td>
                @isset($order->commision)
                {{ $order->commision }}
                <?php $total1+=$order->commision;?>
                @endisset
            </td>
            <td>
                @isset($order->commision)
                {{ $order->cost - $order->commision}}
                <?php $total2+=$order->cost - $order->commision;?>
                @endisset
            </td>
            <td>{{ $order->state }}</td>
        </tr>
        @endforeach
        <tr>
            <td>-</td>
            <td>@isset($orders[0]->company_name)
                {{ $orders[0]->company_name}}
                @endisset
            </td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>{{ $total }}</td>
            <td>{{ $total1 }}</td>
            <td>{{ $total2 }}</td>
            <td>-</td>
        </tr>
        <tr>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>{{ "payed:" }}</td>
            <td>{{ $payed }}</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
        </tr>
    </tbody>
</table>
