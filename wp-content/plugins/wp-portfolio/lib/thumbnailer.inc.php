<?php
/**
 * Functions and code specifically relating to just the thumbnailer.
 */

/**
 * Generate the cache filename for an image URL.
 * @param $imageurl The URL of the image.
 * @param $args The arguments that determine what image to generate that are to be used as part of the filename.
 * @param $extension The file extension to use.
 * 
 * @return String The generated cache filename.
 */
function WPPortfolio_generateFilename($imageurl, $args, $extension)
{
	return sprintf('%s_%s', md5($imageurl), md5(serialize($args))) . '.' . $extension; 
}


/**
 * Fetch a custom thumbnail URL and resize it to match the maximum dimensions defined by the required thumbnail sizes.
 * @param $imageurl The image URL.
 * @param $size_override If specified, the standard size to use to resize the custom thumbnail.
 * @param $forceUpdate If true, force the update of the cached thumbnail.
 * @return String The full path of the image to render.
 */
function WPPortfolio_getAdjustedCustomThumbnail($imageurl, $size_override = false, $forceUpdate = false) 
{
	// Determine cache directory
	$actualThumbPath = trailingslashit(WPPortfolio_getThumbPathActualDir());
	
	// Create cache directory if it doesn't exist
	WPPortfolio_createCacheDirectory();

	// Get all the options from the database for the thumbnail handling
    $setting_fetch_method   = stripslashes(get_option('WPPortfolio_setting_fetch_method'));
    // If $size_override is specified, then use that size rather than the size stored in the settings.
    if ($size_override) {
    	$maxsize = $size_override;
    } 
    // No size override
    else
    {
    	// Custom size specified, so work out the max height and width
		if ($customSize = WPPortfolio_getCustomSizeOption()) 
		{
			$maxWidth  = $customSize;
			$maxHeight = (($customSize / 4) * 3);
			$maxsize   = $maxWidth . 'x' . $maxHeight;
			$customThumbSize = true;
		}

		// No custom size, just do the standard size.
    	else {
    		$maxsize = stripslashes(get_option('WPPortfolio_setting_stw_thumb_size'));
    	}
    }   

    // How are we going to scale the image?
    $setting_scale_type = get_option('WPPortfolio_setting_scale_type');
    
    // Use arguments to work out the target filename	
    $args = array('maxsize' => $maxsize, 'setting_scale_type' => '$setting_scale_type');
    
    // What type of image have we got?
    $fileextension = strtolower(substr(strrchr($imageurl, '.'), 1));
	switch ($fileextension)  
	{     
		case 'png':  
			break;  
			
		case 'gif':  
			break;   
		 
		// Also handles jpeg
		default:  
			$fileextension = 'jpg';
			break;
	}  

    $filename = WPPortfolio_generateFilename($imageurl, $args, $fileextension);    
	$filepath = $actualThumbPath . $filename;

	// As of V1.19 
	// Move old files if they exist - for people with older style permanent caches 
	$filepathOld = $actualThumbPath . md5($imageurl.$maxsize.$setting_scale_type).'.jpg'; 
	
	if (file_exists($filepathOld)) {
		rename($filepathOld, $filepath);
	}

		
	// Work out if we need to update the cached and resized thumbnail
	if ($forceUpdate || WPPortfolio_cacheFileExpired($filepath))
	{		
		WPPortfolio_downloadRemoteImageToLocalPath($imageurl, $filepath, $setting_fetch_method);
		
		// Now we've got the image, resize it.
		if (file_exists($filepath))
		{
			// Turn the standard sizes into actual pixel sizes for the resizing function.	
			switch($maxsize) {
				case "sm":
					$maxWidth = 120;
					$maxHeight = 90;
					break;
					
				case "lg":
					$maxWidth = 200;
					$maxHeight = 150;
					break;
					
				case "xlg":
					$maxWidth = 320;
					$maxHeight = 240;
					break;
					
				// We've got our custom sizes then. $maxWidth and $maxHeight already
				// have the values we want.
				default: 					
					break;
			}
			
			// Resize the image based on settings to scale to width, scale to height, or scale to both.
			switch ($setting_scale_type)
			{
				case 'scale-height':
						$maxWidth = 0;
					break;
				case 'scale-width':
						$maxHeight = 0;
					break;
					
				// scale-both
				default:
						$resizeOption = 'scale-both';
					break;
			}
			
			WPPortfolio_resizeImage($imageurl, $filepath, $maxWidth, $maxHeight);
		}
	}
    
	// File downloaded successfully
	if (file_exists($filepath)) {
		$webFilePath = WPPortfolio_getThumbPathURL();		
		return "$webFilePath/$filename";
	}
	// Something went wrong, so return default image 
	else {
		$pendingThumbPath = WPPortfolio_getPendingThumbURLPath();
		if ($customThumbSize) { $maxsize = 'xlg'; }
		return "$pendingThumbPath$maxsize.jpg"; 
	}
}



