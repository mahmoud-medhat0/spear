<?php

namespace App\Http\Controllers;

use App\Rules\vaildcenter;
use Illuminate\Http\Request;
use App\Imports\ordersimport;
use Illuminate\Support\Facades\DB;
use App\Imports\compnyordersimport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rules\File;

class iscompanycontroller extends Controller
{
    public function __construct()
    {
        session()->flash('parent', 'company');
        return $this->middleware(['auth', 'iscompany']);
    }
    public function index()
    {
        session()->flash('active', 'orderscompany');
        $orders = DB::table('orders')->where('on_archieve', '=', '0')->where('id_company', '=', Auth::user()->company_id)->orderBy('created_at', 'desc')->select('*')->get();
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
        return view('iscompany.orders')
            ->with('centers', $centers)
            ->with('companies', $companies)
            ->with('agents', $agents)
            ->with('delegates', $delegates)
            ->with('states', $states)
            ->with('causes', $causes)
            ->with('police_duplicate', $police_duplicate);
    }
    public function add_manual()
    {
        session()->flash('active','companyaddmanual');
        $centers = DB::table('centers')->select('*')->get();
        return view('iscompany.addmanual')->with('centers',$centers);
    }
    public function addfromsheet()
    {
        session()->flash('active', 'companyaddsheet');
        return view('iscompany.addfromsheet');
    }
    public function storesheet()
    {
        request()->validate([
            'sheet' => ['required', File::types(['xls', 'xlsx'])]
        ]);
        Excel::import(new ordersimport, request()->file('sheet'));
        return redirect()->back()->with('success', 'data imported succesfull to database');
    }
    public function store_m(Request $request)
    {
        $validate = [
            'id_police' => ['required'],
            'name_client' => ['required'],
            'phone' => ['required'],
            'address' => ['required'],
            'cost' => ['required', 'int'],
            'center_id'=>['required',new vaildcenter]
        ];
        $request->validate($validate);
        $special_intructions2 = DB::table('companies')->select('special_intructions')->where('id', '=', auth()->user()->company_id)->get()[0]->special_intructions;
        DB::table('orders')->insert([
            'id_company' =>auth()->user()->company_id,
            'id_police' => $request->id_police,
            'name_client' => $request->name_client,
            'phone' => $request->phone,
            'special_intructions2' => $special_intructions2,
            'date' => now(),
            'address' => $request['address'],
            'cost' => $request['cost'],

        ]);
        $id = DB::table('orders')->select('id')->latest('created_at')->where('id_company', '=', auth()->user()->company_id)->where('id_police', '=', $request->id_police)->get()[0]->id;
        if ($request['phone2'] == null) {
            $request['phone2'] = '*';
        } else {
            DB::table('orders')->where('id', '=', $id)->update([
                'phone2' => $request['phone2']
            ]);
        }
        if ($request['center_id'] == 'none') {
        } else {
            DB::table('orders')->where('id', '=', $id)->update([
                'center_id' => $request['center_id']
            ]);
        }

        if ($request['agent_id'] == 'none' || $request['agent_id'] == 'وكيل') {
        } else {
            DB::table('orders')->where('id', '=', $id)->update([
                'agent_id' => $request['agent_id']
            ]);
        }
        if ($request['delegate_id'] == 'none') {
        } else {
            DB::table('orders')->where('id', '=', $id)->update([
                'delegate_id' => $request['delegate_id']
            ]);
        }
        if ($request['salary_charge'] == null) {
            $request['salary_charge'] = '*';
        } else {
            DB::table('orders')->where('id', '=', $id)->update([
                'salary_charge' => $request['salary_charge']
            ]);
        }
        if ($request['notes'] == null) {
        } else {
            DB::table('orders')->where('id', '=', $id)->update([
                'notes' => $request['notes']
            ]);
        }
        if ($request['special_intructions'] == null) {
        } else {
            DB::table('orders')->where('id', '=', $id)->update([
                'special_intructions' => $request['special_intructions']
            ]);
        }
        if ($request['name_product'] == null) {
        } else {
            DB::table('orders')->where('id', '=', $id)->update([
                'name_product' => $request['name_product']
            ]);
        }
        if ($request['sender'] == null) {
            $request['sender'] = '*';
        } else {
            DB::table('orders')->where('id', '=', $id)->update([
                'sender' => $request['sender']
            ]);
        }
        if ($request['weghit'] == null) {
        } else {
            DB::table('orders')->where('id', '=', $id)->update([
                'weghit' => $request['weghit']
            ]);
        }
        if ($request['open'] == null) {
            $request['open'] = '*';
        } else {
            DB::table('orders')->where('id', '=', $id)->update([
                'open' => $request['open']
            ]);
        }
        if ($request['identy_number'] == null) {
            $request['identy_number'] = '*';
        } else {
            DB::table('orders')->where('id', '=', $id)->update([
                'identy_number' => $request['identy_number']
            ]);
        }
        if ($request['order_locate'] == 'select the order locate') {
            DB::table('orders')->where('id', '=', $id)->update([
                'order_locate' => '0',
                'cause_id' => '1',
                'status_id' => '10',
                'delay_id'=>'1'
            ]);
        } else {
            DB::table('orders')->where('id', '=', $id)->update([
                'order_locate' => $request['order_locate'],
                'cause_id' => '1',
                'status_id' => '10',
                'delay_id'=>'1'
            ]);
        }
        DB::table('orders_history')->insert([
            'order_id' => $id,
            'action' => 'add',
            'new' => json_encode(DB::table('orders')->latest('created_at')->get()[0], JSON_UNESCAPED_UNICODE),
            'user_id' => auth()->user()->id
        ]);
        return redirect()->back()->with('success', 'تمت اضافه الطرد بنجاح');
    }
    public function history()
    {
        session()->flash('active', 'companyhistory');
        $orders = DB::table('orders')->where('on_archieve', '=', '0')->where('id_company', '=', Auth::user()->company_id)->select('*')->get();
        $data = array();
        $ids = array();
        foreach ($orders as $order) {
            $record = DB::table('orders_history')->select('*')->where('order_id', '=', $order->id)
                ->where('action', '=', 'update by delegate')->orWhere('action', '=', 'update')
                ->join('users', 'users.id', '=', 'orders_history.user_id')->selectRaw('users.name')
                ->selectRaw('orders_history.id AS sid')
                ->get();
            array_push($data, $record);
        }
        // dd($data);
        $oldvalues = array();
        $newvalues = array();
        if ($data == '[]') {
            foreach ($data[0] as $order => $value) {
                $old = json_decode($value->old);
                $new = json_decode($value->new);
                $oldvalues[$value->sid] = array();
                $newvalues[$value->sid] = array();
                foreach ($old as $old1 => $value1) {
                    // dd($old1,$value1);
                    if ($old->$old1 != $new->$old1) {
                        $oldvalues[$value->sid][$old1] = array();
                        // $oldvalues[$value->sid][$old1][$old->$old1] = array();
                        $newvalues[$value->sid][$old1] = array();
                        // $newvalues[$value->sid][$old1][$new->$old1] = array();
                        array_push($oldvalues[$value->sid][$old1], $old->$old1);
                        array_push($newvalues[$value->sid][$old1], $new->$old1);
                    }
                }
            }
        }

        return view('history.orders')->with('data', $data[0])->with('oldvalues', $oldvalues)->with('newvalues', $newvalues);
    }
    public function archieve()
    {
        $orders = DB::table('orders')->where('on_archieve', '=', '0')->where('id_company', '=', Auth::user()->company_id)->select('*')->get();
        $data = array();
        $ids = array();
        foreach ($orders as $order) {
            $record = DB::table('orders_history')->select('*')->where('id', '=', $order->id)->where('action', '=', 'update by delegate')->orWhere('action', '=', 'update')
                ->join('users', 'users.id', '=', 'orders_history.user_id')->selectRaw('users.name')
                ->selectRaw('orders_history.id AS sid')->get();
            array_push($data, $record);
        }
        // session()->flash('active', 'history_arcieve');
        // $orders = DB::table('orders')->select('*')->where('on_archieve', '=', '1')->get();
        $police_duplicate = array();
        $polices = array();
        foreach ($data as $order) {
            if (in_array($order['id_police'], $polices)) {
                array_push($police_duplicate, $order['id_police']);
            } else {
                array_push($polices, $order['id_police']);
            }
        }
        $centers = DB::table('centers')->select('*')->get();
        $companies = DB::table('companies')->select('*')->get();
        $agents = DB::table('users')->select('id', 'name')->where('rank_id', '=', '8')->get();
        $delegates = DB::table('users')->select('id', 'name', 'commision')->where('rank_id', '=', '9')->get();
        $states = DB::table('order_state')->select('*')->get();
        $causes = DB::table('causes_return')->select('*')->get();
        session()->flash('orders', $data);
        return view('history.archieve')
            ->with('centers', $centers)
            ->with('companies', $companies)
            ->with('agents', $agents)
            ->with('delegates', $delegates)
            ->with('states', $states)
            ->with('causes', $causes)
            ->with('police_duplicate', $police_duplicate);
    }
}
