<?php
namespace Jenga\Db\Engines\Mongo\Query\Conditions;
/**
 * Condition factory that returns the correct condition object to use for building the Mongo query.
 *
 * @author Chris Santos
 */
 class ConditionFactory {

     public static function get($name, $value, $operator = null) {

         if($operator === null)
             return new Equals($name, $value, $operator);

         switch($operator) {

             default:
                 throw new \Exception('Could not find a Condition object for operator: ' . $operator);
         }
     }
 }