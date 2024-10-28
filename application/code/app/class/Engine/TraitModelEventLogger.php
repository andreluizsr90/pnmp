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

                    App\Model\LogAction::create([
                        'user_account_id' => (isset($_SESSION['user_account']) ? $_SESSION['user_account']['id'] : null),
                        'owner_type' => get_class($model),
                        'owner_id' => $model->id,
                        'action_type' => static::getActionName($eventName),
                        'old_value' => json_encode($model->getDirty()),
                        'new_value' => json_encode($model->getOriginal()),
                        'created_at' => date("Y-m-d H:i:s"),
                    ]);

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