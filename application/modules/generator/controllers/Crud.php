<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Crud extends MX_Controller
{
	public function __construct() {
		parent::__construct();
	}
	
    public function index()
    {
		$data = array(
			'table_name' => $this->input->post('table_name'),
			'module' => $this->input->post('module'),
			'controller' => $this->input->post('controller'),
			'model' => $this->input->post('model'),
			'form_element' => $this->input->post('form_element'),
			'field_table' => array()
		);
		$data['tables'] = $this->db->list_tables();
		if(empty($data['form_element'])){
			if(!empty($data['table_name'])){
				$data['module'] = $this->setModuleName($data['table_name']);
				$data['controller'] = $this->setControllerName($data['table_name']);
				$data['model'] = $this->setModelName($data['table_name']);
				$data['field_table'] = $this->db->field_data($data['table_name']);
			}
		}else{
			/** generate code */
			$hasilGenerate = $this->generate($data);
			echo 'File yang telah digenerate adalah <div>'.implode('</div><div>',$hasilGenerate).'</div>';
			return;
		}
        $this->load->view('crud_generator', $data);
    }

	private function generate($data){
		$result = [];
		$module = APPPATH.'modules'.DIRECTORY_SEPARATOR.$data['module'];
		$controller = $module.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$data['controller'].'.php';
		$sekarang = date('Y-m-d H:i:s');
		$modelStorage = APPPATH.'models';
		$model = $modelStorage.DIRECTORY_SEPARATOR.$data['model'].'.php';
		$this->createModule($module);
		$dataController = ['controller' => $data['controller'], 'model' => $data['model'], 'title' => 'Data '.$data['controller'], 'created_at' => $sekarang];
		$contentController = $this->load->view('generator/template/controller',$dataController,TRUE);
		$result[] = $this->createFile($controller,$contentController);

		$dataModel = ['created_at' => $sekarang,'namaModel' => $data['model']];
		$dataModel = array_merge($dataModel,$this->setDataModelTemplate($data));
		$contentModel = $this->load->view('generator/template/model',$dataModel,TRUE);	
		$result[] = $this->createFile($model,$contentModel);

		return $result;
	}

	private function setDataModelTemplate($data){
		$_formElement = [];
		$_inform = $data['form_element']['inform'];
		$_alias = $data['form_element']['alias'];
		$_required = $data['form_element']['required'];
		$_options = $data['form_element']['options'];
		foreach($_inform as $_f){
			$_label = !empty($_alias[$_f]) ? $_alias[$_f] : $_f;
			$_type = $_options[$_f];
			$_tmp = <<<ARR
			
			'{$_f}' => [
				'id' => '{$_f}',
				'label' => '{$_label}',
				'placeholder' => 'Isikan {$_label}',
				'type' => '{$_type}',
				'value' => '',	
ARR;
			if(isset($_required[$_f])){
			$_tmp .= <<<ARR

				'required' => 'required'	
ARR;
			}	
			$_tmp .= <<<ARR

			]	
ARR;
			array_push($_formElement,$_tmp);
		}
		$_tmp = <<<ARR

		'submit' => [
            'id' => 'submit',
            'type' => 'submit',
            'label' => 'Simpan'
        ]
ARR;
		array_push($_formElement,$_tmp);

		$fields = $this->db->field_data($data['table_name']);
		$primaryKey = $fields[0]->name;
		return [
			'heading' => '\''.implode('\',\'',$data['form_element']['heading']).'\'',
			'formElement' => implode(',',$_formElement),
			'namaTable' => $data['table_name'],
			'primaryKey' => $primaryKey
		];
	}

	private function createModule($module){
		$controllerModule = $module.DIRECTORY_SEPARATOR.'controllers';
		
		$this->createFolder($module);
		$this->createFolder($controllerModule);
	}

	private function createFolder($namaFolder){
		if(!file_exists($namaFolder)){
			mkdir($namaFolder,'0777',true);	
		}
	}

	private function createFile($namaFile,$content){
        $file = fopen($namaFile, "w");
        fputs($file, $content);
		fclose($file);
		return $namaFile;
	}

	private function setControllerName($nama){
		return ucfirst(strtolower($nama));
	}

	private function setModelName($nama){
		return ucfirst(strtolower($nama));
	}

	private function setModuleName($nama){
		return strtolower($nama);
	}
}

/* End of file model_generator.php */
/* Location: ./application/controllers/model_generator.php */