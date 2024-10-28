<?php
namespace App\Engine;

use Illuminate\Database\Eloquent\Model;

trait TraitModelEventLogger {

    protected static function bootModelEventLogger()
    {
        $events = ['created','updated','deleted'];
        foreach ($events as $eventName) {
            static::$eventName(function (Model $model) use ($eventName) {
                try {

                    $log = new App\Model\LogAction();
                    $log->user_account_id = (isset($_SESSION['user_account']) ? $_SESSION['user_account']['id'] : null);
                    $log->owner_type = get_class($model);
                    $log->owner_id = $model->id;
                    $log->action_type = static::getActionName($eventName);
                    $log->old_value = json_encode($model->getDirty());
                    $log->new_value = json_encode($model->getOriginal());
                    $log->created_at = date("Y-m-d H:i:s");
                    $log->save();

                    return true;
                    
                } catch (\Exception $e) {
                    return false;
                }
            });
        }

    }

    protected static function getActionName($event)
    {
        switch (strtolower($event)) {
            case 'created':
                return 'create';
                break;
            case 'updated':
                return 'update';
                break;
            case 'deleted':
                return 'delete';
                break;
            default:
                return 'unknown';
        }
    }
} 