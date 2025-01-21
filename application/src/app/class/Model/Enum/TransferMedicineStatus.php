<?php
namespace App\Model\Enum;

enum TransferMedicineStatus : string {

    case OPEN = 'OPEN';
    case DONE = 'DONE';
    case CANCELED = 'CANCELED';

}