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
			$file = $data[$parameters[1]];

			try {
				Ssh::connect($value, $user, $file);
				return true;
			} catch (ConnectionFailException $e) {
				return false;
			}
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
