<?php
namespace App\Engine;

trait TraitModelEventLogger {

    protected static function booted()
    {
        foreach (['created','updated','deleted'] as $eventName) {
            static::$eventName(function ($model) use ($eventName) {

                try {
                    $log = new \App\Model\Internal\LogAction();
                    $log->user_account_id = (isset($_SESSION['user_account']) ? $_SESSION['user_account']['id'] : null);
                    $log->owner_type = get_class($model);
                    $log->owner_id = $model->id;
                    $log->action_type = $eventName;
                    $log->old_value = json_encode($model->getDirty());
                    $log->new_value = json_encode($model->getOriginal());
                    $log->created_at = date("Y-m-d H:i:s");
                    $log->save();

                    return true;
                } catch (\Throwable $th) {
                    return false;
                }

            });
        }
    }
} 