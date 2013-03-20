<?php

/**
 * Get the details for the specified group ID.
 * @param $groupid The ID of the group to get the details for.
 * @return Array An array of the group details.
 */
function WPPortfolio_getGroupDetails($groupid) 
{
	global $wpdb;
	$table_name = $wpdb->prefix . TABLE_WEBSITE_GROUPS;
	
	$SQL = "SELECT * FROM $table_name 
			WHERE groupid = '".$wpdb->escape($groupid)."' LIMIT 1";
	
	// We need to strip slashes for each entry.
	return WPPortfolio_cleanSlashesFromArrayData($wpdb->get_row($SQL, ARRAY_A));
}


/**
 * Get a list of the groups used in the portfolio.
 * @return Array A list of the groups in the portfolio.
 */
function WPPortfolio_getList_groups()
{
	    global $wpdb;
	    $groups_table = $wpdb->prefix . TABLE_WEBSITE_GROUPS;
	    
	    
        $SQL = "SELECT * FROM $groups_table
	 			ORDER BY groupname";	
	
		$wpdb->show_errors();
		$groups = $wpdb->get_results($SQL, OBJECT);
		return $groups;
}


/**
 * Determine if the specified key is valid, i.e. containing only letters and numbers.
 * @param $key The key to check
 * @return Boolean True if the key is valid, false otherwise.
 */
function WPPortfolio_isValidKey($key) 
{
	// Ensure the key only contains letters and numbers
	return preg_match('/^[a-z0-9A-Z]+$/', $key);
}

/**
 * Recursively delete a directory
 *
 * @param string $dir Directory name
 * @param boolean $deleteRootToo Delete specified top-level directory as well
 */
function WPPortfolio_unlinkRecursive($dir, $deleteRootToo)
{
    if(!$dh = @opendir($dir)) {
        return;
    }
    while (false !== ($obj = readdir($dh)))
    {
        if($obj == '.' || $obj == '..') {
            continue;
        }

        if (!@unlink($dir . '/' . $obj)) {
            WPPortfolio_unlinkRecursive($dir.'/'.$obj, true);
        }
    }

    closedir($dh);
   
    if ($deleteRootToo) {
        @rmdir($dir);
    }
   
    return;
} 


/**
 * A recursive function to copy all subdirectories and their contents.
 * @param $src The source directory
 * @param $dst The target directory
 */ 
function WPPortfolio_fileCopyRecursive($src, $dst)
{
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ( $file = readdir($dir)) )
    {
        if (( $file != '.' ) && ( $file != '..' )) 
        {
            if ( is_dir($src . '/' . $file) ) {
                recurse_copy($src . '/' . $file,$dst . '/' . $file);
            }
            else {
                copy($src . '/' . $file,$dst . '/' . $file);
            }
        }
    } // end while
    closedir($dir);
} 


/**
 * Replace all occurances of the search string with the replacement. Uses alternative for str_ireplace if not available.
 * @param $searchstr The string to search for.
 * @param $replacestr The string to replace the search string with.
 * @param $haystack The string to search.
 * @return String The text with the replaced string.
 */
function WPPortfolio_replaceString($searchstr, $replacestr, $haystack) {
	
	// Faster, but in PHP5.
	if (function_exists("str_ireplace")) {
		return str_ireplace($searchstr, $replacestr, $haystack);
	}
	// Slower but handles PHP4
	else { 
		return preg_replace("/$searchstr/i", $replacestr, $haystack);
	}
}


/**
 * Remove all slashes from all fields from data retrieved from the database.
 * @param $data The data array from the database.
 * @return Array The cleaned array.
 */
function WPPortfolio_cleanSlashesFromArrayData($data)
{
	if (count($data) > 0) {
		foreach ($data as $datakey => $datavalue) {
			$data[$datakey] = stripslashes($datavalue);
		}
	}
	
	return $data;
}

/**
 * Safe method to get the value from an array using the specified key.
 * @param $array The array to search.
 * @param $key The key to use to index the array.
 * @param $returnSpace If true, return a space if there's nothing in the array.
 * @return String The array value.
 */
function WPPortfolio_getArrayValue($array, $key, $returnSpace = false)
{
	if ($array && isset($array[$key])) {
		return $array[$key];
	}
	
	// If returnSpace is true, then return a space rather than nothing at all.
	if ($returnSpace) {
		return '&nbsp;';
	} else {
		return false;
	}
}



?>