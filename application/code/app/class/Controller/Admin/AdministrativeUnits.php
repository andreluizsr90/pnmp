<?php
namespace App\Controller\Admin;

use App\Engine\Controller;
use App\Engine\{TraitSearch, TraitCrud, HelperUtil};

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AdministrativeUnits extends Controller {
	use TraitSearch, TraitCrud;

	public $model = "App\Model\AdministrativeUnit";
	public $route = "/admin/administrative-units";
	public $path = "admin/administrative-units";
	public $orderSearch = ['name' => 1];

    public $formFields = [
		"code",
		"name",
		"parent_code"
	];

    public $searchFilterFields = [
		"name" => 'like'
	];

    public $rolesCrud = [
        "view" => 'UNIT_VIEW',
        "ins" => 'UNIT_ADD',
        "edt" => 'UNIT_UPD',
        "del" => 'UNIT_DEL'
    ];

	private function getNamesTree($code = null, $data = []) {
		if(is_null($code)) {
			return [];
		}

		$record = $this->model::where(['code' => $code ])->first();

		if(!is_null($record->parent_code)) {
			$data = $this->getNamesTree($record->parent_code, $data);
		}

		$data[] = [
			'code' => $code,
			'name' => $record->name
		];

		return $data;
	}

    
    function insertAdditional() {
		$this->setVar('parent_code', $_GET["parent_code"] ?? null);
    }

	
    
    function indexTree() {
        if(property_exists($this, 'rolesCrud') && !empty($this->rolesCrud['view'])) {
            $this->checkRole($this->rolesCrud['view']);
        }

		$code = $_GET["code"] ?? null;

		$this->setVar('item_code', $code);
		$this->setVar('tree_titles', $this->getNamesTree($code));

		$records = $this->model::where(['parent_code' => $code ]);
		if(property_exists($this, 'orderSearch')) {
			if(is_array($this->orderSearch)) {
				$records->sort($this->orderSearch);
			} else {
				$records->sort([$this->orderSearch => 1]);
			}
		}
		

		$this->setVar('records', $records);

        if(method_exists($this, 'indexAdditional')) {
            $this->indexAdditional();
        }

        if(method_exists($this, 'allAdditional')) {
            $this->allAdditional();
        }

		$this->setVar('route', URL_SITE . $this->route);
    	$this->setResponse($this->path . '/list-tree.html');
    }

	public function allAdditional() {
		$this->setVar('administrative_units_parent', $this->model::all());
		$this->setVar('views_path', $this->path);
	}

	public function saveAdditional($record, $isNew) {
		if(!empty($_POST['parent_code'])) {
			$parent = $this->model::where(['code' => $_POST['parent_code']])->first();
			if(!is_null($parent)) {
				$parent_code_all = empty($parent->parent_code_all) ? [] : $parent->parent_code_all;
				$parent_code_all[] = $parent->code;
				$record->parent_code_all = $parent_code_all;
			}
		}
	}

	public function importForm() {
		$this->setVar('route', URL_SITE . $this->route);
    	$this->setResponse($this->path . '/form-import.html');
	}

	public function importDownload() {

		$columns = ["A", "B", "C"];

		$styleArray = [
			'font' => [
				'bold' => true,
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
			],
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_YELLOW,
				],
			],
		];

		$spreadsheet = new Spreadsheet();
		$activeWorksheet = $spreadsheet->getActiveSheet();
		foreach ($this->formFields as $key => $value) {
			$activeWorksheet->setCellValue($columns[$key].'1', $value);
			$activeWorksheet->getStyle($columns[$key].'1')->applyFromArray($styleArray);
		}

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="administrative-units_modelo.xlsx"');
		ob_end_clean();
		exit((new Xlsx($spreadsheet))->save('php://output'));
	}

	public function import() {

		$fileToUpload = $_FILES["fileToUpload"]["tmp_name"] ?? null;
		if(is_null($fileToUpload)) {
            $this->flashDataPost($this->getVar('lang')['action_file_error']);
		}

		try {
			$inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($fileToUpload, [\PhpOffice\PhpSpreadsheet\IOFactory::READER_XLSX]);
		} catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            $this->flashDataPost($this->getVar('lang')['action_file_error']);
		}

		$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
		$reader->setReadDataOnly(true);  
		$spreadsheet = $reader->load($fileToUpload);
		$worksheet = $spreadsheet->getActiveSheet();
		$highestRow = $worksheet->getHighestRow();

		$this->setVar('lineNumber', $highestRow);

		
		$columns = [
			"A" => "code",
			"B" => "name",
			"C" => "parent_code"
		];

		$result = [];
		for ($row = 2; $row <= $highestRow; $row++) { 
			$record = new $this->model;
			foreach ($columns as $column => $field) {
				$data = $worksheet->getCell($column . $row)->getValue();
				if($column == 'A' && is_null($data)) {
					break;
				}

				foreach ($this->formFields as $value) {
					$info = explode(":", $value);
					if($info[0] == $field && !is_null($data)) {
						if(count($info) > 1) {
							$record->{$info[0]} = ($info[1] == "int" ? intval($data) : $data);
						} else {
							$record->{$info[0]} = $data;
						}
					}
				}
			}

			if(empty($record->code)) {
				$result[] = [
					'line' => $row,
					'success' => false,
					'message' => 'Sem dados'
				];
				continue;
			}

			if(!empty($record->parent_code)) {
				$parent = $this->model::where(['code' => $record->parent_code])->first();
				if(!is_null($parent)) {
					$parent_code_all = empty($parent->parent_code_all) ? [] : $parent->parent_code_all;
					$parent_code_all[] = $parent->code;
					$record->parent_code_all = $parent_code_all;
				}
			}

			try {
				
				$record->save();

				$result[] = [
					'line' => $row,
					'success' => true
				];
			} catch (\Throwable $th) {
				$result[] = [
					'line' => $row,
					'success' => false,
					'message' => $th->getMessage()
				];
			}
		}

		$this->setVar('route', URL_SITE . $this->route);
		$this->setVar('process_result', $result);
    	$this->setResponse($this->path . '/form-import-result.html');
	}

}

