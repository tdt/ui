<?php
 
/**
 * editting, deleting, showing the packages and resources
 * @copyright (C) 2013 by OKFN Belgium
 * @license AGPLv3
 * @author Leen De Baets
 * @author Jeppe Knockaert
 * @author Nicolas Dierck
 */

//needed for conntecting to the client
use Guzzle\Http\Client;
//needed for the PUT request
use Guzzle\Http\Message;
use Guzzle\Http\Query;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

// included for catching the 401 errors (authorization needed)
use Guzzle\Http\Exception\ClientErrorResponseException;

$app->match('/ui/package/remove{url}', function (Request $request) use ($app,$hostname) {
	
	$client = new Client();

	try{
		$path = $hostname."tdtadmin/resources/".$request->get('path');
		// checking if once in a session time a username and password is given to authorise for deleting
		// if not, try without authentication
		if ($app['session']->get('userrm') == null || $app['session']->get('pswdrm') ==null) {
			$request = $client->delete($path);
		}
		else {
			$request = $client->delete($path)->setAuth($app['session']->get('userrm'),$app['session']->get('pswdrm'));
		}
		$response = $request->send();
	} catch(ClientErrorResponseException $e) {
		// if tried with authentication and it failed 
		// or when tried without authentication and authentication is needed
		if ($e->getResponse()->getStatusCode() == 401) {
			// necessary information is stored in the session object, needed to redo the request after authentication
			$app['session']->set('method','remove');
			$app['session']->set('path',$path);
			$app['session']->set('redirect',$hostname.'ui/package');
			$app['session']->set('referer',$hostname.'ui/package/remove');
			return $app->redirect('../../ui/authentication');
		} else {
	 		$app['session']->set('error',$e->getResponse()->getStatusCode().": ".$e->getResponse()->getReasonPhrase());
            return $app->redirect('../../ui/error');
	 	}
	}	
	return $app->redirect('../../ui/package');
	
})->value('url', '');

$app->match('/ui/resource/functions{url}', function (Request $request) use ($app,$hostname) {

	// if you want to remove a resource
	if ($request->get("remove") != null){
		$client = new Client();
		try{
			$path = $hostname."tdtadmin/resources/".$request->get('path');
			// controlling if once in a time a username and password is given to authorise for deleting
			// if not, try without authentication
			if ($app['session']->get('userrm') == null || $app['session']->get('pswdrm') ==null) {
				$request = $client->delete($path);
			}
			else {
				$request = $client->delete($path)->setAuth($app['session']->get('userrm'),$app['session']->get('pswdrm'));
			}
			$response = $request->send();
		} catch(ClientErrorResponseException $e) {
			// if tried with authentication and it failed 
			// or when tried without authentication and authentication is needed
			if ($e->getResponse()->getStatusCode() == 401) {
				// necessary information is stored in the session object, needed to redo the request after authentication
				$app['session']->set('method','remove');
				$app['session']->set('path',$path);
				$app['session']->set('redirect',$hostname.'ui/package');
				$app['session']->set('referer',$hostname.'ui/resource/functions');
				return $app->redirect('../../ui/authentication');
			} else {
		 		$app['session']->set('error',$e->getResponse()->getStatusCode().": ".$e->getResponse()->getReasonPhrase());
                return $app->redirect('../../ui/error');
		 	}
		}
		return $app->redirect('../../ui/package');
	}
	// if you want to edit a resource
	else if($request->get("edit") != null){
		// redirecting to the page that will render the form for editing
		$app['session']->set('pathtoresource',$request->get('path'));
		return $app->redirect('../../ui/resource/edit');
	}
	// if you want to get a resource in json format
	else if($request->get("json") != null){
		$client = new Client($hostname);
		try{
			// adding .json to the path to get it in a json format
			$path = $request->get('path').".json";
			// controlling if once in a time a username and password is given to authorise for getting
			// if not, try without authentication
			if ($app['session']->get('userget') == null || $app['session']->get('pswdget') ==null) {
				$request = $client->get($path);
			}	
			else{
				$request = $client->get($path)->setAuth($app['session']->get('userget'),$app['session']->get('pswdget'));
			}
			$response = $request->send()->getBody();
		} catch(ClientErrorResponseException $e) {
			// if tried with authentication and it failed 
			// or when tried without authentication and authentication is needed
			if ($e->getResponse()->getStatusCode() == 401) {
				// necessary information is stored in the session object, needed to redo the request after authentication
				$app['session']->set('method','getFile');
				$app['session']->set('path',$path);
				$app['session']->set('redirect',$hostname.'ui/package');
				$app['session']->set('referer',$hostname.'ui/resource/functions');
				return $app->redirect('../../ui/authentication');
			} else{
				$app['session']->set('error',$e->getResponse()->getStatusCode().": ".$e->getResponse()->getReasonPhrase());
                return $app->redirect('../../ui/error');
			}
		}
		return $response;
	}
	// if you want to get a resource in php format
	else{
		$client = new Client($hostname);
		try{
			// adding .php to the path to get it in a php format
			$path = $request->get('path').".php";
			// controlling if once in a time a username and password is given to authorise for getting
			// if not, try without authentication
			if ($app['session']->get('userget') == null || $app['session']->get('pswdget') ==null) {
				$request = $client->get($path);
			}	
			else{
				$request = $client->get($path)->setAuth($app['session']->get('userget'),$app['session']->get('pswdget'));
			}
			$response = $request->send()->getBody();
		} catch(ClientErrorResponseException $e) {
			// if tried with authentication and it failed 
			// or when tried without authentication and authentication is needed
			if ($e->getResponse()->getStatusCode() == 401) {
				// necessary information is stored in the session object, needed to redo the request after authentication
				$app['session']->set('method','getFile');
				$app['session']->set('path',$path);
				$app['session']->set('redirect',$hostname.'ui/package');
				$app['session']->set('referer',$hostname.'ui/resource/functions');
				return $app->redirect('../../ui/authentication');
			} else {
		 		$app['session']->set('error',$e->getResponse()->getStatusCode().": ".$e->getResponse()->getReasonPhrase());
                return $app->redirect('../../ui/error');
		 	}
		}
		return $response;

	}
})->value('url', '');