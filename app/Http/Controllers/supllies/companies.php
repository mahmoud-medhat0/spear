<?php

namespace App\Http\Controllers\supllies;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\companies_supplies;

class companies extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth');
    }
    public function companies_supplies()
    {
        session()->flash('active', 'companies_supplies');
        $companies = DB::table('companies')->select('id', 'name')->get();
        $dues = array();
        $payed = array();
        foreach ($companies as $company) {
            $due = DB::table('account_stament_companies')->select('late')->where('company_id', '=', $company->id)->get();
            $dues[$company->id] = 0;
            $pay = DB::table('account_stament_companies')->select('payed')->where('company_id', '=', $company->id)->get();
            $payed[$company->id] = 0;
            $orderscost[$company->id] = 0;
            foreach ($due as $key) {
                $dues[$company->id] += $key->late;
            }
            foreach ($pay as $key) {
                $payed[$company->id] += $key->payed;
            }
        }
        return view('supplies.companies.add_d')->with('companies', $companies)->with('dues', $dues)->with('payed', $payed);
    }
    public function companies_new($id)
    {
        $companyname = DB::table('companies')->select('id', 'name')->where('id', '=', $id)->get()[0]->name;
        $orders = DB::table('orders')
            ->where('id_company', '=', $id)
            ->where('company_supply', '=', '0')
            ->where('status_id', '=', '1')
            ->orWhere('status_id', '=', '2')
            ->where('company_supply', '=', '0')->where('order_locate','2')
            ->orWhere('status_id', '=', '8')
            ->where('company_supply', '=', '0')->where('order_locate','2')
            ->orWhere('status_id', '=', '7')
            ->where('company_supply', '=', '0')->where('order_locate','2')
            ->orWhere('status_id', '=', '4')
            ->where('company_supply', '=', '0')->where('order_locate','2')
            ->orWhere('status_id', '=', '5')
            ->where('company_supply', '=', '0')->where('order_locate','2')
            ->select('id_police', 'name_client', 'cost', 'delegate_supply', 'phone', 'phone2')
            ->join('companies', 'orders.id_company', '=', 'companies.id')->selectRaw('companies.name AS company_name')
            ->selectRaw('companies.commission')->selectRaw('orders.id')
            ->join('order_state', 'order_state.id', '=', 'orders.status_id')->selectRaw('order_state.state')
            ->get();
        session()->flash('orders', $orders);
        return view('supplies.companies.all')->with('companyname', $companyname)->with('id', $id);
    }
    public function companies_supply(Request $request)
    {
        $r = $request->except('_token', 'example1_length', 'balance', 'payed', 'name', 'id', 'total','commission','checkbox','total1');
        foreach ($r as $r1 => $value) {
            echo $value;
            $order = DB::table('orders')->where('id', '=', $value);
            if ($order->get()[0]->company_supply=='1') {
                return redirect()->back()->with('error','لقد تم التوريد من قبل');
            }
        }
        $validate = [
            'payed' => ['required']
        ];
        $request->validate($validate);
        DB::table('account_stament_companies')->insert([
            'company_id' => $request['id'],
            'payed' => $request['payed'],
            'total' => $request['total'],
            'late' => $request['total'] - $request['payed']
        ]);
        $id = DB::table('account_stament_companies')->select('id')->where('company_id', '=', $request['id'])
            ->latest('created_at')->get()[0]->id;
        $filename = "تقفيل_" . $request['name'] . "_" . $id;
        $filepath = 'companies/'.$request['name'] . '/' . str($id) . str($filename) . ".xlsx";
        Excel::store(new companies_supplies, $filepath,'public');
        DB::table('account_stament_companies')->select('*')->where('id', '=', $id)->update([
            'excel' => $filepath
        ]);
        date_default_timezone_set('Africa/Cairo');
        Excel::download(new companies_supplies, 'تقفيل_' . $request['name'] . '_' .'.xlsx');
        $r = $request->except('_token', 'example1_length', 'payed', 'name', 'id', 'total', 'total1');
        $profit = 0;
        $payedold = DB::table('account_stament_companies')->where('id', '=', $id)->select('payed')->get()[0]->payed;
        foreach ($r as $r1 => $value) {
            $p1 = DB::table('orders')->where('orders.id', '=', $value)->select('orders.delegate_id')
                ->join('users', 'orders.delegate_id', '=', 'users.id')->selectRaw('users.commision AS usercommission')
                ->join('companies', 'companies.id', '=', 'orders.id_company')->selectRaw('companies.commission AS companycommision')->get()[0];
            $profit = + ($p1->companycommision - $p1->usercommission);
            DB::table('orders')->where('id', '=', $value)->update([
                'company_supply' => 1,
                'company_supply_date' => now()
            ]);
        }
        $payed = 0 + $payedold;
        $latest0 = 0 + DB::table('keep_money')->latest('created_at')->get()[0]->moneyafter;
        DB::table('keep_money')->insert([
            'moneyold' => $latest0,
            'moneyafter' => $latest0 - $payed,
            'user_id' => Auth::user()->id
        ]);
        $payedold = $request['payed'];
        $payed = 0 + $payedold;
        $latest = 0 + DB::table('profits')->latest('created_at')->get()[0]->moneyafter;
        DB::table('profits')->insert([
            'moneyold' => $latest,
            'moneyafter' => $latest0 - $payed
        ]);
        $companies = DB::table('companies')->select('id', 'name')->get();
        $dues = array();
        $payed = array();
        foreach ($companies as $company) {
            $due = DB::table('account_stament_companies')->select('late')->where('company_id', '=', $company->id)->get();
            $dues[$company->id] = 0;
            $pay = DB::table('account_stament_companies')->select('payed')->where('company_id', '=', $company->id)->get();
            $payed[$company->id] = 0;
            $orderscost[$company->id] = 0;
            foreach ($due as $key) {
                $dues[$company->id] += $key->late;
            }
            foreach ($pay as $key) {
                $payed[$company->id] += $key->payed;
            }
        }
        return view('supplies.companies.add_d')->with('companies', $companies)->with('success', 'saved successfully')->with('dues', $dues)->with('payed', $payed);
    }

    public function h_csupplies($id)
    {
        $histories = DB::table('account_stament_companies AS dd')->select('dd.id', 'dd.company_id', 'dd.payed', 'late', 'dd.total', 'dd.excel', 'dd.created_at')->where('dd.company_id', '=', $id)
            ->join('companies AS company', 'company.id', '=', 'dd.company_id')->selectRaw('company.name')->get();
        $name = DB::table('companies')->select('name')->where('id', '=', $id)->get()[0]->name;
        return view('supplies.companies.history_supllies', compact('histories'))->with('name', $name);
    }
}
