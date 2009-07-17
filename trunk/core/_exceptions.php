<?php
	// this file defines all the wax exceptions
	class WaxException extends Exception {}
	
	// data service exceptions
	class DataServiceException extends WaxException {}					// generic data service exception
	class DataServiceNotFoundException extends WaxException {}			// thrown when a model hasn't been given a data service
	
	// core exceptions
	class BlockNotFoundException extends WaxException {}				// thrown when trying to instantiate an invalid Block ($b = Wax::UseBlock("invalidblock"))

	// model exceptions
	class ModelInitException extends WaxException {} 					// thrown when there was an error getting data from a data service, usually a DS exception is called before it gets to here.
	class ModelNoPrimaryKeyException extends WaxException {}			// thrown when searching and no primary key is found
	class ModelNotFoundException extends WaxException {}				// thrown when trying to fetch a model that isn't defined (ex: $modelroot->invalidmodel)
	class InvalidModelException extends WaxException {}					// thrown when trying to fetch a class that isn't a model (ex: $modelroot->PDO)
	
	// the default exception handler:
	// set_exception_handler(...);
?>