/**
 * Determine if specified file has expired from the cache.
 * @param $filepath The full path of the file to check for in the cache.
 * @return Boolean True if the file has expired or no longer exists, or false if the file is still valid.
 */
function WPPortfolio_cacheFileExpired($filepath)
{
	// Use setting to check age of files.
	$setting_cache_days	= stripslashes(get_option('WPPortfolio_setting_cache_days')) + 0;
	
	// If cached thumbnails never expire, then just check if file exists or not.
	if ($setting_cache_days == 0) {
		return (!file_exists($filepath));
	} 
	// If thumbnails are allowed to expire, then check age of files and if file exists.
	else {	
		$cutoff = time() - 3600 * 24 * $setting_cache_days;
		return (!file_exists($filepath) || filemtime($filepath) <= $cutoff);	
	}
}





/**
 * Resize the specified image using the maximum dimensions provided as arguments.
 * @param $imagepath The actual file path of the image to resize.
 * @param $maxWidth The maximum width to resize the image to.
 * @param $maxHeight The maximum height to resize the image to.
 * @return unknown_type
 */
function WPPortfolio_resizeImage($originalImage, $imagepath, $maxWidth = 120, $maxHeight = 80)
{
	// What sort of image? Check the extension from the original image URL to determine the original 
	// image format, as we can't use the extension of the file in the cache, as that has already been 
	// changed to jpg.  
	$type = strtolower(substr(strrchr($originalImage, '.'), 1));

	$quality = 0;  
	switch ($type)  
	{     
		case 'png':  
			$image_create_func = 'ImageCreateFromPNG';  
			$image_save_func = 'ImagePNG';  
	 	
			// Compression Level: from 0  (no compression) to 9  
			$quality = 0;  
			break;  
			
		case 'gif':  
			$image_create_func = 'ImageCreateFromGIF';  
			$image_save_func = 'ImageGIF';   
			break;   
		 
		// Also handles jpeg
		default:  
			$image_create_func = 'ImageCreateFromJPEG';  
			$image_save_func = 'ImageJPEG';    
			$type = 'jpg';

			// Best Quality: 100  
			$quality = 100;
		break;  
	}  
	
	// Load image into GD (now that it's stored locally)
	$img = $image_create_func($imagepath);

	// Resize image to max size 
	$newimg = WPPortfolio_resizeImageResource($img, $maxWidth, $maxHeight, $type);
		
	// Save using quality parameter if available
	if (isset($quality)) {  
		$image_save_func($newimg, $imagepath, $quality);  
	}  
	else {  
		$image_save_func($newimg, $imagepath);  
	}  	
	
	// All done, clean up
	imagedestroy($img);
	imagedestroy($newimg);
		
}

/**
 * Resize the specified image resource to the maximum desired width and height.
 * 
 * @param Resource $imgresource The image resource to resize to the maximum desired width and height.
 * @param Integer $desired_max_width The desired maximum width.
 * @param Integer $desired_max_height The desired maximum height.
 * @param String $type The type of image being resized (gif, jpg, png). 
 * 
 * @return Image Resource The resized image resource.
 */
