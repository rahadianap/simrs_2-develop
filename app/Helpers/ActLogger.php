<?php
namespace App\Helpers;
 
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use App\Models\ActivityLog;
 
class ActLogger {    
    public static function ActivityLog($activity, $trxId, $actType, $clientId) {
        
        $userName = Auth::user()->name;
        $userId = Auth::user()->id;
        $email = Auth::user()->email;
        
        $log = new ActivityLog();
        $log->client_id = $siteId;
        $log->log_id = date('YmdHis').Uuid::uuid4()->getHex();
        $log->user_id = $userId;
        $log->user_name = $userName;
        $log->email = $email;
        $log->activity = $activity;
        $log->act_type = $actType;
        $log->trx_id = $trxId;
        $log->trx_type = $actType;
        $log->save();
    }
}
