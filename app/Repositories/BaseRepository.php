<?php

namespace App\Repositories;
use App\Repositories\BaseInterface;

abstract class BaseRepository implements BaseInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $_model;

    public function __construct()
    {
        $this->setModel();
    }

    abstract public function getModel();

    public function setModel()
    {
        $this->_model = app()->make($this->getModel() );
    }

    public function getAll($columns = array('*'))
    {
        return $this->_model->all($columns);
    }

    public function paginate($limit = null, $columns = array('*')){
        $limit = is_null($limit) ?  15 : $limit;
        $results = $this->_model->paginate($limit, $columns);
        return  $results ;
    }

    public function create(array $attributes){
        return $this->_model->create($attributes);
    }

    public function update($id, array $attributes) {
        $result = $this->find($id);
        if ($result) {
            $result->update($attributes);
            return $result;
        }

        return false;
    }

    public function delete($id)
    {
        $result = $this->find($id);
        if ($result) {
            $result->delete();

            return true;
        }

        return false;
    }

    public function find($id, $columns = array('*') )
    {
        $result = $this->_model->findOrFail($id, $columns);

        return $result;
    }

    public function findByField($field, $value, $columns = array('*')){

    }

    public function findWhere( array $where ,$conditions,  $columns = array('*')){
        foreach ($where as $field => $value) {
            $this->_model = $this->_model->where($field,$conditions,$value);
        }
        $result = $this->_model->get($columns);

        return $result;
    }

    public function findWhereIn( $field, array $values, $columns = array('*')){
        $result = $this->_model->whereIn($field, $values)->get($columns);
        return $result;
    }

    public function findWhereNotIn( $field, array $values, $columns = array('*')){
        $result = $this->_model->whereNotIn($field, $values)->get($columns);
        return $result;
    }

    public function groupBy( $field ,$columns = array('*')){
        $result = $this->_model->all($columns)->groupBy($field);
        return $result;
    }


    public function with($relations)
    {
        $result = $this->_model->with($relations);
        return $result;
    }
}