function WPPortfolio_resizeImageResource($imgresource, $desired_max_width, $desired_max_height, $type)
{
	$old_width  = imageSX($imgresource);
	$old_height = imageSY($imgresource);
	$new_width  = $old_width;
	$new_height = $old_height;
	
	// Scale width to match rescaled height.
	if ($desired_max_width == 0)
	{
		// Work out the aspect ratio, as we need to ensure the image stays in 
		// the correct dimensions
		$aspect_ratio = $old_height / $old_width;
		
		// Use $desired_max_height as the new_height, and change new_width using 
		// the aspect ratio
		$new_height = $desired_max_height;
		$new_width = $new_height / $aspect_ratio;
	}
	
	else if ($desired_max_height == 0) {
		// Work out the aspect ratio, as we need to ensure the image stays in 
		// the correct dimensions
		$aspect_ratio = $old_width / $old_height;
		
		// Use $desired_max_width as the new_width, and change new_height using 
		// the aspect ratio
		$new_width = $desired_max_width;
		$new_height = $new_width / $aspect_ratio;
	}
	
	// Check that image is already larger than needed, i.e. so needs resizing.
	else if ($old_width > $desired_max_width  || $old_height > $desired_max_height)
	{
		// Work out the aspect ratio, as we need to ensure the image stays in 
		// the correct dimensions
		$aspect_ratio = $old_width / $old_height;
		
		// Use $desired_max_width as the new_width, and change new_height using 
		// the aspect ratio
		$new_width = $desired_max_width;
		$new_height = $new_width / $aspect_ratio;
		
		// Just check that the new height is actually below the max height, if not
		// adjust the sizes again to ensure it is
		if ($new_height > $desired_max_height)
		{
			
			$new_height = $desired_max_height;
			$new_width = $new_height * $aspect_ratio;
		}
	}
		
	// Create new resized canvas
	$image_resized = ImageCreateTrueColor($new_width, $new_height);
	
	// Code to check if this image is PNG or GIF. If so, then set canvas to ransparent.
 	if ($type != 'jpg') 
 	{
		imagealphablending($image_resized, false);
		imagesavealpha($image_resized, true);
		$color_transparent = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
		imagefilledrectangle($image_resized, 0, 0, $new_width, $new_height, $color_transparent);
		
		// Only needed if resizing a gif 
		if ($type == 'gif') {
			imagecolortransparent($image_resized, $color_transparent);
		}
	}
	
	// Copy resized image into new canvas
	imagecopyresampled($image_resized, $imgresource, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);
	
	return $image_resized;
}


/**
 * Gets the thumbnail for the specified website, stores it in the cache, and then returns the 
 * relative path to the cached image.
 * 
 * @param String $url The URL to get the thumbnail for.
 * @param String $size_override If specified, use this size rather than the size in the settings.
 * @param Boolean $capture_internal_page If true, capture an internal page rather than just a home page. Requires the pro feature for Shrink The Web to 'Capture Specific Page'.
 * @return String The full URL for the thumbnail stored in the cache.
 */
function WPPortfolio_getThumbnail($url, $size_override = false, $capture_internal_page = false)
{
	// Create cache directory if it doesn't exist
	WPPortfolio_createCacheDirectory();
	
	// Get all the options from the database for the thumbnail    
    $args["stwaccesskeyid"] = stripslashes(get_option('WPPortfolio_setting_stw_access_key'));
    $args["stwu"] 			= stripslashes(get_option('WPPortfolio_setting_stw_secret_key'));
    $setting_fetch_method   = stripslashes(get_option('WPPortfolio_setting_fetch_method'));
    
    // Allowing internal links?
    if ($capture_internal_page) {
    	$args["stwinside"] = 1;
    }
        
    // If $size_override is specified, then use that size rather than the size stored in the settings.
    if ($size_override) {
    	$args["stwsize"] = $size_override;
    } 
    else {
    	// Do we have a custom size?
		if ($customSize = WPPortfolio_getCustomSizeOption()) {
			$args["stwxmax"] = $customSize;
		}
		
		// No custom size, just do the standard size.
    	else {
    		$args["stwsize"] = stripslashes(get_option('WPPortfolio_setting_stw_thumb_size'));
    	}    	
    }
    
    // Try to grab the thumbnail
    $imagefile = WPPortfolio_getCachedThumbnail($url, 
    								WPPortfolio_getThumbPathActualDir(), 
    								WPPortfolio_getThumbPathURL(),  
    								WPPortfolio_getPendingThumbURLPath(), 
    								$args, 
    								false, 
    								$setting_fetch_method);    
        
    return $imagefile;
}

