<?php
namespace App\Model\Internal;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use App\Engine\TraitModelEventLogger;

class BaseModel extends EloquentModel {
    use TraitModelEventLogger;
}
