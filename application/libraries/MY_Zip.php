<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Zip Compression Class
 *
 * This class is based on a library I found at Zend:
 * http://www.zend.com/codex.php?id=696&single=1
 *
 * The original library is a little rough around the edges so I
 * refactored it and added several additional methods -- Rick Ellis
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Encryption
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/zip.html
 */
class MY_Zip  extends CI_Zip{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Download
	 *
	 * @access	public
	 * @param	string	the file name
	 * @param	string	the data to be encoded
	 * @return	bool
	 */
	function download($filename = 'backup.zip')
	{
		if (preg_match("|.+?\.epub|", $filename))
		{
            $CI =& get_instance();
            $CI->load->helper('download');

            $get_zip = $this->get_zip();

            $zip_content =& $get_zip;

            force_download($filename, $zip_content);
		}else{
            parent::download($filename);
        }
	}

    /**
     * Read a directory and add it to the zip.
     *
     * This function recursively reads a folder and everything it contains (including
     * sub-folders) and creates a zip based on it.  Whatever directory structure
     * is in the original file path will be recreated in the zip file.
     *
     * @access	public
     * @param	string	path to source
     * @return	bool
     */
    function read_dir($path, $preserve_filepath = TRUE, $root_path = NULL, $found=FALSE, $level=0)
    {
        if ( ! $fp = @opendir($path))
        {
            return FALSE;
        }

        // Set the original directory root for child dir's to use as relative
        if ($root_path === NULL)
        {
            $root_path = dirname($path).'/';
        }

        while (FALSE !== ($file = readdir($fp)))
        {
            if (substr($file, 0, 1) == '.')
            {
                continue;
            }

            if (@is_dir($path.$file)&&$found)
            {
                $this->read_dir($path.$file."/", $preserve_filepath, $root_path, $found, $level+1);
            }
            else
            {
                if (FALSE !== ($data = @file_get_contents($path.$file)))
                {
                    $name = str_replace("\\", "/", $path);

                    if ($preserve_filepath === FALSE)
                    {
                        $name = str_replace($root_path, '', $name);
                    }

                    if($file=='mimetype' && !$found){
                        $this->add_data($name.$file, $data);
                        rewinddir($fp);
                        $found=true;
                    }else if(($file!='mimetype' && $found) || $level>0){
                        $this->add_data($name.$file, $data);
                    }
                }
            }
        }

        return TRUE;
    }

}