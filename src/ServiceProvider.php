<?php

namespace Zoomyboy\LaravelValidators;

use Illuminate\Support\ServiceProvider as GlobalServiceProvider;
use Zoomyboy\PhpSsh\Exceptions\ConnectionFailException;
use Zoomyboy\PhpSsh\Ssh;
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
			$data = $validator->getData();
			$user = $data[$parameters[0]];
			$method = 'with' . ucfirst($data[$parameters[1]]);
			$auth = $data[$parameters[2]];

			return Ssh::auth($value, $user)->{$method}($auth)->check();
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
