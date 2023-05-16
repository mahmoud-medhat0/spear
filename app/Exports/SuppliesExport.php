<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SuppliesExport implements FromView, ShouldAutoSize
{
    public $orders;

    public function view(): View
    {
        $id = request()->id;
        $payed = request()->payed;
        $r = request()->except('_token', 'example1_length');
        $orders = DB::table('orders')->select('orders.id', 'orders.id_company', 'id_police', 'name_client', 'phone', 'phone2', 'delegate_id', 'orders.delegate_supply')
            ->where('delegate_id', '=', $id)
            ->where('delegate_supply', '=', '0')
            ->where('status_id', '=', '1')
            ->where('delegate_supply', '=', '0')
            ->where('delegate_id', '=', $id)
            ->orWhere('status_id', '=', '2')
            ->where('delegate_supply', '=', '0')
            ->where('delegate_id', '=', $id)
            ->orWhere('status_id', '=', '8')
            ->where('delegate_supply', '=', '0')
            ->where('delegate_id', '=', $id)
            ->orWhere('status_id', '=', '7')
            ->where('delegate_supply', '=', '0')
            ->where('delegate_id', '=', $id)
            ->orWhere('status_id', '=', '4')
            ->where('delegate_supply', '=', '0')
            ->where('delegate_id', '=', $id)
            ->orWhere('status_id', '=', '5')
            ->where('delegate_supply', '=', '0')
            ->where('delegate_id', '=', $id)
            ->join('order_state', 'order_state.id', '=', 'orders.status_id')->selectRaw('order_state.state')
            ->selectRaw('orders.address')->selectRaw('orders.cost')
            ->join('companies', 'orders.id_company', '=', 'companies.id')->selectRaw('companies.name AS company_name')
            ->join('users as agents', 'agents.id', '=', 'orders.delegate_id')->selectRaw('agents.name AS agent_name')
            ->selectRaw('agents.commision')
            ->get();
        // $id = $request['agent_id'];
        // $agentname = DB::table('users')->select('name')->where('id','=',$id)->get()[0]->name;
        return view('table', [
            'orders' => $orders,
            'r' => $r,
            'payed' => $payed
        ]);
    }
}
