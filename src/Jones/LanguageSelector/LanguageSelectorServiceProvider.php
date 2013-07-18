<?php namespace Jones\LanguageSelector;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Cartalyst\Sentry\Facades\Laravel\Sentry;

class LanguageSelectorServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('jones/language-selector');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerFilter();
	}

	protected function registerFilter()
	{
		$this->app['router']->before(function ($request) {
			//Load all supported languages
			$dirs = File::directories(app_path().'/lang/');
    		foreach($dirs as $dir)
			{
				$available[] = substr($dir, strrpos($dir, "\\")+1);
			}
			
			//If we have a language key, first check whether the user has selected a valid language
			$locale = false;;
			if(Config::get('language-selector::lang_key') !== false) {
				$attr = Config::get('language-selector::lang_key');
				$user = false;

				//Using the Auth class?
    			if(Auth::check())
				{
					$user = Auth::user();
				}

				//Or Sentry?
				if(class_exists('Cartalyst\Sentry\SentryServiceProvider') && Sentry::check())
				{
					$user = Sentry::getUser();
				}

				//If we have a user check their language
				if($user !== false)
				{
					$value = $user->$attr;
					if(in_array($value, $available))
					{
						$locale = $value;
					}
				}
			}

			//We hadn't a user? Ok check for the "Accept-Language" Header
			if($locale === false)
			{
				$langs = array();

				//Fetch the languages
       			if (Request::server('HTTP_ACCEPT_LANGUAGE') && Request::server('HTTP_ACCEPT_LANGUAGE') != '')
				{
					$langs = preg_replace('/(;q=[0-9\.]+)/i', '', strtolower(trim(Request::server('HTTP_ACCEPT_LANGUAGE'))));
		
					$langs = explode(',', $langs);
				}

				//We only need to loop through the languages
				foreach ($langs as $lang)
				{
					if(in_array($lang, $available))
					{
						$locale = $lang;
						break;
					}
				}
			}

			//No languages available? Then fallback to the language in the config			
			if($locale === false)
			{
				$locale = Config::get('app.locale');
			}

			//Finally we set the locale
			App::setLocale($locale);
		});
	}
}