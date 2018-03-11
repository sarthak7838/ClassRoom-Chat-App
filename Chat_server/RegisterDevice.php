<?php
	require_once 'DbOperation.php';
	$response = array();

	if($_SERVER['REQUEST_METHOD']=='POST'){

		$token = $_POST['token'];
		$email = $_POST['email'];
		$name=$_POST['name'];
		$db = new DbOperation();

		$result = $db->registerDevice($name,$email,$token);

		if($result == 0){
			$user=$db->getUser($email);
			$response['id']=$user['id'];
			$response['name']=$user['name'];
			$reposne['email']=$user['email'];
			$response['error'] = false;
			$response['message'] = 'Device registered successfully';

		}elseif($result == 2){
			$user=$db->getUser($email);
			$response['id']=$user['id'];
			$response['name']=$user['name'];
			$reposne['email']=$user['email'];
			$response['error'] = false;
			$response['message'] = 'Device already registered';
		}else{
			$response['error'] = true;
			$response['message']='Device not registered';
		}
	}else{
		$response['error']=true;
		$response['message']='Invalid Request...';
	}

	echo json_encode($response);
