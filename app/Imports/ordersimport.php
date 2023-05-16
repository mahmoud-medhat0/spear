<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\orders;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class ordersimport implements ToCollection, WithBatchInserts
{
    public function collection(Collection $rows)
    {
        $id = request()->input('company');
        (array) $centers = DB::table('centers')->select('id', 'center_name', 'id_agent')->get();
        (array) $order_states = DB::table('order_state')->select('*')->get();
        (array) $causes = DB::table('causes_return')->select('*')->get();
        (array) $subcenters = DB::table('sub_center')->select('*')->get();
        $special_intructions2 = DB::table('companies')->select('special_intructions')->where('id', '=', $id)->get();
        if (isset($special_intructions2[0]->special_intructions)) {
            $special_intructions0 = $special_intructions2[0]->special_intructions;
        } elseif (!isset($special_intructions2[0]->special_intructions)) {
            $special_intructions0 = 'none';
        }
        unset($rows[0]);
        foreach ($rows as $row) {
            if ($row[0] != null) {
                DB::table('orders')->insert([
                    'id_company' => $id,
                    'special_intructions2' => $special_intructions0
                ]);
                $orderid = DB::table('orders')->where('id_company',$id)->first()->id;
                DB::table('orders')->where('id_company',$id)->first()->update([
                    'id_police'=>config('app.name') . '-' . $orderid
                ]);
                $id_police=config('app.name') . '-' . $orderid;
                $idorder = DB::table('orders')->select('id')->where('id_police', '=', $id_police)->get()['0']->id;
                $order = DB::table('orders')->select('*')->where('id', '=', $idorder);
                $row[8] = Carbon::instance(Date::excelToDateTimeObject($row[8]))->format('y-m-d');
                if ($row[1] == null) {
                    $row[1] = 'none';
                    $order->update([
                        'name_client' => $row[1]
                    ]);
                } else {
                    $order->update([
                        'name_client' => $row[1]
                    ]);
                }
                if ($row[2] == null) {
                    $row[2] = 'none';
                    $order->update([
                        'phone' => $row[2]
                    ]);
                } else {
                    $order->update([
                        'phone' => $row[2]
                    ]);
                }
                if ($row[3] == null) {
                    $row[3] = 'none';
                    $order->update([
                        'phone2' => $row[3]
                    ]);
                } else {
                    $order->update([
                        'phone2' => $row[3]
                    ]);
                }
                if ($row[4] != null) {
                    foreach ($centers as $center) {
                        if ($row[4] == $center->center_name) {
                            $agent_id = $center->id_agent;
                            $order->update([
                                'center_id' => $center->id,
                                'delegate_id' => $agent_id,
                            ]);
                        }
                    }
                } else {
                    foreach ($centers as $center) {
                        if (str_contains($row[5], $center->center_name)) {
                            $agent_id = $center->id_agent;
                            $order->update([
                                'center_id' => $center->id,
                                'delegate_id' => $agent_id,
                            ]);
                        }
                    }
                    foreach ($subcenters as $subcenter) {
                        if (str_contains($row[5], $subcenter->name)) {
                            $order->update([
                                'center_id' => $subcenter->id_center,
                            ]);
                        }
                    }
                }
                if ($row[5] == null) {
                    $row[5] = 'none';
                    $order->update([
                        'address' => $row[5]
                    ]);
                } else {
                    $order->update([
                        'address' => $row[5]
                    ]);
                }
                if ($row[6] != null) {
                    $order->update([
                        'cost' => $row[6]
                    ]);
                }
                if ($row[7] != null) {
                    $order->update([
                        'salary_charge' => $row[7]
                    ]);
                } else {
                    $order->update([
                        'salary_charge' => 0
                    ]);
                }
                if ($row[8] == null) {
                    $row[8] = 'none';
                    $order->update([
                        'date' => $row[8]
                    ]);
                } else {
                    $order->update([
                        'date' => $row[8]
                    ]);
                }
                if ($row[9] == null) {
                    $row[9] = 'none';
                    $order->update([
                        'notes' => $row[9]
                    ]);
                } else {
                    $order->update([
                        'notes' => $row[9]
                    ]);
                }
                if ($row[11] == null) {
                    $row[11] = 'none';
                    $order->update([
                        'special_intructions' => $row[11]
                    ]);
                } else {
                    $order->update([
                        'special_intructions' => $row[11]
                    ]);
                }
                if ($row[12] == null) {
                    $row[12] = 'none';
                    $order->update([
                        'name_product' => $row[12]
                    ]);
                } else {
                    $order->update([
                        'name_product' => $row[12]
                    ]);
                }

                if ($row[13] == null) {
                    $row[13] = 'none';
                    $order->update([
                        'sender' => $row[13]
                    ]);
                } else {
                    $order->update([
                        'sender' => $row[13]
                    ]);
                }
                if ($row[14] == null) {
                    $row[14] = 'none';
                    $order->update([
                        'weghit' => $row[14]
                    ]);
                } else {
                    $order->update([
                        'weghit' => $row[14]
                    ]);
                }
                if ($row[15] == null) {
                    $row[15] = 'none';
                    $order->update([
                        'open' => $row[15]
                    ]);
                } else {
                    $order->update([
                        'open' => $row[15]
                    ]);
                }
                if ($row[16] == null) {
                    $row[16] = 'none';
                    $order->update([
                        'identy_number' => $row[16]
                    ]);
                } else {
                    $order->update([
                        'identy_number' => $row[16]
                    ]);
                }
            }
            $order->update([
                'cause_id' => '1',
                'status_id' => '10',
                'order_locate' => '0',
                'delay_id' => '1'
            ]);
            DB::table('orders_history')->insert([
                'order_id' => $idorder,
                'action' => 'add',
                'new' => json_encode(DB::table('orders')->latest('created_at')->get()[0], JSON_UNESCAPED_UNICODE),
                'user_id' => auth()->user()->id
            ]);
        }
    }
    public function batchSize(): int
    {
        return 1000;
    }
}
