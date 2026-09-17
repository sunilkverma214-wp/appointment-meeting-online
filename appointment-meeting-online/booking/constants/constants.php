<?php // API Keys
	$zoom_api_account_id =  get_option( 'zoom_api_account_id', $default = false );  
	$zoom_api_client_id =  get_option( 'zoom_api_client_id', $default = false );  
	$zoom_api_client_secret_key = get_option( 'zoom_api_client_secret_key', $default = false );
	$base64_Client_Id_and_Secret = base64_encode($zoom_api_client_id.':'.$zoom_api_client_secret_key);

	define('ZOOM_API_ACCOUNT_ID', $zoom_api_account_id);
	define('ZOOM_API_CLIENT_ID', $zoom_api_client_id);	 
	define('ZOOM_API_CLIENT_SECRET_KEY', $zoom_api_client_secret_key);
	define('BASE64_CLIENT_ID_AND_SECRET', $base64_Client_Id_and_Secret);
?>