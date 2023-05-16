<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class history extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth');
    }

    public function orders_history()
    {
        session()->flash('active', 'orders_history');
        $data = DB::table('orders_history')->select('*')->where('action','=','update by delegate')->orWhere('action','=','update')
        ->join('users','users.id','=','orders_history.user_id')->selectRaw('users.name')
        ->selectRaw('orders_history.id AS sid')->orderBy('orders_history.created_at', 'desc')->get();
        $oldvalues = array();
        $newvalues = array();
        foreach ($data as $order =>$value) {
            $old = json_decode($value->old);
            $new = json_decode($value->new);
            $oldvalues[$value->sid] = array();
            $newvalues[$value->sid] = array();
            foreach ($old as $old1=>$value1) {
                // dd($old1,$value1);
                if($old->$old1!=$new->$old1){
                    $oldvalues[$value->sid][$old1] = array();
                    // $oldvalues[$value->sid][$old1][$old->$old1] = array();
                    $newvalues[$value->sid][$old1] = array();
                    // $newvalues[$value->sid][$old1][$new->$old1] = array();
                    array_push($oldvalues[$value->sid][$old1],$old->$old1);
                    array_push($newvalues[$value->sid][$old1],$new->$old1);
                }
            }
        }
        return view('history.orders')->with('data', $data)->with('oldvalues', $oldvalues)->with('newvalues', $newvalues);
    }
    public function history_arcieve()
    {
        session()->flash('active', 'history_arcieve');
        $orders = DB::table('orders')->select('*')->where('on_archieve', '=', '1')->get();
        $police_duplicate = array();
        $polices = array();
        foreach ($orders as $order) {
            if (in_array($order->id_police, $polices)) {
                array_push($police_duplicate, $order->id_police);
            } else {
                array_push($polices, $order->id_police);
            }
        }
        $centers = DB::table('centers')->select('*')->get();
        $companies = DB::table('companies')->select('*')->get();
        $agents = DB::table('users')->select('id', 'name')->where('rank_id', '=', '8')->get();
        $delegates = DB::table('users')->select('id', 'name', 'commision')->where('rank_id', '=', '9')->get();
        $states = DB::table('order_state')->select('*')->get();
        $causes = DB::table('causes_return')->select('*')->get();
        session()->flash('orders', $orders);
        return view('history.archieve')
            ->with('centers', $centers)
            ->with('companies', $companies)
            ->with('agents', $agents)
            ->with('delegates', $delegates)
            ->with('states', $states)
            ->with('causes', $causes)
            ->with('police_duplicate', $police_duplicate);
    }
    // public function users_history()
    // {
    //     session()->flash('active', 'users_history');
    //     $data = DB::table('users_history')->select('*')
    //     ->join('users','users.id','=','users_history.user_id')->selectRaw('users.name')
    //     ->selectRaw('users_history.id AS sid')->get();
    //     $oldvalues = array();
    //     $newvalues = array();
    //     foreach ($data as $order =>$value) {
    //     if (!is_null($value->old)&& !is_null($value->new)) {
    //         $old = json_decode($value->old);
    //         $new = json_decode($value->new);
    //         $oldvalues[$value->sid] = array();
    //         $newvalues[$value->sid] = array();
    //         foreach ($old as $old1=>$value1) {
    //          if ($old->$old1!=$new->$old1) {
    //             $oldvalues[$value->sid][$old1] = array();
    //             // $oldvalues[$value->sid][$old1][$old->$old1] = array();
    //             $newvalues[$value->sid][$old1] = array();
    //             // $newvalues[$value->sid][$old1][$new->$old1] = array();
    //             array_push($oldvalues[$value->sid][$old1], $old->$old1);
    //             array_push($newvalues[$value->sid][$old1], $new->$old1);
    //          }
    //         }
    //     }elseif(!isset($value->old)&&$value->old==null&&isset($value->new)&&$value->old!=null){
    //         $old=null;
    //         $new = json_decode($value->new);
    //         $oldvalues[$value->sid] = null;
    //         $newvalues[$value->sid] = array();
    //         foreach ($new as $old1=>$value1) {
    //           // dd($old1,$value1);
    //             $oldvalues[$value->sid][$old1] = array();
    //             // $oldvalues[$value->sid][$old1][$old->$old1] = array();
    //             $newvalues[$value->sid][$old1] = array();
    //             // $newvalues[$value->sid][$old1][$new->$old1] = array();
    //             array_push($newvalues[$value->sid][$old1], $new->$old1);
    //         }
    //     }elseif(!isset($value->new)&&$value->new==null&&isset($value->old)&&$value->old!=null){
    //         $old = json_decode($value->old);
    //         $new = null;
    //         $oldvalues[$value->sid] = array();
    //         $newvalues[$value->sid] = null;
    //         foreach ($old as $old1=>$value1) {
    //             // dd($old1,$value1);
    //             $oldvalues[$value->sid][$old1] = array();
    //             // $oldvalues[$value->sid][$old1][$old->$old1] = array();
    //             $newvalues[$value->sid][$old1] = array();
    //             // $newvalues[$value->sid][$old1][$new->$old1] = array();
    //             array_push($oldvalues[$value->sid][$old1], $old->$old1);
    //         }

    //     }
    //     }
    //     return view('history.users')->with('data', $data)->with('oldvalues', $oldvalues)->with('newvalues', $newvalues);
    // }
}
