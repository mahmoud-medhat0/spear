<?php

namespace App\Http\Controllers;

use PDF;
use Pusher\Pusher;
use App\Models\User;
use App\Rules\validagent;
use App\Rules\validcause;
use App\Rules\validsatus;
use App\Rules\vaildcenter;
use App\Events\OrderUpdate;
use App\Rules\validcompany;
use App\Rules\ValidDelayId;
use App\Models\Notification;
use App\Rules\validdelegate;
use Illuminate\Http\Request;
use App\Imports\ordersimport;
use App\Imports\orderssearch;
use App\Rules\ValidOrderLogic;
use App\Rules\ValidOrderLocate;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rules\File;
use App\Models\orders as ModelsOrders;

class orders extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth');
    }
    public function new(Request $request)
    {
        session()->flash('active', 'neworders');
        $companies = DB::table('companies')->select('*')->get();
        $centers = DB::table('centers')->select('*')->get();
        $agents = DB::table('users')->select('id', 'name')->where('rank_id', '=', '8')->get();
        $delegates = DB::table('users')->select('id', 'name', 'commision')->where('rank_id', '=', '9')->get();
        return view('orders.addmanual')->with('companies', $companies)->with('centers', $centers)->with('agents', $agents)->with('delegates', $delegates);
    }

    public function store_m(Request $request)
    {
        $validate = [
            'id_company' => ['required', new validcompany],
            'name_client' => ['required'],
            'phone' => ['required'],
            'address' => ['required'],
            'cost' => ['required', 'int'],
            'center_id'=>['required',new vaildcenter],
            'order_locate'=>['required',new ValidOrderLocate]
        ];
        $request->validate($validate);
        $special_intructions2 = DB::table('companies')->select('special_intructions')->where('id', '=', $request->id_company)->get()[0]->special_intructions;
        $data = [
            'id_company' => $request->id_company,
            'name_client' => $request->name_client,
            'phone' => $request->phone,
            'special_intructions2' => $special_intructions2,
            'date' => now(),
            'address' => $request['address'],
            'cost' => $request['cost'],

        ];
        $id = DB::table('orders')->insertGetId($data);
        DB::table('orders')
        ->where('id',$id)
        ->update([
            'id_police' => config('app.name') . '-' . $id
        ]);

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
        if ($request['delegate_id'] == 'none' || $request['delegate_id'] == 'مندوب' ) {
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
    public function index()
    {
        session()->flash('active', 'orders_all');
        $orders = DB::table('orders')->select('*')->where('on_archieve', '=', '0')->orderBy('created_at', 'desc')->get();
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
        $delayes = DB::table('causes_delay')->select('*')->get();
        session()->flash('orders', $orders);
        return view('orders.all')
            ->with('centers', $centers)
            ->with('companies', $companies)
            ->with('agents', $agents)
            ->with('delegates', $delegates)
            ->with('states', $states)
            ->with('causes', $causes)
            ->with('police_duplicate', $police_duplicate)
            ->with('delayes',$delayes);
    }

    public function ordersajax()
    {
        session()->flash('active', 'orders_all');
        $orders = DB::table('orders')->select('*')->where('on_archieve', '=', '0')->orderBy('created_at', 'desc')->get();
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
        $delayes = DB::table('causes_delay')->select('*')->get();
        session()->flash('orders', $orders);
        return view('orders.ordersajax')
            ->with('centers', $centers)
            ->with('companies', $companies)
            ->with('agents', $agents)
            ->with('delegates', $delegates)
            ->with('states', $states)
            ->with('causes', $causes)
            ->with('police_duplicate', $police_duplicate)
            ->with('delayes',$delayes);
    }

    public function addorders()
    {
        session()->flash('active', 'addorders');
        $companies = DB::table('companies')->select('id', 'name')->get();
        return view('orders.add', compact('companies'));
    }
    public function storeorders()
    {
        request()->validate([
            'company' => ['required', 'exists:companies,id'],
            'sheet' => ['required', File::types(['xls', 'xlsx'])]
        ]);
        Excel::import(new ordersimport, request()->file('sheet'));
        $companies = DB::table('companies')->select('id', 'name')->get();
        return redirect()->back()->with('companies', $companies)->with('success', 'data imported succesfull to database');
    }
    public function checks(Request $request)
    {
        $method = $request['method'];
        switch ($method) {
            case 'state':
                $request->validate(['value' => ['required', new validsatus]]);
                $checks = $request->except('_token', 'example1_length', 'method', 'value', 'checkbox');
                foreach ($checks as $key => $value) {
                    $validate[$key] = ['required', new ValidOrderLogic];
                    $request->validate($validate);
                    $order = DB::table('orders')->where('id', '=', $value);
                    $orderold = $order->get();
                    $order->update([
                        'status_id' => $request['value']
                    ]);
                    DB::table('orders_history')->insert([
                        'order_id' => $value,
                        'action' => 'edit',
                        'old' => json_encode($orderold[0], JSON_UNESCAPED_UNICODE),
                        'new' => json_encode($order->get()[0], JSON_UNESCAPED_UNICODE),
                        'user_id' => auth()->user()->id
                    ]);
                }
                return redirect()->back()->with('success', 'تم تحديث حالات الطرود');
                break;
            case 'cause':
                $request->validate(['value' => ['required', new validcause]]);
                $checks = $request->except('_token', 'example1_length', 'method', 'value', 'checkbox');
                foreach ($checks as $key => $value) {
                    $validate[$key] = ['required', new ValidOrderLogic];
                    $request->validate($validate);
                    $order = DB::table('orders')->where('id', '=', $value);
                    $orderold = $order->get();
                    $order->update([
                        'cause_id' => $request['value']
                    ]);
                    DB::table('orders_history')->insert([
                        'order_id' => $value,
                        'action' => 'edit',
                        'old' => json_encode($orderold[0], JSON_UNESCAPED_UNICODE),
                        'new' => json_encode($order->get()[0], JSON_UNESCAPED_UNICODE),
                        'user_id' => auth()->user()->id
                    ]);
                }
                return redirect()->back()->with('success', 'تم تحديث سبب ارتجاع الطرود');
                break;
            case 'locate':
                $request->validate(['value' => ['required', 'in:0,1,2,3,4']]);
                $checks = $request->except('_token', 'example1_length', 'method', 'value', 'checkbox');
                foreach ($checks as $key => $value) {
                    $validate[$key] = ['required', new ValidOrderLogic];
                    $request->validate($validate);
                    $order = DB::table('orders')->where('id', '=', $value);
                    $orderold = $order->get();
                    $order->update([
                        'order_locate' => $request['value']
                    ]);
                    DB::table('orders_history')->insert([
                        'order_id' => $value,
                        'action' => 'edit',
                        'old' => json_encode($orderold[0], JSON_UNESCAPED_UNICODE),
                        'new' => json_encode($order->get()[0], JSON_UNESCAPED_UNICODE),
                        'user_id' => auth()->user()->id
                    ]);
                }
                return redirect()->back()->with('success', 'تم تحديث مندوب الطرود بنجاح');
                break;
            case 'destroy':
                $request->validate(['value' => ['required', 'in:1,2']]);
                $checks = $request->except('_token', 'example1_length', 'method', 'value', 'checkbox');
                switch ($request['value']) {
                    case '1':
                        foreach ($checks as $key => $value) {
                            $order = DB::table('orders')->where('id', '=', $value);
                            $orderold = $order->get();
                            DB::table('orders_history')->insert([
                                'order_id' => $value,
                                'action' => 'delete',
                                'old' => json_encode($orderold[0], JSON_UNESCAPED_UNICODE),
                                'user_id' => auth()->user()->id
                            ]);
                            $order->delete();
                        }
                        return redirect()->back()->with('success', 'تم حذف الطرود');
                        break;
                    case '2':
                        if ($checks==null) {
                            return redirect()->back()->with('error','برجاء تحديد الطرود اللتي تود طباعتها');
                        }
                        if (count($checks)>260) {
                            return redirect()->back()->with('error','الحد الاقصى لطباعه الطرود 260 طرد');
                        }
                        foreach ($checks as $key => $value) {
                            $objectB = DB::table('orders')->select('*')->where('id', '=', $value)->get();
                            if ($objectB == null | $objectB == '[]') {
                            } else {
                                $obj_merged[$value] = $objectB[0];
                            }
                        }
                        $data = [
                            'title' => 'orders_'.date('ymd'),
                            'orders' => $obj_merged
                        ];
                        $pdf = PDF::loadView('police', $data);
                        return $pdf->download('orders_print_' . date('y-m-d') . '.pdf');
                        break;
                }
                break;
            case 'agent':
                $request->validate(['value' => ['required', new validagent]]);
                $checks = $request->except('_token', 'example1_length', 'method', 'value', 'checkbox');
                foreach ($checks as $key => $value) {
                    $validate[$key] = ['required', new ValidOrderLogic];
                    $request->validate($validate);
                    $order = DB::table('orders')->where('id', '=', $value);
                    $orderold = $order->get();
                    $order->update([
                        'agent_id' => $request['value']
                    ]);
                    DB::table('orders_history')->insert([
                        'order_id' => $value,
                        'action' => 'edit',
                        'old' => json_encode($orderold[0], JSON_UNESCAPED_UNICODE),
                        'new' => json_encode($order->get()[0], JSON_UNESCAPED_UNICODE),
                        'user_id' => auth()->user()->id
                    ]);
                }
                return redirect()->back()->with('success', 'تم تغير وكلاء الطرود');
                break;
            case 'delegate':
                $request->validate(['value' => ['required', new validdelegate]]);
                $checks = $request->except('_token', 'example1_length', 'method', 'value', 'checkbox');
                foreach ($checks as $key => $value) {
                    $validate[$key] = ['required', new ValidOrderLogic];
                    $request->validate($validate);
                    $order = DB::table('orders')->where('id', '=', $value);
                    $orderold = $order->get();
                    $order->update([
                        'delegate_id' => $request['value']
                    ]);
                    DB::table('orders_history')->insert([
                        'order_id' => $value,
                        'action' => 'edit',
                        'old' => json_encode($orderold[0], JSON_UNESCAPED_UNICODE),
                        'new' => json_encode($order->get()[0], JSON_UNESCAPED_UNICODE),
                        'user_id' => auth()->user()->id
                    ]);
                }
                return redirect()->back()->with('success', 'تم تغيير مناديب الطرود');
                break;
            case 'center':
                $request->validate(['value' => ['required', new vaildcenter]]);
                $checks = $request->except('_token', 'example1_length', 'method', 'value', 'checkbox');
                foreach ($checks as $key => $value) {
                    $validate[$key] = ['required', new ValidOrderLogic];
                    $request->validate($validate);
                    $order = DB::table('orders')->where('id', '=', $value);
                    $orderold = $order->get();
                    $order->update([
                        'center_id' => $request['value']
                    ]);
                    DB::table('orders_history')->insert([
                        'order_id' => $value,
                        'action' => 'edit',
                        'old' => json_encode($orderold[0], JSON_UNESCAPED_UNICODE),
                        'new' => json_encode($order->get()[0], JSON_UNESCAPED_UNICODE),
                        'user_id' => auth()->user()->id
                    ]);
                }
                return redirect()->back()->with('success', 'تم تغيير مراكز الطرود');
                break;
        }
    }
    public function edit($id)
    {
        $order = DB::table('orders')->select('*')->where('id', '=', $id)->get()[0];
        $companies = DB::table('companies')->select('*')->get();
        $centers = DB::table('centers')->select('*')->get();
        $delegates = DB::table('users')->select('id', 'name')->where('rank_id', '=', '9')->get();
        $agents = DB::table('users')->select('id', 'name')->where('rank_id', '=', '8')->get();
        $states = DB::table('order_state')->select('*')->get();
        $causes = DB::table('causes_return')->select('*')->get();
        $causes_delay = DB::table('causes_delay')->select('*')->get();
        return view('orders.edit')
            ->with('order', $order)
            ->with('companies', $companies)
            ->with('centers', $centers)
            ->with('agents', $agents)
            ->with('delegates', $delegates)
            ->with('states', $states)
            ->with('causes', $causes)
            ->with('causes_delay',$causes_delay);
    }
    public function update(Request $request)
    {
        $validate['id'] = ['required', new ValidOrderLogic];
        $validate['delay_id'] = ['required',new ValidDelayId];
        $validate['order_locate']=['required',new ValidOrderLocate];
        $request->validate($validate);
        $id = request()->input('id');
        $order = DB::table('orders')->select('*')->where('id', '=', $id)->get()[0];
        $orderupdate = DB::table('orders')->select('*')->where('id', '=', $id);
        $orderold = $orderupdate->get();
        if ($request['phone'] != null) {
            if ($request['phone'] != "none") {
                $orderupdate->update([
                    'phone' => $request['phone']
                ]);
            }
        } else {
            $orderupdate->update([
                'phone' => 'none'
            ]);
        }
        if ($request['phone2'] != null) {
            if ($request['phone2'] != "none") {
                $orderupdate->update([
                    'phone2' => $request['phone2']
                ]);
            }
        } else {
            $orderupdate->update([
                'phone2' => 'none'
            ]);
        }
        if ($request['center_id'] != "none") {
            $validate['center_id'] = [new vaildcenter];
            request()->validate($validate);
            $orderupdate->update([
                'center_id' => $request['center_id']
            ]);
        } else {
            $orderupdate->update([
                'center_id' => null
            ]);
        }
        // if ($request['agent_id'] != "none"|| $request['agent_id']!='agent'||$request['agent_id']!='وكيل') {
        //     $validate['agent_id'] = [new validagent];
        //     request()->validate($validate);
        //     $orderupdate->update([
        //         'agent_id' => $request['agent_id']
        //     ]);
        // } else {
        //     $orderupdate->update([
        //         'agent_id' => null
        //     ]);
        // }
        if ($request['delegate_id'] != "none") {
            $validate['delegate_id'] = [new validdelegate];
            request()->validate($validate);
            $orderupdate->update([
                'delegate_id' => $request['delegate_id']
            ]);
        } else {
            $orderupdate->update([
                'delegate_id' => null
            ]);
        }
        if ($request['address'] == null) {
            $orderupdate->update(['address' => "none"]);
        } else {
            $orderupdate->update(['address' => $request['address']]);
        }
        if ($request['cost'] == null) {
            $orderupdate->update([
                'cost' => 'none'
            ]);
        } else {
            $orderupdate->update(['cost' => $request['cost']]);
        }
        if ($request['cost'] == null) {
            $orderupdate->update([
                'cost' => 'none'
            ]);
        } else {
            $orderupdate->update(['cost' => $request['cost']]);
        }
        if ($request['salary_charge'] == null) {
            $orderupdate->update([
                'salary_charge' => 'none'
            ]);
        } else {
            $orderupdate->update(['salary_charge' => $request['salary_charge']]);
        }
        if ($request['notes'] == null) {
            $orderupdate->update([
                'notes' => 'none'
            ]);
        } else {
            $orderupdate->update(['notes' => $request['notes']]);
        }
        if ($request['special_intructions'] == null) {
            $orderupdate->update([
                'special_intructions' => 'none'
            ]);
        } else {
            $orderupdate->update(['special_intructions' => $request['special_intructions']]);
        }
        if ($request['name_product'] == null) {
            $orderupdate->update([
                'name_product' => 'none'
            ]);
        } else {
            $orderupdate->update(['name_product' => $request['name_product']]);
        }
        if ($request['sender'] == null) {
            $orderupdate->update([
                'sender' => 'none'
            ]);
        } else {
            $orderupdate->update(['sender' => $request['sender']]);
        }
        if ($request['weghit'] == null) {
            $orderupdate->update([
                'weghit' => 'none'
            ]);
        } else {
            $orderupdate->update(['weghit' => $request['weghit']]);
        }
        if ($request['open'] == null) {
            $orderupdate->update([
                'open' => 'none'
            ]);
        } else {
            $orderupdate->update(['open' => $request['open']]);
        }
        if ($request['status_id'] != "none") {
            $validate['status_id'] = ['required', new validsatus];
            request()->validate($validate);
            $orderupdate->update(['status_id' => $request['status_id']]);
        } else {
            $orderupdate->update(['status_id' => null]);
        }
        if ($request['cause_id'] != "none") {
            $validate['cause_id'] = ['required', new validcause];
            request()->validate($validate);
            $orderupdate->update(['cause_id' => $request['cause_id']]);
        } else {
            $orderupdate->update(['cause_id' => null]);
        }
        if ($request['order_locate'] != "none") {
            $validate['order_locate'] = ['in:0,1,2,3,4'];
            request()->validate($validate);
            $orderupdate->update([
                'order_locate' => $request['order_locate']
            ]);
        } else {
            $orderupdate->update(['order_locate' => null]);
        }
        if ($request['identy_number'] == null) {
            $orderupdate->update([
                'identy_number' => 'none'
            ]);
        } else {
            $orderupdate->update(['identy_number' => $request['identy_number']]);
        }
        if ($request['company_supply'] == null || $request['company_supply'] == '0') {
            $orderupdate->update([
                'company_supply' => 0,
            ]);
        } else {
            $validate['company_supply'] = ['in:0,1'];
            $request->validate($validate);
            $orderupdate->update([
                'company_supply' => $request['company_supply'],
                'company_supply_date' => now()
            ]);
        }
        if ($request['delegate_supply'] == null || $request['delegate_supply'] == '0') {
            $orderupdate->update([
                'delegate_supply' => 0,
            ]);
        } else {
            $validate['delegate_supply'] = ['in:0,1'];
            $request->validate($validate);
            $orderupdate->update([
                'delegate_supply' => $request['delegate_supply'],
                'delegate_supply_date' => now()
            ]);
        }
        DB::table('orders_history')->insert([
            'order_id' => $id,
            'action' => 'edit',
            'old' => json_encode($orderold[0], JSON_UNESCAPED_UNICODE),
            'new' => json_encode($orderupdate->get()[0], JSON_UNESCAPED_UNICODE),
            'user_id' => auth()->user()->id
        ]);
        return redirect()->route('orders_all')->with('order', $order)->with('success', 'order updated successful');
    }
    public function stamp()
    {
        return response()->download(public_path('excel/test.xlsx'), 'orders_stamp.xlsx');
    }
    public function stamp_sub()
    {
        return response()->download(public_path('excel/stamp_sub.xlsx'), 'sub_stamp.xlsx');
    }
    public function delete($id)
    {
        $order = DB::table('orders')->select('*')->where('id', '=', $id);
        $orderold = $order->get();
        DB::table('orders_history')->insert([
            'order_id' => $id,
            'action' => 'edit',
            'old' => json_encode($orderold[0], JSON_UNESCAPED_UNICODE),
            'user_id' => auth()->user()->id
        ]);
        $order->delete();
        $orders = DB::table('orders')->select('*')->get();
        $centers = DB::table('centers')->select('*')->get();
        $companies = DB::table('companies')->select('*')->get();
        return redirect()->back()->with('success', 'order deleted successful')->with('orders', $orders)->with('centers', $centers)->with('companies', $companies);
    }
    public function orderssearch()
    {
        request()->validate([
            'sheet' => ['required', File::types(['xls', 'xlsx'])]
        ]);
        Excel::import(new orderssearch, request()->file('sheet'));
        $centers = DB::table('centers')->select('*')->get();
        $companies = DB::table('companies')->select('*')->get();
        $agents = DB::table('users')->select('id', 'name')->where('rank_id', '=', '8')->get();
        $delegates = DB::table('users')->select('id', 'name', 'commision')->where('rank_id', '=', '9')->get();
        $states = DB::table('order_state')->select('*')->get();
        $causes = DB::table('causes_return')->select('*')->get();
        $police_duplicate = array();
        $polices = array();
        if (session()->get('orders') != null) {
            foreach (session()->get('orders') as $order) {
                if (in_array($order->id_police, $polices)) {
                    array_push($police_duplicate, $order->id_police);
                } else {
                    array_push($polices, $order->id_police);
                }
            }
        }
        // session()->flash('orders',session()->get('orders'));
        return view('orders.all')
            ->with('centers', $centers)
            ->with('companies', $companies)
            ->with('agents', $agents)
            ->with('delegates', $delegates)
            ->with('states', $states)
            ->with('causes', $causes)
            ->with('police_duplicate', $police_duplicate);
        // ->with('companies',$companies)->with('success','data imported succesfull to database');
    }
    public function print($id)
    {
        $orders = DB::table('orders')->where('id', $id)->get();
        $data = [
            'title' => date('y-m-d'),
            'orders' => $orders
        ];
        $pdf = PDF::loadView('police', $data);
        return $pdf->download('order_' . $id . '.pdf');
    }
}
