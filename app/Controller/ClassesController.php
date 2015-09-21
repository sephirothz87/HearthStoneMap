<?php
class ClassesController extends AppController {
	private $ERR_CODE_1 = 1;
	private $ERR_CODE_2 = 2;
	private $ERR_CODE_3 = 3;
	public function beforeFilter() {
		$this->autoRender = false;
	}
	public function index() {
		$this->response->body("hello,cakephp.index()");
	}
	public function getClasses() {
		fwrite(fopen("F:\\tmp\\log.txt", "a"), "[".date('Y-m-d H:i:s',time()+60*60*6)."]"."getClasses start" . "\n");
		$this->layout = false;
		
		$ret = array (
				'result' => [ ] 
		);
		
		$classes_model = $this->loadAppModel("ClassesModel");
		
// 		$result=$classes_model->find("All",array('id','name_cn'));

		$s_id=5;
// 		$row = $classes_model->getSimpleRow(array("id" => $s_id));
		

		$options = array ();
		$options ['order'] = 'id';
		
		$options ['conditions'] = array("id" => $s_id);
		
		$list = $classes_model->find ( "all", $options );
		
		$ret = array ();
		foreach ( $list as $row ) {
			if ($row [$classes_model->alias]) {
				$ret [] = $row [$classes_model->alias];
			}
		}

		$result=$ret[0];
		
		$id = $result["id"];

		$name_cn = $result["name_cn"];
		
		fwrite(fopen("F:\\tmp\\log.txt", "a"), "[".date('Y-m-d H:i:s',time()+60*60*6)."]"."id = ".$id . "\n");
		fwrite(fopen("F:\\tmp\\log.txt", "a"), "[".date('Y-m-d H:i:s',time()+60*60*6)."]"."name_cn = ".$name_cn . "\n");
		$this->response->body("hello,cakephp");
		fwrite(fopen("F:\\tmp\\log.txt", "a"), "[".date('Y-m-d H:i:s',time()+60*60*6)."]"."getClasses end" . "\n");
	}
}