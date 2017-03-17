<?php

namespace Zoomyboy\LaravelValidators;

use Illuminate\Support\ServiceProvider as GlobalServiceProvider;
use Validator;

class ServiceProvider extends GlobalServiceProvider {
	public function boot() {
		$this->loadTranslationsFrom(__DIR__.'/../lang', 'validators');
		Validator::extend('fileexists', function($attribute, $value, $parameters, $validator) {
			return file_exists ($value);
		});
		
		$this->replace('fileexists');
	}

	private function replace($rule) {
		Validator::replacer($rule, function($message, $attribute, $ruleOrig, $formParams) use ($rule) {
			return $attribute.': '.trans('validators::validator.'.$rule);
		});
	}
}
