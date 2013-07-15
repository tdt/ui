<?php
 
/**
 * Input the mapping file and input file
 * @copyright (C) 2013 by OKFN Belgium
 * @license AGPLv3
 * @author Leen De Baets
 * @author Jeppe Knockaert
 * @author Nicolas Dierck
 */

// Needed for conntecting to the client
use Guzzle\Http\Client;
// included for catching the 401 errors (authorization needed)
use Guzzle\Http\Exception\ClientErrorResponseException;

use Symfony\Component\HttpFoundation\Request;
// for loading the files
use Symfony\Component\HttpFoundation\File\UploadedFile;

$app->match('/ui/inputfile{url}', function (Request $request) use ($app,$hostname,$data) {
	

	// enumerating the possible types of input files
	$possibilities['JSON'] = "JSON";
	$possibilities['XML'] = "XML";
	$possibilities['CSV1'] = "CSV with header row and ; as a delimiter";
	$possibilities['CSV2'] = "CSV with header row and , as a delimiter";
	$possibilities['CSV3'] = "CSV without header row and ; as a delimiter";
	$possibilities['CSV4'] = "CSV without header row and , as a delimiter";

	$form = $app['form.factory']->createBuilder('form');
	$form = $form->add('typeinput','choice',array('choices' => $possibilities, 'multiple' => false, 'expanded' => false, 'label' => false));
	$form = $form->add('inputfile','text',array('label' => 'Choose input file'));
	$form = $form->add('mappingfile','text',array('label' => 'Choose mapping file'));
	$form = $form->getForm();

	if ('POST' == $request->getMethod()) {
		$form->bind($request);
		$data2 = $form->getData();
		$app['session']->set('inputfile',$data2['inputfile']);
		$app['session']->set('mappingfile',$data2['mappingfile']);
		$app['session']->set('typeinput',$data2['typeinput']);
		return $app->redirect('/ui/input');
	}

	$data['form'] = $form->createView();
	// adding the datafields title and function for the twig file
	$data['title']= "";
	$data['header']= "";
	$data['button']= "Further";
	return $app['twig']->render('form.twig', $data);

})->value('url', '');