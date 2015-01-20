<?php

class _Core_Helper {
	
	protected function jLoaderRegister($class, $path, $force=true){
		if ( file_exists($path) ){
			JLoader::register($class, $path, $force);
		}
	}
	
}