<?php
namespace Jenga\Db\Engines\Mongo\Query\Conditions;
/**
 * Condition factory that returns the correct condition object to use for building the Mongo query.
 *
 * @author Chris Santos
 */
 class ConditionFactory {

     public static function get($field, $value, $operator = null) {

         if(is_a($value, 'Jenga\Db\Query\Filters\Filter')) {

             switch(get_class($value)) {

                 case 'Jenga\Db\Query\Filters\Field':
                     break;

                 case 'Jenga\Db\Query\Filters\Nested':
                     return new Nested($field, $value, $operator);

                 default:
                     throw new \Exception('Unknown Filter type for the Mongo engine.');
             }
         }

         if($operator === null) {

             if(is_array($value))
                return new In($field, $value, $operator);

             else
                 return new Equals($field, $value, $operator);
         }

         switch($operator) {

             case '__in':
                 return new In($field, $value, $operator);

             case '__nin':
                 return new NotIn($field, $value, $operator);

             case '__gt':
                 return new GreaterThan($field, $value, $operator);

             case '__gte':
                 return new GreaterThanOrEqual($field, $value, $operator);

             case '__lt':
                 return new LessThan($field, $value, $operator);

             case '__lte':
                 return new LessThanOrEqual($field, $value, $operator);

             default:
                 throw new \Exception('Could not find a Condition object for operator: ' . $operator);
         }
     }
 }