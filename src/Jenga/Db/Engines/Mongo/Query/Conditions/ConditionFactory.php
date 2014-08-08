<?php
namespace Jenga\Db\Engines\Mongo\Query\Conditions;
/**
 * Condition factory that returns the correct condition object to use for building the Mongo query.
 *
 * @author Chris Santos
 */
 class ConditionFactory {

     public static function get($name, $value, $operator = null) {

         if($operator === null) {

             if(is_array($value))
                return new In($name, $value, $operator);

             else
                 return new Equals($name, $value, $operator);
         }

         switch($operator) {

             case '__in':
                 return new In($name, $value, $operator);

             case '__nin':
                 return new NotIn($name, $value, $operator);

             case '__gt':
                 return new GreaterThan($name, $value, $operator);

             case '__gte':
                 return new GreaterThanOrEqual($name, $value, $operator);

             case '__lt':
                 return new LessThan($name, $value, $operator);

             case '__lte':
                 return new LessThanOrEqual($name, $value, $operator);

             default:
                 throw new \Exception('Could not find a Condition object for operator: ' . $operator);
         }
     }
 }