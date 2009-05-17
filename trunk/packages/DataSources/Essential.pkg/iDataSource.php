<?php
    interface iDataSource {
        function Get(array $args);      // get a variable from a resource
        function GetAll(array $args);   // get all the data from the data set
        function Insert(array $args);   // set a variable in the resource
        function Update(array $args);   // perform an update 
        function Remove(array $args);   // perform a deletion
        
        function Load(array $args);     // load/connect to the resource
        function Save();                // save any changes made
    }
?>