<?php

namespace Plume\Core;

trait MysqliTrait{
	/**
	 * 根据Id获取对象
	 * @param  string|int $id
	 * @return array
	 */
    public function fetchById($id) {
		return $this->getConnection()->where($this->getDao()->getTableId(), $id)->get($this->getDao()->getTableName());
	}

	/**
	 * 根据条件查询记录列表
	 * $where 查询条件 【示例： array('title'=>'Fifth news') 】
	 * $fetchNum 要查询的记录数量
	 * $skipNum 要跳过的记录数量
	 * $order 排序条件 【示例： array('id'=>'desc') 】
	 * @param array|\Closure $where
	 * @param array $order
	 * @param int $fetchNum
	 * @param int $skipNum
	 * @return array
	 */
	public function fetch($where, $order=array(), $skipNum=0, $fetchNum=-1) {
		$obj = $this->getConnection();
        if (empty($where)) {
            return array();
        }
        foreach ($where as $key => $value) {
            $obj->where($key, $value);
        }
		if (empty($order) == false) {
			foreach ($order as $key => $value) {
				$obj->orderBy($key, $value);
			}
		}
		$limits = array();
		if ($fetchNum > 0) {
			if ($skipNum >= 0) {
				array_push($limits, $skipNum);
			}
			array_push($limits, $fetchNum);
		}
		if (empty($limits)) {
		    return $obj->get($this->getDao()->getTableName());
        }

		return $obj->get($this->getDao()->getTableName(), $limits);
	}

	/**
	 * 获取满足条件的记录数
	 * @param string|array $where
	 * @return int
	 */
	public function fetchCount($where=array()) {
        $obj = $this->getConnection();
        if (empty($where) == false) {
            foreach ($where as $key => $value) {
                $obj->where($key, $value);
            }
        }
        $count = $obj->getValue($this->getDao()->getTableName(), 'count(1)');
        return (int)$count;
	}

	/**
	 * 根据主键id检查是否存在
	 * @param string $id
	 * @return boolean
	 */
	public function existsId($id) {
        $count = $this->fetchCount(array($this->getDao()->getTableId() => $id));
        return $count > 0 ? true : false;
	}

	public function exists($where=array()) {
        $count = $this->fetchCount($where);
        return $count > 0 ? true : false;
	}

	/**
	 * 获取所有记录
	 * @param string|array $order
	 * @return array
	 */
	public function fetchAll($order=array()) {
		$obj = $this->getConnection();
        if (empty($order) == false) {
            foreach ($order as $key => $value) {
                $obj->orderBy($key, $value);
            }
        }
        return $obj->get($this->getDao()->getTableName());
	}

	/**
	 * 新增对象
	 * @param $insertData array
	 * @return int
	 */
	public function insert($insertData) {
		$this->provider('cache')->cacheClear('db', $this->getDao()->getTableName());
        return $this->getConnection()->insert($this->getDao()->getTableName(), $insertData);
	}

	/**
	 * 更新对象
	 * @param  array $set
	 * @param  string|array|\Closure $where
	 * @return int
	 */
	public function update($set, $where=array()) {
		$this->provider('cache')->cacheClear('db', $this->getDao()->getTableName());
        $obj = $this->getConnection();
        if (empty($where) == false) {
            foreach ($where as $key => $value) {
                $obj->where($key, $value);                     
            }
        }
        return $obj->update($this->getDao()->getTableName(), $set);
	}

	/**
	 * 根据Id删除记录
	 * @param  string|int $id
	 * @return int
	 */
	public function deleteById($id) {
		$this->provider('cache')->cacheClear('db', $this->getDao()->getTableName());
	    $obj = $this->getConnection()->where($this->getDao()->getTableId(), $id);
        return $obj->delete($this->getDao()->getTableName());
	}

	/**
	 * 根据条件删除记录
	 * @param  string|array|\Closure $where
	 * @return int
	 */
	public function delete($where) {
		$this->provider('cache')->cacheClear('db', $this->getDao()->getTableName());
        $obj = null;
        if (empty($where) == false) {
            foreach ($where as $key => $value) {
                $obj = $this->getConnection()->where($key, $value);
            }
        }
        if ($obj ==  null) return false;
        return $obj->delete($this->getDao()->getTableName());
	}

	public function queryBySql($sql, $where=array()) {
        if (empty($where)) return $this->getConnection()->rawQuery($sql);
        return $this->getConnection()->rawQuery($sql, $where);
	}

	public function insertMulti(array $multiInsertData, array $dataKeys = null){
		$this->provider('cache')->cacheClear('db', $this->getDao()->getTableName());
		return $this->getConnection()->insertMulti($this->getDao()->getTableName(), $multiInsertData, $dataKeys);
	}

	//开启事务操作
	public function beginTransaction() {
        $this->getConnection()->startTransaction();
	}

	//提交事务操作
	public function commitTransaction() {
        $this->getConnection()->commit();
	}

	//回滚事务操作
	public function rollbackTransaction() {
        $this->getConnection()->rollback();
	}

	//关闭连接
	public function close(){
		$this->getConnection()->__destruct();
	}

	private function getConnection(){
        $conn = $this->getDao()->connect();
        if (!$conn->ping()){
            $conn->connect();
        }
        return $conn;
	}

	private function getDao(){
		if($this->classType === 'service'){
			if(is_null($this->dao)){
				throw new \Exception('Dao in service can not be null');
			}
			return $this->dao;
		}else{
			//dao
			return $this;
		}
	}
}