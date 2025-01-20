<?php
namespace App\Model\Enum;

enum OrderMedicineStatus : string {

    case OPEN = 'OPEN';
    case APPROVED = 'APPROVED';
    case DONE = 'DONE';
    case CANCELED = 'CANCELED';

}