/**
 * Gets the thumbnail for the specified website, stores it in the cache, and then returns the 
 * HTML for loading the image. This handles the Shrink The Web javascript loader for free basic
 * accounts.
 * 
 * @param $url The URL to get the thumbnail for.
 * @param $size_override If specified, use this size rather than the size in the settings.
 * @param $capture_internal_page If true, capture an internal page rather than just a home page. Requires the pro feature for Shrink The Web to 'Capture Specific Page'.
 * @param $attrib_alt If specified, the alt attribute for the image.
 * @param $attrib_class  If specified, the CSS class for the image.
 * 
 * @return String The HTML to render the thumbnail.
 */
function WPPortfolio_getThumbnailHTML($url, $size_override = false, $capture_internal_page = false, $attrib_alt = false, $attrib_class = false)
{ 
	$imageURL = WPPortfolio_getThumbnail($url, $size_override, $capture_internal_page);
	
	// Add attributes if known
	$tags = false;
	if ($attrib_alt) {
		$tags .= 'alt="'.$attrib_alt.'"';
	}
	
	if ($attrib_class) {
		$tags .= ' class="'.$attrib_class.'"';
	}		
	
	$imagetag = sprintf('<img src="%s" %s/>', $imageURL, $tags);
	
	return $imagetag;
}




/**
 * Get a thumbnail, caching it first if possible.
 * 
 * @param $url The URL to get a thumbnail for.
 * @param $actualThumbPath The actual path on the server to store the cached thumbnail.
 * @param $webFilePath The web URL path for the thumbnails that relates to the actual file path.
 * @param $pendingThumbPath The path for images when a thumbnail cannot be loaded.
 * @param $args The arguments that contain the access ID and size info to get the thumbnail.
 * @param $forceUpdate If true, force the retrieval over the web of the thumbnail.
 * @param $fetchMethod The method used to fetch a thumbnail (either curl or fopen).
 * 
 * @return String The full path of the image to render.
 */
function WPPortfolio_getCachedThumbnail($url, $actualThumbPath, $webFilePath, $pendingThumbPath,
							$args = null, $forceUpdate = false, $fetchMethod = "curl")
{
	// Don't try something if not enough arguements.
	if (!$args || !$url || !$actualThumbPath || !$webFilePath)
		return false;
		
	// Add arguments not yet added
    $args["Service"] = "ShrinkWebUrlThumbnail";
    $args["Action"] = "Thumbnail";		
	$args["stwurl"] = $url; // now url is added to the parameters at the end
		
		
	// Check the error cache for the error rather than do a fresh request.
	// If there's an error, return the cached error rather than wasting a request
	$cachedError = WPPortfolio_errors_checkForCachedError($args, $pendingThumbPath);
	if ($cachedError) { 
		return $cachedError;
	}

	$actualThumbPath = trailingslashit($actualThumbPath);

	$type = strtolower(substr(strrchr($url, '.'), 0));
	
	$args['filetype'] = $type;
		
    // Use arguments to work out the target filename
    $filename = WPPortfolio_generateFilename($url, $args, 'jpg');    
	$filepath = $actualThumbPath . $filename;
	
	// As of V1.19 
	// Move old files if they exist - for people with older style permanent caches 
	$filepathOld = $actualThumbPath . md5($url.serialize($args)).".jpg"; 
	if (file_exists($filepathOld)) {
		rename($filepathOld, $filepath);
	}	
	
	// The URL to return.
	$returnName = false;
	$errorStatus = false;
	
	// Work out if we need to update the cached and resize thumbnail	
	$usingCache = true;
	if ($forceUpdate || WPPortfolio_cacheFileExpired($filepath))
	{
		// Fetch thumbnail, recording any errors.
		$thumbResults = WPPortfolio_checkWebsiteThumbnailCaptured($url, $args, $fetchMethod);
		$errorStatus  = $thumbResults['status']; 
		$thumbURL 	  = $thumbResults['thumbnail'];

		// Try to download the file to local server
		if ($thumbURL) {		
			WPPortfolio_downloadRemoteImageToLocalPath($thumbURL, $filepath, $fetchMethod);
		}
		
		$usingCache = false;
	}		
	 
	// File downloaded successfully
	if (($usingCache || (!$usingCache && $thumbURL)) && file_exists($filepath)) {
		$returnName = "$webFilePath/$filename";
	}
	// Something went wrong, so return an error status image 
	else {
		$returnName = WPPortfolio_error_getErrorStatusImg($args, $pendingThumbPath, $errorStatus);
	}
	
	// Log what happened when retrieving from cache
	if ($usingCache) {
		// Param 1: Requested thumbnail URL
		// Param 2: Type - request or cache
		// Param 3: Did the operation succeed?
		// Param 4: Further Information
		$cacheSuccess = (file_exists($filepath));
		WPPortfolio_debugLogThumbnailRequest($url, 'cache', $cacheSuccess, $filename, $args, ($cacheSuccess ? '' : 'Could not find file in cache.'));
	}
	
	return $returnName;
}


