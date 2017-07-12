<?php

namespace Zoomyboy\LaravelValidators;

use Illuminate\Support\ServiceProvider as GlobalServiceProvider;
use Zoomyboy\PhpSsh\Exceptions\ConnectionFailException;
use Zoomyboy\PhpSsh\Client;
use Validator;


class ServiceProvider extends GlobalServiceProvider {
	public function boot() {
		$this->loadTranslationsFrom(__DIR__.'/../lang', 'validators');
		Validator::extend('fileexists', function($attribute, $value, $parameters, $validator) {
			return file_exists ($value) && is_file($value);
		});

		Validator::extend('directoryexists', function($attribute, $value, $parameters, $validator) {
			return file_exists ($value) && is_dir($value);
		});

		Validator::extend('sshlogin', function($attribute, $value, $parameters, $validator) {
			if (!$value['host'] || !$value['user'] || !$value['auth'] || !$value['authMethod']) {return false;}

			$method = Client::authMethodFromIndex($value['authMethod']);

			return Client::auth($value['host'], $value['user'])->{$method}($value['auth'])->check();
		});
		
		$this->replace('fileexists');
		$this->replace('directoryexists');
		$this->replace('sshlogin');
	}

	private function replace($rule) {
		Validator::replacer($rule, function($message, $attribute, $ruleOrig, $formParams) use ($rule) {
			return $attribute.': '.trans('validators::validator.'.$rule);
		});
	}
}
