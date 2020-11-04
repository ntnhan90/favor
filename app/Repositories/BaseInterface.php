<?php

namespace App\Repositories;

interface BaseInterface
{
   
    public function getAll($columns = array('*'));
    
    public function paginate($limit = null, $columns = array('*'));

    public function create(array $attributes);
    
    public function update($id, array $attributes);

    public function delete($id);
    
    public function find($id, $columns = array('*'));

    public function findByField($field, $value, $columns = array('*'));

    public function findWhere( array $where , $conditions , $columns = array('*'));


    public function findWhereIn( $field, array $values, $columns = array('*'));

    public function findWhereNotIn( $field, array $values, $columns = array('*'));

    public function groupBy( $field, $columns = array('*'));

    public function with($relations);
}