/**
 * Generate the error image filename based on what went wrong.
 * @param Array $args The arguments used to render the thumbnail.
 * @param String $pendingThumbPath The root path for the error thumbnail.
 * @param String $errorStatus The error status message.
 * 
 * @return String Full URL to the thumbnail showing error that occured.
 */
function WPPortfolio_error_getErrorStatusImg($args, $pendingThumbPath, $errorStatus = false)
{
	// What size of error status image to show...
	$size = WPPortfolio_getArrayValue($args, 'stwsize');
	if (!$size) { 
		$size = 'xlg'; 
	}
	
	// What error?
	switch ($errorStatus)
	{
		case 'queued':
		case 'invalid_api_access_details':
		case 'lock_to_account':
		case 'unknown_host':
		case 'upgrade_account':
		case 'upgrade_account_feature':
			$status = $errorStatus;
		break;
		
		// Catch all for errors.
		default: 
			$status = 'other_error';
		break;
	}
	
	return $pendingThumbPath . $status . '_' . $size .".jpg"; 
}

/**
 * Method that checks that the thumbnail for the specified website exists. 
 * @param $url The website to get the thumbnail for.
 * @param $args The arguements used for the HTTP request.
 * @param $fetchMethod The method used to fetch a thumbnail (either curl or fopen). 
 * 
 * @return The URL of the image file from the server if the request was successful, false otherwise.
 */
function WPPortfolio_checkWebsiteThumbnailCaptured($url, $args = null, $fetchMethod = "curl")
{
	$args = is_array($args) ? $args : array();	
	
	// Don't try something if not enough arguements.
	if (!$args || !$url)
		return false;	
			
	$request_url = urldecode("http://images.shrinktheweb.com/xino.php?".http_build_query($args,'','&'));	// avoid &amp;
	
	// Check that the thumbnail exists, from the STW server
	// Use cURL if possible
	if ($fetchMethod == "curl" && function_exists('curl_init')) 
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $request_url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
		$remotedata = curl_exec($ch);
		curl_close($ch);		
	}
	// This may be disabled as a security measure
	else
	{
		$remotedata = file_get_contents($request_url);
	}
	
	// WPPT_debug_showArray(htmlentities($remotedata));
	$resultData = WPPortfolio_xml_processReturnData($remotedata);
	
	// Extract image URL to download - Legacy code
	//$regex = '/<[^:]*:Thumbnail\\s*(?:Exists=\"((?:true)|(?:false))\")?[^>]*>([^<]*)<\//';
	//if (preg_match($regex, $remotedata, $matches) == 1 && $matches[1] == "true") {
	//		$imageURL = $matches[2];
	//}
	
	$imageURL = $resultData['thumbnail'];
	
	
	// Param 1: Requested thumbnail URL
	// Param 2: Type - request or cache
	// Param 3: Did the operation succeed?
	// Param 4: Further Information		
	$detail = sprintf('<span class="wpp-debug-detail-header">Error Summary</span>
					  <span class="wpp-debug-detail-info">%s - %s</span>
					  <span class="wpp-debug-detail-header">Request URL</span>
					  <span class="wpp-debug-detail-info">%s</span>
					  <span class="wpp-debug-detail-header">Raw Response</span>
					  <span class="wpp-debug-detail-info">%s</span>', $resultData['status'], $resultData['msg'], $request_url, $remotedata);
	
	WPPortfolio_debugLogThumbnailRequest($url, 'web', ($imageURL != false), $detail, $args, $resultData['status']);
		
	// Return image URL and all remote data.
	return $resultData;
}


