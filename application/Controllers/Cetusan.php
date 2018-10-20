<?php

/*
 * A tool can use command line to create Controller Model View etc.
 * 
 * @author Piece Chao (趙承瑋)
 * @link https://github.com/piece601/Codeigniter-Pitisan
 * @copyright Copyright (c) 2015, Piece Chao <https://github.com/piece601> 
 * @Editor Rum Haidar Fauzan (Haidar)
 */

class Cetusan extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		if ( ! $this->input->is_cli_request() ) {
			echo "This is command line interface tool.";
			exit();
			return false;
		}
		$this->load->helper('file');
	}

	public function _remap($method, $params = array())
	{
		switch ( $method ) {
			case 'controller':
				$this->controller(
					isset($params[0]) ? $params[0] : Null,
					isset($params[1]) ? $params[1] : 'CI_Controller'
				);
				break;
			case 'model':
				$this->model(
					isset($params[0]) ? $params[0] : Null,
					isset($params[1]) ? $params[1] : Null,
					isset($params[2]) ? $params[2] : Null,
					isset($params[3]) ? $params[3] : 'CI_Model'
				);
				break;
			case 'view':
				$this->view(	isset($params[0]) ? $params[0] : Null, $params);
				break;
			case 'cm':
			case 'mc':
				if ( ! isset($params[0]) ) {
					echo "\n\033[33mUsage:\n\033[0m";
					echo " mc name \n\n";
					echo "\033[33mArguments:\n\033[0m";
					echo " name		The name of the controller class and model class.\n 		(use . to seperate sub directory like last example)\n\n";
					break;
				}
				$this->controller($params[0], 'CI_Controller');
				$this->model($params[0], $params[0], $params[0].'Id', 'CI_Model');
				break;
			default:
				$this->index();
				break;
		}
		return true;
	}

	// controller creator
	protected function _controller_creator($name, $extendsName)
	{
		$name = explode('/', $name)[count(explode('/', $name))-1]; // Find the end of array
		$data = "<?php\n";
		$data .= "defined('BASEPATH') OR exit('No direct script access allowed');\n\n";
		$data .= "class ".$name." extends ".$extendsName." {\n";
		$data .= "\n	public function __construct()\n";
		$data .= "		{\n";
		$data .= "			parent::__construct();\n";
		$data .= "			".'$this'."->load->model('".strtolower($name)."_model');\n";
		$data .= "		}\n";
		$data .= "\n";
		$data .= "	function index(){\n";
        $data .= "		".'$this'."->load->view('v_".strtolower($name)."');\n";
	    $data .= "	}\n";
	 	$data .= "\n";
	    $data .= "	function show(){\n";
	    $data .= "	    ".'$data'."=".'$this'."->".strtolower($name)."_model->select".$name."();\n";
	    $data .= "	    echo json_encode(".'$data'.");\n";
	    $data .= "	}\n";
	 	$data .= "\n";
	    $data .= "	function save(){\n";
	    $data .= "	    ".'$data'."=".'$this'."->".strtolower($name)."_model->insert".$name."();\n";
	    $data .= "	    echo json_encode(".'$data'.");\n";
	    $data .= "	}\n";
	 	$data .= "\n";
	    $data .= "	function edit(){\n";
	    $data .= "	    ".'$data'."=".'$this'."->".strtolower($name)."_model->update".$name."();\n";
	    $data .= "	    echo json_encode(".'$data'.");\n";
	    $data .= "	}\n";
	 	$data .= "\n";
	    $data .= "	function drop(){\n";
	    $data .= "	    ".'$data'."=".'$this'."->".strtolower($name)."_model->delete".$name."();\n";
	    $data .= "	    echo json_encode(".'$data'.");\n";
	    $data .= "	}		\n";
		$data .= "}";
		return $data;
	}

	// model creator
	protected function _model_creator($name, $table, $primaryKey, $extendsName)
	{
		$name = explode('/', $name)[count(explode('/', $name))-1]; // Find the end of array
		$data = "<?php\n";
		$data .= "defined('BASEPATH') OR exit('No direct script access allowed');\n\n";
		$data .= "class ".$name."_model extends ".$extendsName." {\n";
		$data .= "\n";
		$data .= "var ".'$_table'." = 'nama_tabel' //ubah nama_tabel dengan nama tabel, variabel bisa diubah menjadi public, private atau protected sesuai kebutuhan.\n";
		if ( isset($table) ) {
			$data .= "	protected ".'$table'." = ".$table.";\n";
		}
		if ( isset($primaryKey) ) {
			$data .= "	protected ".'$primaryKey'." = ".$primaryKey.";\n";
		}
		$data .= "\n	public function __construct()\n";
		$data .= "	{\n";
		$data .= "		parent::__construct();\n";
		$data .= "	}\n";
		$data .= "\n";
		$data .= "	function select".$name."(){ //menampilkan data dari tabel\n";
        $data .= "		".'$hasil'." = ".'$this'."->db->get(".'$this'."->_table);\n";
        $data .= "		return ".'$hasil'."->result();\n";
	    $data .= "	}\n";
	 	$data .= "\n";
	    $data .= "	function insert".$name."(){ //sesuaikan nama inputan dengan nama array, sebagai contoh:\n";
	    $data .= "    ".'$data'." = array(\n";
	    $data .= "            'kode'  => ".'$this'."->input->post('kode'), \n";
	    $data .= "            'nama'  => ".'$this'."->input->post('nama'), \n";
	    $data .= "            'telepon' => ".'$this'."->input->post('telepon'), \n";
	    $data .= "        );\n";
	    $data .= "    ".'$result'." = ".'$this'."->db->insert(".'$this'."->_table, ".'$data'.");\n";
	    $data .= "    return ".'$result'.";\n";
	    $data .= "	}\n";
	 	$data .= "\n";
	    $data .= "	function update".$name."(){ //kustom variabel inputan yang akan diedit seperti berikut.\n";
	    $data .= "    ".'$kode'." = ".'$this'."->input->post('kode');\n";
	    $data .= "    ".'$nama'." = ".'$this'."->input->post('nama');\n";
	    $data .= "    ".'$telepon'." = ".'$this'."->input->post('telepon');\n";
	 	$data .= "\n";
	    $data .= "    ".'$this'."->db->set('kode', ".'$kode'.");\n";
	    $data .= "    ".'$this'."->db->set('nama', ".'$nama'.");\n";
	    $data .= "    ".'$this'."->db->where('telepon', ".'$telepon'.");\n";
	    $data .= "    ".'$result'." = ".'$this'."->db->update(".'$this'."->_table);\n";
	    $data .= "    return ".'$result'.";\n";
	    $data .= "	}\n";
	 	$data .= "\n";
	    $data .= "	function delete".$name."(){ //hapus berdasarkan id tabel.\n";
	    $data .= "    ".'$kode'."=".'$this'."->input->post('kode');\n";
	    $data .= "    ".'$this'."->db->where('kode', ".'$kode'.");\n";
	    $data .= "    ".'$result'."=".'$this'."->db->delete(".'$this'."->_table);\n";
	    $data .= "    return ".'$result'.";\n";
	    $data .= "	}\n";

		$data .= "}";
		return $data;
	}

	// view creator
	protected function _view_creator($params)
	{
		$data = '';
		foreach ($params as $key => $value) {
			$data .= '<?php $this->load->view(\''.str_replace('.','/', $value).'\') ?>'."\n";
		}
		return $data;
	}

	// recursive create folder and return file path
	protected function _folder_creator($fileName, $mvc)
	{
		$folder = APPPATH.$mvc.'/';
		$arrDir = explode('.', $fileName); 
		unset($arrDir[count($arrDir)-1]);
		foreach ( $arrDir as $key => $value) {
			$folder .= $value.'/';
			if ( ! file_exists( $folder ) ) {
				mkdir( $folder );
			}
		}
		$arrDir = explode('.', $fileName);
		switch ( $mvc ) {
			case 'views':
				$arrDir[count($arrDir)-1] = strtolower( $arrDir[count($arrDir)-1] );
				break;
			default:
				$arrDir[count($arrDir)-1] = ucfirst( $arrDir[count($arrDir)-1] );
				break;
		}
		return implode('/', $arrDir);
	}

	public function index()
	{
		echo "\n\033[33mUsage:\n\033[0m";
		echo " controller	Create controller\n";
		echo " model		Create model\n";
		echo " view		Create view\n";
		echo " mc		Create controller and model\n\n";
		return true;
	}

	public function controller($name = Null, $extendsName = 'CI_Controller')
	{
		// No param, Will response help.
		if ( ! isset( $name ) ) {
			echo "\n\033[33mUsage:\n\033[0m";
			echo " controller name [extendsName]\n\n";
			echo "\033[33mArguments:\n\033[0m";
			echo " name		The name of the controller class (use . to seperate sub directory like last example)\n";
			echo " extendsName	This class extends which class\n\n";
			echo "\033[33mExample:\n\033[0m";
			echo " cetusan controller Test\n";
			echo " # Create a Test.php file in controllers folder.\n\n";
			echo " cetusan controller Test CI_Controller\n";
			echo " # Create a Test.php file and extends CI_Controller in controllers folder.\n\n";
			echo " cetusan controller hi.123.Test CI_Controller\n";
			echo " # Create a Test.php file and extends CI_Controller in controllers/hi/123 folder.\n\n";
			return false;
		}
		// Recursive create folder and return path
		$path = $this->_folder_creator($name, 'controllers');

		// File exist.
		if ( file_exists(APPPATH.'controllers/'.$path.'.php') ) {
			echo "This controller file already exists.\n";
			return false;
		}

		// Actually write file.
		if ( ! write_file(APPPATH.'controllers/'.$path.'.php',
											$this->_controller_creator($path, $extendsName) ) ) {
			echo "Unable to write the file.\n";
			return false;
		}
		echo $path . " controller was created!\n";
		return true;
	}

	public function model($name = Null, $table = Null, $primaryKey = Null, $extendsName = 'CI_Model')
	{
		// No param, Will response help.
		if ( ! isset( $name ) ) {
			echo "\n\033[33mUsage:\n\033[0m";
			echo " model name [table] [primaryKey] [extendsName] \n\n";
			echo "\033[33mArguments:\n\033[0m";
			echo " name		The name of the model class (use . to seperate sub directory like last example)\n";
			echo " table		This class will operate which table\n";
			echo " primaryKey	The primary key of table\n";
			echo " extendsName	This class extends which class\n\n";
			echo "\033[33mExample:\n\033[0m";
			echo " cetusan model Test\n";
			echo " # Create a file Test_model.php in models folder.\n\n";
			echo " cetusan model Product products\n";
			echo " # Create a file Product_model.php contain a variable \$table='products' in models folder.\n\n";
			echo " cetusan model User users user_id CI_Model\n";
			echo " # Create a file User_model.php contain 2 variable \$table='users' \$primaryKey='user_id' and extends CI_Model in models folder.\n\n";
			echo " cetusan model some.other.User users user_id CI_Model\n";
			echo " # Create a file User_model.php contain 2 variable \$table='users' \$primaryKey='user_id' and extends CI_Model in models/some.other folder.\n\n";

			return false;
		}
		// Recursive create folder and return path
		$path = $this->_folder_creator($name, 'models');

		// File exist.
		if ( file_exists(APPPATH.'models/'.$path.'_model.php') ) {
			echo "This model file already exists.\n";
			return false;
		}
		// Actually write file.
		if ( ! write_file(APPPATH.'models/'.$path.'_model.php',
											$this->_model_creator($path, $table, $primaryKey, $extendsName) ) ) {
			echo "Unable to write the file.\n";
			return false;
		}
		echo $path . " model was created!\n";
		return true;
	}

	public function view($name = Null, $params = Null)
	{
		// No param, Will response help.
		if ( ! isset( $name ) ) {
			echo "\n\033[33mUsage:\n\033[0m";
			echo " view name [require_file] [require_file] [require_file] ...more\n\n";
			echo "\033[33mArguments:\n\033[0m";
			echo " name		The name of the view file be create (use . to seperate sub directory like last example)\n";
			echo " require_file	Assign will be required file in the view file (use . to seperate sub directory like last example)\n\n";
			echo "\033[33mExample:\n\033[0m";
			echo " cetusan view Test\n";
			echo " # Create a Test.php file in views folder.\n\n";
			echo " cetusan view some.Test\n";
			echo " # Create a Test.php file in views/some folder.\n\n";
			echo " cetusan view Test template.header\n";
			echo " # Create a Test.php file in views folder and this file contain views/template/header.php file.\n\n";
			echo " cetusan view user.index template.header template.footer \n";
			echo " # Create a index.php file in views/user folder and this file contain views/template/header.php and views/template/footer.php file.\n\n";
			return false;
		}
		// Recursive create folder and return path
		$path = $this->_folder_creator($name, 'views');

		// Remove the first param, in order to match the require file.
		unset($params[0]);

		// File exist.
		if ( file_exists(APPPATH.'views/v_'.$path.'.php') ) {
			echo "This view file already exists.\n";
			return false;
		}

		// Actually write file.
		if ( ! write_file(APPPATH.'views/v_'.$path.'.php',
											$this->_view_creator($params) ) ) {
			echo "Unable to write the file.\n";
			return false;
		}
		echo $path . " view was created!";
		return true;
	}

}