<?php

namespace App\MessageHandler;


trait RogerHandlerTrait {


	protected function isAboutEntityNames($context, array $entityNames = []) {
		if (!isset($context['class']))
			return false;

        $class_array = explode('\\', $context['class']);
        $className = end($class_array);
        
        if (in_array($className, $entityNames))
        	return true;

        return false;
	}
	
}