/**
 * Process the XML data from STW, and turn it into meaningful messages that we can return to the use.
 * @param String $data The raw XML data from the XML web service.
 * @return Array The details of the fetch results ([msg] = Raw message returned, [status] = interpreted message, [thumbnail] = thumbnail URL if all is ok. 
 */
function WPPortfolio_xml_processReturnData($data)
{
	if (!$data) {
		$returndata['status'] = 'other_error';
		$returndata['msg'] 	  = 'Data from STW was empty.';
		return $returndata;
	}
	
	$stw_response_status = false;
	
	// SimpleXML loaded in PHP	
	if (extension_loaded('simplexml')) 
	{
		$returndata = array();
		
		// Load XML into DOM object
		$dom = new DOMDocument;
		$dom->loadXML($data);
		$xml = simplexml_import_dom($dom);
		
		// Pull response codes from XML feed
		$stw_response_status	= (string)$xml->children('http://www.shrinktheweb.com/doc/stwresponse.xsd')->Response->ResponseStatus->StatusCode; // HTTP Response Code
		$thumbnail				= (string)$xml->children('http://www.shrinktheweb.com/doc/stwresponse.xsd')->Response->ThumbnailResult->Thumbnail[0]; // Image Location (alt method)
		$stw_action				= (string)$xml->children('http://www.shrinktheweb.com/doc/stwresponse.xsd')->Response->ThumbnailResult->Thumbnail[1]; // ACTION
		
	} // endif if (extension_loaded('simplexml'))

	// SimpleXML not loaded in PHP.
	else 
	{
		// Check for thumbnail  - old method
		/*
		if (preg_match('/<[^:]*:Thumbnail\\s*(?:Exists=\"((?:true)|(?:false))\")?[^>]*>([^<]*)<\//', $data, $matches)) {
			$thumbnail = $matches[2];
			$stw_action = $matches[1];
		}*/
		
		// Extract thumbnail
        if (preg_match('/<[^:]*:ThumbnailResult?[^>]*>[^<]*<[^:]*:Thumbnail\s*(?:Exists=\"((?:true)|(?:false))\")+[^>]*>([^<]*)<\//', $data, $matches)) {
            $thumbnail = $matches[2];
        }
        
        // Get action from <stw:Thumbnail Verified="true">delivered</stw:Thumbnail>
		if (preg_match('/<[^:]*:Thumbnail\s*? Verified=[^>]*?>([^<]*)<\//', $data, $matches)) {
            $stw_action = $matches[1];
        }
		
		// Check for response code.
		if (preg_match('/<[^:]*:ResponseStatus>[^:]*:StatusCode>([^>]*)<\/[^:]*:StatusCode>[^:]*:ResponseStatus>/', $data, $matches)) {
			$stw_response_status = $matches[1];
		}
	}
	
	//print_r($stw_action);
	//echo '<br/>';
	//print_r($stw_response_status);
	
	// ### Format data for returning
	$returndata['thumbnail'] = false;; 
	
	// Thumbnail loaded fine
	if ($stw_action == 'delivered') 
	{
		$returndata['status'] 	= 'success';	
		$returndata['msg'] 		= 'Thumbnail delivered successfully.';
		$returndata['thumbnail'] = $thumbnail;
	}
	
	// Thumbnail queued
	else if ($stw_response_status == 'Success' && ($stw_action == 'noexist' || $stw_action == 'queued'))
	{
		$returndata['msg'] = 'Thumbnail queued for update.';
		$returndata['status'] 	= 'queued';	
	}
	
	// Need to store information on what went wrong.
	else 
	{
		$returndata['msg'] = $stw_response_status;
				
		// Invalid API details
		if (strpos($stw_response_status, 'Invalid Credentials') !== FALSE) {
			$returndata['status'] = 'invalid_api_access_details';	
		}
		
		// Need IP or host locking
		else if (strpos($stw_response_status, 'Lock to account') !== FALSE) {
			$returndata['status'] = 'lock_to_account';
		}
		
		// Unknown site
		else if (strpos($stw_response_status, 'NS_ERROR_UNKNOWN_HOST') !== FALSE) {				
			$returndata['status'] = 'unknown_host';	
		}
		
		// Need to upgrade to Basic/Plus
		else if (strpos($stw_response_status, 'Upgrade account') !== FALSE) { 
			$returndata['status'] = 'upgrade_account';	
		}
		
		// Need feature update - e.g. inside thumbs
		else if (strpos($stw_response_status, 'Upgrade to use this feature') !== FALSE) { 
			$returndata['status'] = 'upgrade_account_feature';	
		}		
		
		else {
			$returndata['status'] = 'other_error';
		}
	}// end if else thumbnail	
	
	return $returndata;
}


