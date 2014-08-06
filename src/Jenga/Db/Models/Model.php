<?php
namespace Jenga\Models;
/**
 * Base model.
 */
class Model {

    public function __construct() {

    }

    /**
     * Default manager used for querying objects.
     *
     * @returns QuerySet
     */
    public static function objects() {

    }

    public static function onSite() {

        $qs = self::objects()->filter(['client_id' => CLIENT_ACCOUNT_ID]);
    }

    /**
     * Alias for using the default manager.
     *
     * @returns QuerySet
     */
    public static function filter() {

        return self::objects();
    }

    public function __get($name) {

    }

    public function save() {

    }
}