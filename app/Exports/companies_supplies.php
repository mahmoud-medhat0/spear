<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class companies_supplies implements FromView,ShouldAutoSize
{
    public $orders;

    public function view() : View
    {
        $id = request()->id;
        $payed = request()->payed;
        $r =request()->except('_token','example1_length');
        $orders = DB::table('orders')->select('id_police','name_client','cost','delegate_supply','phone','phone2')
        ->join('companies','orders.id_company','=','companies.id')->selectRaw('companies.name AS company_name')
        ->selectRaw('companies.commission')->selectRaw('orders.id')
        ->join('order_state','order_state.id','=','orders.status_id')->selectRaw('order_state.state')
        ->where('id_company','=',$id)->where('delegate_supply','=','1')
        ->where('company_supply','=','0')->get();
        // $id = $request['agent_id'];
        // $agentname = DB::table('users')->select('name')->where('id','=',$id)->get()[0]->name;
        return view('table',[
            'orders'=>$orders,
            'r'=>$r,
            'payed'=>$payed
        ]);
    }
}
