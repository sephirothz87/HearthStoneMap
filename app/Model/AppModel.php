<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
	private $cache_key = "";
	public $rules = array ();
	function __construct($id = false, $table = null, $ds = null) {
		parent::__construct ( $id, $table, $ds );

		if ($this->useTable) {
			$this->alias = $this->useTable;
		}
	}
	public function getRules($fields = null) {
		if ($fields == null) {
			return $this->rules;
		}
		if (is_string ( $fields )) {
			$fields = explode ( ",", $fields );
		}
		if (is_array ( $fields )) {
			$ret = array ();
			foreach ( $fields as $name ) {
				$name = trim ( $name );
				if ($name) {
					if (isset ( $this->rules [$name] )) {
						$ret [$name] = $this->rules [$name];
					}
				}
			}
			return $ret;
		}
	}
	/**
	 * 新建数据表
	 *
	 * @param array $data
	 * @param int $created_by
	 */
	function createRow($data, $created_by = 0) {
		if (! isset ( $data [$this->alias] )) {
			$tmp = array ();
			$tmp [$this->alias] = $data;
			$data = $tmp;
		}
		$datetime = date ( "Y-m-d H:i:s" );
		$data [$this->alias] ['created_by'] = $created_by;
		$data [$this->alias] ['created_at'] = $datetime;
		$data [$this->alias] ['updated_by'] = $created_by;
		$data [$this->alias] ['updated_at'] = $datetime;

		$this->create ();
		$this->save ( $data, false );
	}
	function updateRowById($id, $data, $updated_by = 0) {
		$this->id = $id;
		$this->updateRow ( $data, $updated_by );
	}
	function updateRow($data, $updated_by = 0) {
		if (! isset ( $data [$this->alias] )) {
			$tmp = array ();
			$tmp [$this->alias] = $data;
			$data = $tmp;
		}
		$datetime = date ( "Y-m-d H:i:s" );
		$data [$this->alias] ['updated_by'] = $updated_by;
		$data [$this->alias] ['updated_at'] = $datetime;
		$this->save ( $data, false );
	}

	function updateByCond($cond,$data,$updated_by = 0){
		$data['updated_by'] = $updated_by;
		$data['updated_at'] = date ( "'Y-m-d H:i:s'" ); // updateAllメソッドは敢えてquoteを付けないとダメ

		$this->updateAll($data,$cond);
	}
	function deleteByCond($cond){
		$this->deleteAll($cond);
	}
	function updateOrInsertAll($data, $options = array(), $updated_by = 0){
		
		foreach($data as $key => $val){
			$data[$key]['updated_at'] = date("Y-m-d H:i:s");
			$data[$key]['updated_by'] = $updated_by;
		}
		$this->log($data);
		$this->saveAll($data, $options);
	}
	/**
	 * 获得条件完全一致的一行
	 *
	 * @param unknown $data
	 * @param string $no_alias
	 * @return Ambigous <>|Ambigous <multitype:, NULL, mixed>
	 */
	function getSimpleRow($condisions, $no_alias = true,$options = array()) {
		$options['conditions'] = $condisions;
		$options['fields'] = '*';

		$row = $this->find ( "first", $options );

		if ($row) {
			$id = $row [$this->alias] ['id'];
			$this->id = $id;
		}

		if ($row && $no_alias) {
			return $row [$this->alias];
		} else {
			return $row;
		}
	}
	function getByID($id) {
		$row = $this->findById ( $id );
		if (isset ( $row ) && isset ( $row [$this->alias] )) {
			return $row [$this->alias];
		} else {
			return null;
		}
	}
	public function disableRow($id, $account_id = 0) {
		$data = array (
				'enable' => 0
		);
		$this->id = $id;
		return $this->updateRow ( $data, $account_id );
	}
	function getPageList($conditions, $order = 'id', $limit = 20, $page = 1,$options = array()) {
		if ($limit > 0) {
			$options ['limit'] = $limit;
			$options ['page'] = $page;
		}
		$options ['order'] = $order;

		$options ['conditions'] = $conditions;
		$list = $this->find ( "all", $options );

		$ret = array ();
		foreach ( $list as $row ) {
			foreach($row as $alias => $array){
				if($alias == $this->alias || $alias === 0){ continue; }
				foreach($array as $Key => $Value){ $row[$this->alias]["${alias}_$Key"] = $Value; }
			}

			if(isset($row[$this->alias])){
				if(isset($row[0])){ $row[$this->alias] = array_merge($row[$this->alias],$row[0]); }
				$ret[] = $row[$this->alias];
			}elseif(isset($row[0])){
				$ret[] = $row[0];
			}
		}
		return $ret;
	}
	function getAllList($conditions, $order = 'id') {
		$options = array ();
		$options ['order'] = $order;

		$options ['conditions'] = $conditions;
		$list = $this->find ( "all", $options );
		$ret = array ();
		foreach ( $list as $row ) {
			if ($row [$this->alias]) {
				$ret [] = $row [$this->alias];
			}
		}
		return $ret;
	}
	function getAllListCond($Cond = array()){
		$list = $this->find("all",$Cond);
		$ret = array();
		foreach($list as $row){
			if($row[$this->alias]){ $ret[] = $row[$this->alias]; }
		}
		return $ret;
	}
	function getCountCond($Cond = array()){
		return$this->find("count",$Cond);
	}
	function getCntRow($conditions,$options = array ()) {
		$options ['conditions'] = $conditions;
		$list = $this->find ( "all", $options );
		return count ( $list );
	}

	/**
	 * 获得与条件指定一致的前缀符合的值
	 *
	 * @param unknown $prefix
	 * @param unknown $values
	 * @return multitype:unknown
	 */
	function getFieldMatchValues($prefix, $values) {
		$ret = array ();
		$len = strlen ( $prefix );
		foreach ( $values as $key => $val ) {
			if (substr ( $key, 0, $len ) == $prefix) {
				$ret [$key] = $val;
			}
		}
		return $ret;
	}

	/**
	 * 获得满足条件的最大值
	 *
	 * @param unknown $data
	 * @param string $max_field
	 * @return Ambigous <>|Ambigous <multitype:, NULL, mixed>
	 */
	function getMaxRow($condisions, $max_field) {
		$options ['conditions'] = $condisions;
		$options ['fields'] = 'MAX(' . $max_field . ') as max';
		$row = $this->find ( "first", $options );

		if ($row) {
			$max = $row [0] ['max'];
		} else {
			$max = 0;
		}

		return $max;
	}

	public function getOneRow($cond, $order,$no_alias = true) {
		$options = array();
		$options['conditions'] = $cond;
		$options['order'] = $order;

		$row = $this->find('first',$options);
		if ($row && $no_alias) {
			return $row [$this->alias];
		} else {
			return $row;
		}
	}
}
