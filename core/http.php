<?php 
class HttpHelper {
    function getIntValue($key, $method) {
	if(filter_has_var($method, $key) !== false && filter_input($method, $key, FILTER_VALIDATE_INT) !== false)
	{
            return filter_input($method, $key, FILTER_SANITIZE_NUMBER_INT);
	}
		
	return null;
    }
    
    function getFloatValue($key, $method) {
	if(filter_has_var($method, $key) !== false && filter_input($method, $key, FILTER_VALIDATE_FLOAT) !== false)
	{
            return filter_input($method, $key);
	}
		
	return null;
    }
	
    function getValue($key, $method) {
        if(filter_has_var($method, $key) !== false)
	{
            return filter_input($method, $key);
	}
		
	return null;
    }
	
    function throwPageNotFound() {
        header('HTTP/1.0 404 Not Found');
        echo "<h1>404 File not found</h1>";
        exit();
    }
    
    function redirect($url) {
        header('Location: '.$url);
        exit();
    }
}
?>