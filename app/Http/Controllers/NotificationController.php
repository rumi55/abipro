<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Http\Resources\NotificationResource;
use \App\Notification;
use DB;

class NotificationController extends Controller
{
    public function index()
    {
        $page_title = "Notifications";
        $notifications = Notification::where('user_id', user('id'))->orderby('id', 'desc')->where('is_read', 0)->get();
        return view('user.notif', compact('notifications', 'page_title'));
    }
    public function latest()
    {
        $rows = Notification::where('user_id', user('id'))->orderby('id', 'desc')->where('is_read', 0)->limit(10);
        $total = count($rows->get());
        return NotificationResource::collection($rows->get());
        return response()->json(['items' => $rows->get(), 'total' => $total]);
    }

    public function read($id){
        $notif = Notification::findOrFail($id);
        $url = $notif->url;
        $notif->is_read=true;
        $notif->save();
        return redirect($url);
    }
    public function readall(Request $request){
        $id = $request->input('id');
        $notifs = Notification::whereIn('id', $id);
        $notifs->update(['is_read'=>1]);
        return back();
    }
}
