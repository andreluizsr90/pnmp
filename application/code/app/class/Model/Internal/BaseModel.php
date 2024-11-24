<?php
namespace App\Model\Internal;

class BaseModel extends \Mongolid\Model\AbstractModel {
    
    protected function logEvent($action, $newData = null, $oldData = null)
    {
        $actionLog = new \App\Model\Internal\LogAction();
        $actionLog->entity_id = $this->_id;
        $actionLog->entity_type = get_class($this);
        $actionLog->action = $action;
        $actionLog->action_date = date('Y-m-d H:i:s');
        $actionLog->user_id = $_SESSION['user_account']['id'] ?? null;  // Optionally capture the user ID
        $actionLog->new_data = $newData;
        $actionLog->old_data = $oldData;
        $actionLog->save();
    }

    public function save(): bool
    {

        $action = $this->_id ? 'update' : 'create';
        $oldData = null;

        if (!$this->_id) {
            $this->_id = $this->getNextSequence(static::class);
        }

        if($action == 'update') {
            $oldData = $this->first($this->_id)->getDocumentAttributes();
        }

        $result = parent::save();

        if ($result) {
            $this->logEvent($action, $this->getDocumentAttributes(), $oldData);
        }

        return $result;
    }

    public function delete(): bool
    {
        
        $oldData = $this->getDocumentAttributes();
        $result = parent::delete();

        if ($result) {
            $this->logEvent('delete', null, $oldData);
        }

        return $result;
    }

    function getNextSequence(string $entity): int
    {
        $counter = \App\Model\Internal\Sequence::where(['entity' => $entity])->first();

        if ($counter) {
            $counter->sequence++;
        } else {
            $counter = new \App\Model\Internal\Sequence();
            $counter->entity = $entity;
            $counter->sequence = 1;
        }

        $counter->save();

        return $counter->sequence;
    }
    
    public static function getColl() {
        return (new $this)->getCollection();
    }
}
