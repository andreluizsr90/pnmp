<?php
namespace App\Controller;

use App\Engine\Controller;
use App\Model\Notice as NoticeMdl;
use App\Model\Institution as InstitutionMdl;
use App\Business\Inventory as InventoryBusiness;
use App\Model\OrderMedicine as OrderMedicineMdl;

class Dashboard extends Controller {

	public function init() {
		$this->setVar('notices', NoticeMdl::all()->sort(['created_at' => -1]));
		

        $medicineStocks = InventoryBusiness::calcMedicines($this->getVar('institution')["_id"], false);
		$quantities = [
			'30' => 0,
			'60' => 0,
			'90' => 0,
			'other' => 0	
		];
		foreach ($medicineStocks as $mK => $m) {
			foreach ($m['batches'] as $b) {

				$time = strtotime($b['valid_date']);
				if($time < strtotime('+30 days')) {
					$quantities['30'] += $m['quantity'];
					continue;
				}
				if($time > strtotime('+30 days') && $time < strtotime('+60 days')) {
					$quantities['60'] += $m['quantity'];
					continue;
				}
				if($time > strtotime('+60 days') && $time < strtotime('+90 days')) {
					$quantities['90'] += $m['quantity'];
					continue;
				}
				
				$quantities['other'] += $m['quantity'];
			}
		}
		$this->setVar('quantities', $quantities);

		$this->setVar('orders_pending', OrderMedicineMdl::where([
            'institution_supplier' => $this->getVar('institution')["_id"],
            'status' => OrderMedicineMdl::$allowedStatus['OPEN']
        ])->count());

		$this->setVar('orders_receiving', OrderMedicineMdl::where([
            'institution_supplier' => $this->getVar('institution')["_id"],
            'status' => OrderMedicineMdl::$allowedStatus['APPROVED']
        ])->count());

		$this->setResponse('dashboard.html');
	}

}

