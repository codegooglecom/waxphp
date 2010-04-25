<?php
    /**
    * WaxObjects are the base objects used in Wax framework development.
    *
    * The primary advantages to using a WaxObject instead of a DCIObject
    * is that WaxObjects are accessible via RESTful URLs.  For example,
    * say there was a DCIObject for the Users model.  Lots of translation
    * code would be necessary to transfer a REST request to that object.
    *
    * Instead, we can have URLs in this form:
    *     /ObjName[.identifier]/role method/arg1/arg2/arg3
    * And there can be specialized role methods for handling this information
    *
    * As a result, the models and controllers become transparent as far
    * as the user/programmer is concerned.  Primarily, WaxBlocks are used for
    * type hinting, as we don't want any old DCIObject to be accessible via
    * a REST url.
    */
    class WaxObject extends DCIObject {
        var $id;
    }
?>