/**
 * Method to get image at the specified remote URL and attempt to save it to the specifed local path.
 * @param $remoteURL The URL of the remote image to download.
 * @param $localPath The path to use to store the image locally.
 * @param $fetchMethod The method used to fetch a thumbnail (either curl or fopen). 
 */
function WPPortfolio_downloadRemoteImageToLocalPath($remoteURL, $localPath, $fetchMethod = "curl")
{
	
	if ($fetchMethod == "curl" && function_exists('curl_init'))
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $remoteURL);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout in 10 seconds		
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
		$imagedata = curl_exec($ch);		
	}
	else {		
		$imagedata = file_get_contents($remoteURL);
	}

	
	// Only save data if we managed to get the file contents
	if ($imagedata) {
		$localFileHandle = fopen($localPath, "w+");
		fputs($localFileHandle, $imagedata);
		fclose($localFileHandle);
	} else {	
		// Try to delete file if download failed.
		if (file_exists($localPath)) {	
			@unlink($localPath);
		}
	}
}

/**
 * Logs a thumbnail request to the debug log.
 * @param $url The URL being requested
 * @param $requestType The type of request, namely cache or request.
 * @param $requestSuccess If true, the event succeeded.
 * @param $detail Any additional debug information.
 * @param $args The arguments used to fetch the thumbnail.
 * @param $errorMessage The error message if there was one.
 */
function WPPortfolio_debugLogThumbnailRequest($url, $requestType, $requestSuccess, $detail, $args, $errorMessage)
{
	// Escape if debug logging not enabled
	if ($requestSuccess && get_option('WPPortfolio_setting_enable_debug') != 'on') {
		return false;
	}
	
	global $wpdb;
	$table_debug = $wpdb->prefix . TABLE_WEBSITE_DEBUG;
	
	$data = array();
	$data['request_url'] 		= $url;
	$data['request_type'] 		= $requestType;
	$data['request_result'] 	= ($requestSuccess ? 1 : 0);
	$data['request_detail'] 	= $detail;
	$data['request_date'] 		= date( 'Y-m-d H:i:s');
	$data['request_param_hash'] = md5(serialize($args));	
	$data['request_error_msg']  = $errorMessage;
	 	
	
	$SQL = arrayToSQLInsert($table_debug, $data);
	$wpdb->query($SQL);
}


?>