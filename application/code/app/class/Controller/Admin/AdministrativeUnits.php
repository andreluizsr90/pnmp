<?php
namespace App\Controller\Admin;

use App\Engine\Controller;
use App\Engine\{TraitSearch, TraitCrud, HelperUtil};
use App\Model\AdministrativeUnit as AdministrativeUnitMdl;

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
		"parent_id"
	];

    public $searchFilterFields = [
		"name" => 'like'
	];

    public $rolesCrud = [
        "view" => 'UNIT_VIEW',
        "ins" => 'UNIT_ADD',
        "edt" => 'UNIT_UPD',
        "del" => 'UNIT_DEL',
        "imp" => 'UNIT_IMPORT'
    ];

	private function getNamesTree($id = null, $data = []) {
		if(is_null($id)) {
			return [];
		}

		$record = $this->model::where(['_id' => (int) $id ])->first();

		if(!is_null($record->parent_id)) {
			$data = $this->getNamesTree($record->parent_id, $data);
		}

		$data[] = [
			'id' => $id,
			'name' => $record->name
		];

		return $data;
	}

    
    function insertAdditional() {
		$this->setVar('parent_id', $_GET["parent_id"] ?? null);
    }

	
    
    function indexTree() {
        if(property_exists($this, 'rolesCrud') && !empty($this->rolesCrud['view'])) {
            $this->checkRole($this->rolesCrud['view']);
        }

		$id = $_GET["id"] ?? null;
		if(is_null($id)) {
			$where = ['parent_id' => ['$exists' => false ] ];
		} else {
			$where = ['parent_id' => (int) $id ];
		}

		$this->setVar('item_id', $id);
		$this->setVar('tree_titles', $this->getNamesTree($id));

		$records = $this->model::where($where);
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

    	$this->setResponse($this->path . '/list-tree.html');
    }

	public function allAdditional() {
		$this->setVar('administrative_units_parent', $this->model::all());
		$this->setVar('views_path', $this->path);
	}

	public function saveAdditional($record, $isNew) {
		if(!empty($_POST['parent_id'])) {
			$parent = $this->model::where(['_id' => (int) $_POST['parent_id']])->first();
			if(!is_null($parent)) {
				$parent_code_all = empty($parent->parent_code_all) ? [] : $parent->parent_code_all;
				$parent_code_all[] = $parent->code;
				$record->parent_code_all = $parent_code_all;
			}

			$record->parent()->attach(AdministrativeUnitMdl::first((int) $_POST['parent_id']));
		} else {
			unset($record->parent_id);
			unset($record->parent_code_all);
		}
	}

	public function importForm() {
        $this->checkRole($this->rolesCrud['imp']);

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
        $this->checkRole($this->rolesCrud['imp']);

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

				if($column == 'C') {
					$record->$field = $data;
					continue;
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

					$record->parent()->attach($parent);
					unset($record->parent_code);
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

		$this->setVar('process_result', $result);
    	$this->setResponse($this->path . '/form-import-result.html');
	}

}

