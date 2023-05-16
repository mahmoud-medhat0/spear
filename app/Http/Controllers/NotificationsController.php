<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function notifications()
    {
        $notifications = Notification::where('notifiable_id',auth()->user()->id)->get();
        foreach ($notifications as $notification) {
            $notification->created_at_date = Carbon::parse($notification->created_at)->format('Y-m-d');
            $notification->created_at_time = Carbon::parse($notification->created_at)->format('h:i:s A');
        }
        $unread = Notification::where('notifiable_id',auth()->user()->id)->where('read_at',null)->get()->count();
        return response()->json(['unread'=>$unread,'notifications'=>$notifications]);
    }
    public function read()
    {
        $notifications = Notification::where('notifiable_id',auth()->user()->id)->update([
            'read_at'=>now()
        ]);
        return response()->json(['success'=>'success']);
    }
}
