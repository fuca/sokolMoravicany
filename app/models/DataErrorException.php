<?php

final class DataErrorException extends Exception {
    
    public function __construct($message, $code, $previous) {
	parent::__construct($message, $code, $previous);
    }
    
    public function __construct(Exception $x) {
	parent::__construct($x->getMessage(), $x->getCode(), $x->getPrevious());
    }
}
