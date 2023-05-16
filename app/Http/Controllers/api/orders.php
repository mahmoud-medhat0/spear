<?php

namespace App\Http\Controllers\api;

use Pusher\Pusher;
use App\Models\User;
use App\Rules\validuid;
use App\Rules\validcause;
use App\Rules\ValidDelayId;
use App\Rules\validorderid;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Rules\ValidOrderState;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class orders extends Controller
{
    public function __construct()
    {
        App::setLocale('ar');
        return $this->middleware('auth:sanctum');
    }
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' =>['required',new validuid()]
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->toArray()], 422,[], JSON_UNESCAPED_UNICODE);
        }
        $userid = DB::table('uid')->select('user_id')->where('uid', '=', request()->uid)->get()[0]->user_id;
        $rank = DB::table('users')->select('id', 'rank_id')->where('id', '=', $userid)->get()[0]->rank_id;
        switch ($rank) {
            case '8':
                $orders = DB::table('orders')->whereNot('order_locate','3')->whereNot('order_locate','4')->where('agent_id', '=', $userid)->where('on_archieve', '0')->select('*')
                    ->selectRaw('orders.id AS orderid')
                    ->join('order_state', 'order_state.id', '=', 'orders.status_id')->selectRaw('order_state.state AS statename')
                    ->join('causes_return', 'causes_return.id', '=', 'orders.cause_id')->selectRaw('causes_return.cause AS causename')
                    ->join('companies', 'companies.id', '=', 'orders.id_company')->selectRaw('companies.name AS company_name')
                    ->get();
                $results =array();
                foreach ($orders as $order) {
                    foreach ($order as $key => $value) {
                        if (str_contains($value, request()->data)) {
                            array_push($results, $order);
                        }
                    }
                }
                return response()->json($results, 200, [], JSON_UNESCAPED_UNICODE);
                break;
            case '9':
                $orders = DB::table('orders')->whereNot('order_locate','3')->whereNot('order_locate','4')->where('delegate_id', '=', $userid)->where('on_archieve', '0')->select('*')
                    ->selectRaw('orders.id AS orderid')
                    ->join('order_state', 'order_state.id', '=', 'orders.status_id')->selectRaw('order_state.state AS statename')
                    ->join('causes_return', 'causes_return.id', '=', 'orders.cause_id')->selectRaw('causes_return.cause AS causename')
                    ->join('companies', 'companies.id', '=', 'orders.id_company')->selectRaw('companies.name AS company_name')
                    ->get();
                $results =array();
                foreach ($orders as $order) {
                    foreach ($order as $key => $value) {
                        if (str_contains($value, request()->data)) {
                            array_push($results, $order);
                        }
                    }
                }
                return response()->json($results, 200, [], JSON_UNESCAPED_UNICODE);
                break;
            default:
                return response()->json(['error'=>'غير مسموح لك بالدخول'], 422,[], JSON_UNESCAPED_UNICODE);
                break;
        }
    }
    public function all(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' =>['required',new validuid()]
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->toArray()], 422,[], JSON_UNESCAPED_UNICODE);
        }
        $userid = DB::table('uid')->select('user_id')->where('uid', '=', $request->uid)->get()[0]->user_id;
        $rank = DB::table('users')->select('id', 'rank_id')->where('id', '=', $userid)->get()[0]->rank_id;
        switch ($rank) {
        case '9':
            $orders = DB::table('orders')->whereNot('order_locate','3')->whereNot('order_locate','4')->where('on_archieve', '0')->where('order_locate', '=', '2')->where("status_id", "=", "10")->where('delegate_id', '=', $userid)->orWhere('status_id', '=', '11')->where('delegate_id', '=', $userid)->where('on_archieve', '0')->select('*')
                ->selectRaw('orders.id AS orderid')
                ->join('order_state', 'order_state.id', '=', 'orders.status_id')->selectRaw('order_state.state AS statename')
                ->join('causes_return', 'causes_return.id', '=', 'orders.cause_id')->selectRaw('causes_return.cause AS causename')
                ->join('companies', 'companies.id', '=', 'orders.id_company')->selectRaw('companies.name AS company_name')
                ->get();
            return response()->json($orders, 200, [], JSON_UNESCAPED_UNICODE);
            break;
        case '8':
            $orders = DB::table('orders')->whereNot('order_locate','3')->whereNot('order_locate','4')->where('on_archieve', '0')->where('order_locate', '=', '2')->where("status_id", "=", "10")->where('agent_id', '=', $userid)->orWhere('status_id', '=', '11')->where('agent_id', '=', $userid)->where('on_archieve', '0')
                ->select('*')->selectRaw('orders.id AS orderid')
                ->join('order_state', 'order_state.id', '=', 'orders.status_id')->selectRaw('order_state.state AS statename')
                ->join('causes_return', 'causes_return.id', '=', 'orders.cause_id')->selectRaw('causes_return.cause AS causename')
                ->join('companies', 'companies.id', '=', 'orders.id_company')->selectRaw('companies.name AS company_name')
                ->get();
            return response()->json($orders, 200, [], JSON_UNESCAPED_UNICODE);
            break;
        default:
            return response()->json(['error'=>'غير مسموح لك بالدخول'], 422,[], JSON_UNESCAPED_UNICODE);
            break;
        }
    }
    public function handle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' =>['required',new validuid()]
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->toArray()], 422,[], JSON_UNESCAPED_UNICODE);
        }
        $userid = DB::table('uid')->select('user_id')->where('uid', '=', $request->uid)->get()[0]->user_id;
        $rank = DB::table('users')->select('id', 'rank_id')->where('id', '=', $userid)->get()[0]->rank_id;
        switch ($rank) {
            case '8':
                $orders = DB::table('orders')->whereNot('order_locate','3')->whereNot('order_locate','4')->where('on_archieve', '0')->where("status_id", "=", "10")->where('agent_id', '=', $userid)->where('order_locate', '=', '0')
                    ->orWhere('order_locate', '=', '1')->where("status_id", "=", "10")->where('agent_id', '=', $userid)->where('on_archieve', '0')->select('*')->selectRaw('orders.id AS orderid')
                    ->join('order_state', 'order_state.id', '=', 'orders.status_id')->selectRaw('order_state.state AS statename')
                    ->join('causes_return', 'causes_return.id', '=', 'orders.cause_id')->selectRaw('causes_return.cause AS causename')
                    ->join('companies', 'companies.id', '=', 'orders.id_company')->selectRaw('companies.name AS company_name')
                    ->get();
                return response()->json($orders, 200, [], JSON_UNESCAPED_UNICODE);
                break;
            case '9':
                $orders = DB::table('orders')->whereNot('order_locate','3')->whereNot('order_locate','4')->where('on_archieve', '0')->where("status_id", "=", "10")->where('delegate_id', '=', $userid)->where('order_locate', '=', '0')->orWhere('order_locate', '=', '1')->where('on_archieve', '0')->where("status_id", "=", "10")->where('delegate_id', '=', $userid)->select('*')->selectRaw('orders.id AS orderid')
                    ->join('order_state', 'order_state.id', '=', 'orders.status_id')->selectRaw('order_state.state AS statename')
                    ->join('causes_return', 'causes_return.id', '=', 'orders.cause_id')->selectRaw('causes_return.cause AS causename')
                    ->join('companies', 'companies.id', '=', 'orders.id_company')->selectRaw('companies.name AS company_name')
                    ->get();
                return response()->json($orders, 200, [], JSON_UNESCAPED_UNICODE);
                break;
            default:
                return response()->json(['error'=>'غير مسموح لك بالدخول'], 422,[], JSON_UNESCAPED_UNICODE);
                break;
            }
    }
    public function delivered(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' =>['required',new validuid()]
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->toArray()], 422,[], JSON_UNESCAPED_UNICODE);
        }
        $userid = DB::table('uid')->select('user_id')->where('uid', '=', $request->uid)->get()[0]->user_id;
        $rank = DB::table('users')->select('id', 'rank_id')->where('id', '=', $userid)->get()[0]->rank_id;
        switch ($rank) {
            case '8':
                $orders = DB::table('orders')->whereNot('order_locate','3')->whereNot('order_locate','4')
                    ->where('on_archieve', '0')->select('*')->where('agent_id', '=', $userid)->where('status_id', '=', '1')
                    ->orWhere('status_id', '=', '2')->where('delegate_id', '=', $userid)->where('on_archieve', '0')
                    ->orWhere('status_id', '=', '3')->where('delegate_id', '=', $userid)->where('on_archieve', '0')
                    ->orWhere('status_id', '=', '4')->where('delegate_id', '=', $userid)->where('on_archieve', '0')
                    ->orWhere('status_id', '=', '5')->where('delegate_id', '=', $userid)->where('on_archieve', '0')
                    ->orWhere('status_id', '=', '9')->where('delegate_id', '=', $userid)->where('on_archieve', '0')
                    ->orWhere('status_id', '=', '7')->where('delegate_id', '=', $userid)->where('on_archieve', '0')
                    ->orWhere('status_id', '=', '8')->where('delegate_id', '=', $userid)->where('on_archieve', '0')
                    ->selectRaw('orders.id AS orderid')
                    ->join('order_state', 'order_state.id', '=', 'orders.status_id')->selectRaw('order_state.state AS statename')
                    ->join('causes_return', 'causes_return.id', '=', 'orders.cause_id')->selectRaw('causes_return.cause AS causename')
                    ->join('companies', 'companies.id', '=', 'orders.id_company')->selectRaw('companies.name AS company_name')->get();
                return response()->json($orders, 200, [], JSON_UNESCAPED_UNICODE);
                break;
            case '9':
                $orders = DB::table('orders')->whereNot('order_locate','3')->whereNot('order_locate','4')->where('on_archieve', '0')->select('*')->where('delegate_id', '=', $userid)->where('status_id', '=', '1')
                    ->orWhere('status_id', '=', '2')->where('delegate_id', '=', $userid)->where('on_archieve', '0')
                    ->orWhere('status_id', '=', '4')->where('delegate_id', '=', $userid)->where('on_archieve', '0')
                    ->orWhere('status_id', '=', '5')->where('delegate_id', '=', $userid)->where('on_archieve', '0')
                    ->orWhere('status_id', '=', '9')->where('delegate_id', '=', $userid)->where('on_archieve', '0')          ->orWhere('status_id', '=', '7')->where('delegate_id', '=', $userid)->where('on_archieve', '0')
                    ->orWhere('status_id', '=', '8')->where('delegate_id', '=', $userid)->where('on_archieve', '0')
                    ->selectRaw('orders.id AS orderid')
                    ->join('order_state', 'order_state.id', '=', 'orders.status_id')->selectRaw('order_state.state AS statename')
                    ->join('causes_return', 'causes_return.id', '=', 'orders.cause_id')->selectRaw('causes_return.cause AS causename')
                    ->join('companies', 'companies.id', '=', 'orders.id_company')->selectRaw('companies.name AS company_name')->get();
                return response()->json($orders, 200, [], JSON_UNESCAPED_UNICODE);
                break;
            default:
                return response()->json(['error'=>'غير مسموح لك بالدخول'], 422,[], JSON_UNESCAPED_UNICODE);
                break;
        }

    }
    public function returned(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' =>['required',new validuid()]
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->toArray()], 422,[], JSON_UNESCAPED_UNICODE);
        }
        $userid = DB::table('uid')->select('user_id')->where('uid', '=', $request->uid)->get()[0]->user_id;
        $rank = DB::table('users')->select('id', 'rank_id')->where('id', '=', $userid)->get()[0]->rank_id;
        switch ($rank) {
            case '8':
                $orders = DB::table('orders')->whereNot('order_locate','3')->whereNot('order_locate','4')
                    ->where('on_archieve', '0')->where('agent_id', '=', $userid)
                    ->orwhereIn('status_id',['2','3','4','5','7','8','9'])
                    ->select('*')->selectRaw('orders.id AS orderid')
                    ->join('order_state', 'order_state.id', '=', 'orders.status_id')->selectRaw('order_state.state AS statename')
                    ->join('causes_return', 'causes_return.id', '=', 'orders.cause_id')->selectRaw('causes_return.cause AS causename')
                    ->join('companies', 'companies.id', '=', 'orders.id_company')->selectRaw('companies.name AS company_name')->get();
                return response()->json($orders, 200, [], JSON_UNESCAPED_UNICODE);
                break;
            case '9':
                $orders = DB::table('orders')->where('on_archieve', '0')->where('delegate_id', '=', $userid)
                    ->whereNot('order_locate','3')->whereNot('order_locate','4')
                    ->where('status_id','2')->orwhereIn('status_id',['3','4','5','7','8','9'])
                    ->select('*')->selectRaw('orders.id AS orderid')
                    ->join('order_state', 'order_state.id', '=', 'orders.status_id')->selectRaw('order_state.state AS statename')
                    ->join('causes_return', 'causes_return.id', '=', 'orders.cause_id')->selectRaw('causes_return.cause AS causename')
                    ->join('companies', 'companies.id', '=', 'orders.id_company')->selectRaw('companies.name AS company_name')->get();
                return response()->json($orders, 200, [], JSON_UNESCAPED_UNICODE);
                break;
            default:
                return response()->json(['error'=>'غير مسموح لك بالدخول'], 422,[], JSON_UNESCAPED_UNICODE);
                break;
        }
    }
    public function delayed(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' =>['required',new validuid()]
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->toArray()], 422,[], JSON_UNESCAPED_UNICODE);
        }
        $userid = DB::table('uid')->select('user_id')->where('uid', '=', $request->uid)->get()[0]->user_id;
        $rank = DB::table('users')->select('id', 'rank_id')->where('id', '=', $userid)->get()[0]->rank_id;
        switch ($rank) {
            case '8':
                $orders = DB::table('orders')->whereNot('order_locate','3')->whereNot('order_locate','4')->where('on_archieve', '0')->select('*')->where('agent_id', '=', $userid)->selectRaw('orders.id AS orderid')->where('status_id', '=', '6')
                    ->join('order_state', 'order_state.id', '=', 'orders.status_id')->selectRaw('order_state.state AS statename')
                    ->join('causes_delay', 'causes_delay.id', '=', 'orders.delay_id')->selectRaw('causes_delay.cause AS delayname')
                    ->join('companies', 'companies.id', '=', 'orders.id_company')->selectRaw('companies.name AS company_name')->get();
                return response()->json($orders, 200, [], JSON_UNESCAPED_UNICODE);
                break;
            case '9':
                $orders = DB::table('orders')->whereNot('order_locate','3')->whereNot('order_locate','4')->where('on_archieve', '0')->select('*')->where('delegate_id', '=', $userid)->selectRaw('orders.id AS orderid')->where('status_id', '=', '6')
                    ->join('order_state', 'order_state.id', '=', 'orders.status_id')->selectRaw('order_state.state AS statename')
                    ->join('causes_delay', 'causes_delay.id', '=', 'orders.delay_id')->selectRaw('causes_delay.cause AS delayname')
                    ->join('companies', 'companies.id', '=', 'orders.id_company')->selectRaw('companies.name AS company_name')->get();
                return response()->json($orders, 200, [], JSON_UNESCAPED_UNICODE);
                break;
            default:
            return response()->json(['error'=>'غير مسموح لك بالدخول'], 422,[], JSON_UNESCAPED_UNICODE);
                break;
        }
    }
    public function causes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' =>['required',new validuid()]
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->toArray()], 422,[], JSON_UNESCAPED_UNICODE);
        }
        $causes = DB::table('causes_return')->select('*')->get();
        return response()->json($causes, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function causesdelayed(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' =>['required',new validuid()]
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->toArray()], 422,[], JSON_UNESCAPED_UNICODE);
        }
        $causes = DB::table('causes_delay')->select('*')->get();
        return response()->json($causes, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function delevered_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' =>['required',new validuid()]
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->toArray()], 422,[], JSON_UNESCAPED_UNICODE);
        }
        $status = DB::table('order_state')->select('*')->where('id', '=', '1')->orWhere('id', '=', '2')->orWhere('id', '4')->orWhere('id', '5')->orWhere('id', '7')->orWhere('id', '8')->get();
        return response()->json($status, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function canceled_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' =>['required',new validuid()]
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->toArray()], 422,[], JSON_UNESCAPED_UNICODE);
        }
        $status = DB::table('order_state')->select('*')->where('id', '=', '9')->get();
        return response()->json($status, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function returned_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' =>['required',new validuid()]
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->toArray()], 422,[], JSON_UNESCAPED_UNICODE);
        }
        $status = DB::table('order_state')->select('*')->where('id', '=', '3')->orWhere('id', '=', '4')->orWhere('id', '=', '5')->orWhere('id', '=', '9')->get();
        return response()->json($status, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function delay_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' =>['required',new validuid()]
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->toArray()], 422,[], JSON_UNESCAPED_UNICODE);
        }
        $status = DB::table('order_state')->select('*')->where('id', '=', '6')->get();
        return response()->json($status, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function coordinate_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' =>['required',new validuid()]
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->toArray()], 422,[], JSON_UNESCAPED_UNICODE);
        }
        $status = DB::table('order_state')->select('*')->where('id', '=', '11')->get();
        return response()->json($status, 200, [], JSON_UNESCAPED_UNICODE);

    }
    //store section
    public function store_delivered(Request $request)
    {
        $rules = [
            'uid'=>['required',new validuid()],
            'order_id'=>['required',new validorderid()],
            'status_id'=>['required',new ValidOrderState()],
            'gps_delivered'=>['required','string']
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->toArray()], 422,[], JSON_UNESCAPED_UNICODE);
        }
        $order = DB::table('orders')->select('*')->where('id', '=', $request->order_id);
        $orderold = $order->get();
        $order->update([
            'status_id' => $request->status_id,
            'gps_delivered' => $request->gps_delivered
        ]);
        if($request->status_id == '2') {
            foreach(User::whereNot('rank_id', '7')->get() as $user) {
                $notification = new Notification();
                $notification->title = 'order '.$request->order_id.' updated';
                $notification->notifiable_id = $user->id;
                $notification->data = 'order '.$request->order_id.' updated by '.auth()->user()->name;
                $notification->notifiable_type = 'user';
                $notification->save();
            }
            $options = [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'encrypted' => true,
            ];
            $pusher = new Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                $options
            );
            $notification1 = new Notification();
            $notification1->title = 'order '.$request->order_id.' updated';
            $notification1->notifiable_id = auth()->user()->id;
            $notification1->data = 'order '.$request->order_id.' updated by '.auth()->user()->name;
            $notification1->notifiable_type = 'user';
            $notification1->save();
            $pusher->trigger('notifications', 'OrderUpdate', $notification1);
        }
        $userid = DB::table('uid')->select('user_id')->where('uid', '=', $request->uid)->get()[0]->user_id;
        DB::table('orders_history')->insert([
            'order_id' => $request->order_id,
            'action' => 'update by delegate',
            'old' => json_encode($orderold[0], JSON_UNESCAPED_UNICODE),
            'new' => json_encode($order->get()[0], JSON_UNESCAPED_UNICODE),
            'user_id' => $userid
        ]);
        return response()->json(['success' => 'تم تحديث حاله الطرد بنجاح'], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function store_returned(Request $request)
    {
        $rules = [
            'uid'=>['required',new validuid()],
            'order_id'=>['required',new validorderid()],
            'status_id'=>['required',new ValidOrderState()],
            'cause_id'=>['required',new validcause]
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->toArray()], 422,[], JSON_UNESCAPED_UNICODE);
        }
        $order = DB::table('orders')->select('*')->where('id', '=', $request->order_id);
        $orderold = $order->get();
        $order->update([
            'status_id' => $request->status_id,
            'cause_id' => $request->cause_id,
        ]);
        if ($request->notes != null) {
            $order->update([
                'notes' => $request->notes
            ]);
        }
        foreach(User::whereNot('rank_id', '7')->get() as $user) {
            $notification = new Notification();
            $notification->title = 'order '.$request->order_id.' updated';
            $notification->notifiable_id = $user->id;
            $notification->data = 'order '.$request->order_id.' updated by '.auth()->user()->name;
            $notification->notifiable_type = 'user';
            $notification->save();
        }
        $options = [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'encrypted' => true,
        ];
        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );
        $notification1 = new Notification();
        $notification1->title = 'order '.$request->order_id.' updated';
        $notification1->notifiable_id = auth()->user()->id;
        $notification1->data = 'order '.$request->order_id.' updated by '.auth()->user()->name;
        $notification1->notifiable_type = 'user';
        $notification1->save();
        $pusher->trigger('notifications', 'OrderUpdate', $notification1);
        $userid = DB::table('uid')->select('user_id')->where('uid', '=', $request->uid)->get()[0]->user_id;
        DB::table('orders_history')->insert([
            'order_id' => $request->order_id,
            'action' => 'update by delegate',
            'old' => json_encode($orderold[0], JSON_UNESCAPED_UNICODE),
            'new' => json_encode($order->get()[0], JSON_UNESCAPED_UNICODE),
            'user_id' => $userid
        ]);
        return response()->json(['success' => 'تم تحديث حاله الطرد بنجاح'], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function store_canceled(Request $request)
    {
        $rules = [
            'uid'=>['required',new validuid()],
            'order_id'=>['required',new validorderid()],
            'status_id'=>['required',new ValidOrderState()],
            'gps_delivered'=>['required','string']
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->toArray()], 422,[], JSON_UNESCAPED_UNICODE);
        }
        $order = DB::table('orders')->select('*')->where('id', '=', $request->order_id);
        $orderold = $order->get();
        $order->update([
            'status_id' => $request->status_id,
        ]);
        if ($request->notes != null) {
            $order->update([
                'notes' => $request->notes
            ]);
        }
        $userid = DB::table('uid')->select('user_id')->where('uid', '=', $request->uid)->get()[0]->user_id;
        DB::table('orders_history')->insert([
            'order_id' => $request->order_id,
            'action' => 'update by delegate',
            'old' => json_encode($orderold[0], JSON_UNESCAPED_UNICODE),
            'new' => json_encode($order->get()[0], JSON_UNESCAPED_UNICODE),
            'user_id' => $userid
        ]);
        return response()->json(['success' => 'تم تحديث حاله الطرد بنجاح'], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function store_coordinated(Request $request)
    {
        $rules = [
            'uid'=>['required',new validuid()],
            'order_id'=>['required',new validorderid()],
            'status_id'=>['required',new ValidOrderState()]
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->toArray()], 422,[], JSON_UNESCAPED_UNICODE);
        }
        $order = DB::table('orders')->select('*')->where('id', '=', $request->order_id);
        $orderold = $order->get();
        $order->update([
            'status_id' => $request->status_id
        ]);
        $userid = DB::table('uid')->select('user_id')->where('uid', '=', $request->uid)->get()[0]->user_id;
        DB::table('orders_history')->insert([
            'order_id' => $request->order_id,
            'action' => 'update by delegate',
            'old' => json_encode($orderold[0], JSON_UNESCAPED_UNICODE),
            'new' => json_encode($order->get()[0], JSON_UNESCAPED_UNICODE),
            'user_id' => $userid
        ]);
        return response()->json(['success' => 'تم تحديث حاله الطرد بنجاح'], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function store_delayed(Request $request)
    {
        $rules = [
            'uid'=>['required',new validuid()],
            'order_id'=>['required',new validorderid()],
            'status_id'=>['required',new ValidOrderState()],
            'delay_id'=>['required',new ValidDelayId()],
        ];
        if($request->delay_id=='8' || $request->delay_id=='9') {
            $rules['date']= ['required','date','after:today'];
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->toArray()], 422,[], JSON_UNESCAPED_UNICODE);
        }
        $order = DB::table('orders')->select('*')->where('id', '=', $request->order_id);
        $orderold = $order->get();
        $order->update([
            'status_id' => $request->status_id,
            'delay_date' => $request->date,
            'delay_id' => $request->delay_id ?? '0000-00-00'
            ]);
        $userid = DB::table('uid')->select('user_id')->where('uid', '=', $request->uid)->get()[0]->user_id;
        DB::table('orders_history')->insert([
            'order_id' => $request->order_id,
            'action' => 'update by delegate',
            'old' => json_encode($orderold[0], JSON_UNESCAPED_UNICODE),
            'new' => json_encode($order->get()[0], JSON_UNESCAPED_UNICODE),
            'user_id' => $userid
        ]);
        return response()->json(['success' => 'تم تحديث حاله الطرد بنجاح'], 200, [], JSON_UNESCAPED_UNICODE);
    }
        public function test(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' =>['required',new validuid()]
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>'false','orders'=>'','error'=>$validator->errors()->toArray()], 422,[], JSON_UNESCAPED_UNICODE);
        }
        $userid = DB::table('uid')->select('user_id')->where('uid', '=', $request->uid)->get()[0]->user_id;
        $rank = DB::table('users')->select('id', 'rank_id')->where('id', '=', $userid)->get()[0]->rank_id;
        switch ($rank) {
        case '9':
            $orders = DB::table('orders')->whereNot('order_locate','3')->whereNot('order_locate','4')->where('on_archieve', '0')->where('order_locate', '=', '2')->where("status_id", "=", "10")->where('delegate_id', '=', $userid)->orWhere('status_id', '=', '11')->where('delegate_id', '=', $userid)->where('on_archieve', '0')->select('*')
                ->selectRaw('orders.id AS orderid')
                ->join('order_state', 'order_state.id', '=', 'orders.status_id')->selectRaw('order_state.state AS statename')
                ->join('causes_return', 'causes_return.id', '=', 'orders.cause_id')->selectRaw('causes_return.cause AS causename')
                ->join('companies', 'companies.id', '=', 'orders.id_company')->selectRaw('companies.name AS company_name')
                ->get();
            return response()->json(['success'=>'true','orders'=>$orders,'error'=>''], 200, [], JSON_UNESCAPED_UNICODE);
            break;
        case '8':
            $orders = DB::table('orders')->whereNot('order_locate','3')->whereNot('order_locate','4')->where('on_archieve', '0')->where('order_locate', '=', '2')->where("status_id", "=", "10")->where('agent_id', '=', $userid)->orWhere('status_id', '=', '11')->where('agent_id', '=', $userid)->where('on_archieve', '0')
                ->select('*')->selectRaw('orders.id AS orderid')
                ->join('order_state', 'order_state.id', '=', 'orders.status_id')->selectRaw('order_state.state AS statename')
                ->join('causes_return', 'causes_return.id', '=', 'orders.cause_id')->selectRaw('causes_return.cause AS causename')
                ->join('companies', 'companies.id', '=', 'orders.id_company')->selectRaw('companies.name AS company_name')
                ->get();
                return response()->json(['success'=>'true','orders'=>$orders,'error'=>''], 200, [], JSON_UNESCAPED_UNICODE);
                break;
        default:
            return response()->json(['sccess'=>'false','orders'=>'','error'=>'غير مسموح لك بالدخول'], 422,[], JSON_UNESCAPED_UNICODE);
            break;
        }
    }
    public function money(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' =>['required',new validuid()]
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>'false','orders'=>'','error'=>$validator->errors()->toArray()], 422,[], JSON_UNESCAPED_UNICODE);
        }
        $userid = DB::table('uid')->select('user_id')->where('uid', '=', $request->uid)->get()[0]->user_id;
        $rank = DB::table('users')->select('id', 'rank_id')->where('id', '=', $userid)->get()[0]->rank_id;
        switch ($rank) {
            case '8':
                $orders = DB::table('orders')->whereNot('order_locate','3')->whereNot('order_locate','4')
                    ->where('on_archieve', '0')->select('*')->where('agent_id', '=', $userid)->where('status_id', '=', '1')
                    ->orWhere('status_id', '=', '2')->where('delegate_id', '=', $userid)->where('on_archieve', '0')->where('delegate_supply','0')
                    ->orWhere('status_id', '=', '3')->where('delegate_id', '=', $userid)->where('on_archieve', '0')->where('delegate_supply','0')
                    ->orWhere('status_id', '=', '4')->where('delegate_id', '=', $userid)->where('on_archieve', '0')->where('delegate_supply','0')
                    ->orWhere('status_id', '=', '5')->where('delegate_id', '=', $userid)->where('on_archieve', '0')->where('delegate_supply','0')
                    ->orWhere('status_id', '=', '9')->where('delegate_id', '=', $userid)->where('on_archieve', '0')->where('delegate_supply','0')
                    ->orWhere('status_id', '=', '7')->where('delegate_id', '=', $userid)->where('on_archieve', '0')->where('delegate_supply','0')
                    ->orWhere('status_id', '=', '8')->where('delegate_id', '=', $userid)->where('on_archieve', '0')->where('delegate_supply','0')
                    ->join('users','users.id','orders.delegate_id');
                    return response()->json(['delivered'=>$orders->sum('orders.cost'),'comission'=>$orders->sum('users.commision'),'total'=>$orders->sum('orders.cost')-($orders->sum('users.commision')* $orders->count()),'orders_count'=>$orders->count()], 200, [], JSON_UNESCAPED_UNICODE);
                    break;
            case '9':
                $orders = DB::table('orders')->whereNot('order_locate','3')->whereNot('order_locate','4')->where('on_archieve', '0')->select('*')->where('delegate_id', '=', $userid)->where('status_id', '=', '1')
                    ->orWhere('status_id', '=', '2')->where('delegate_id', '=', $userid)->where('on_archieve', '0')->where('delegate_supply','0')
                    ->orWhere('status_id', '=', '4')->where('delegate_id', '=', $userid)->where('on_archieve', '0')->where('delegate_supply','0')
                    ->orWhere('status_id', '=', '5')->where('delegate_id', '=', $userid)->where('on_archieve', '0')->where('delegate_supply','0')
                    ->orWhere('status_id', '=', '9')->where('delegate_id', '=', $userid)->where('on_archieve', '0')->where('delegate_supply','0')
                    ->orWhere('status_id', '=', '7')->where('delegate_id', '=', $userid)->where('on_archieve', '0')->where('delegate_supply','0')
                    ->orWhere('status_id', '=', '8')->where('delegate_id', '=', $userid)->where('on_archieve', '0')->where('delegate_supply','0')
                    ->join('users','users.id','orders.delegate_id');
                return response()->json(['delivered'=>$orders->sum('orders.cost'),'comission'=>$orders->sum('users.commision'),'total'=>$orders->sum('orders.cost')-($orders->sum('users.commision')* $orders->count()),'orders_count'=>$orders->count()], 200, [], JSON_UNESCAPED_UNICODE);
                break;
            default:
                return response()->json(['error'=>'غير مسموح لك بالدخول'], 422,[], JSON_UNESCAPED_UNICODE);
                break;
        }
    }
}
