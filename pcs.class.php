<?php
/***************************************************************************
 * 
 * @copyright (c) 2012 Baidu.com, Inc. All Rights Reserved
 *
 * @author yangwei01 yangwei01@baidu.com
 * 
 * @date 2012.02.20
 * 
 * @version 1.1
 *
***************************************************************************/

if (!defined('PCS_API_PATH'))
    define('PCS_API_PATH', dirname(__FILE__));
require_once(PCS_API_PATH . '/libs/requestcore/requestcore.class.php');

/**
 * Default PCS Exception
 */
class PCSException extends Exception {
}

/**
 * Main PCS Class
 */
class BaiduPCS {
    /* default hostname for pcs server */
    const default_hostname = 'pcs.baidu.com';
    /* default entrance for pcs server */
    const default_entrance = '/rest/2.0/pcs/';

    /* request type of user account operation */
    const account_type = 'account';
    /* request type of app quota operation */
    const quota_type = 'quota';
    /* request type of file tag operation  */
    const tag_type = 'tag';
    /* request type of normal file operation */
    const file_type_normal = 'file';
    /* request type of big file operation */
    const file_type_super = 'superfile';
    /* request type of file share operation */
    const share_type = 'share';
    /* request type of event callback operation */
    const callback_type = 'inotify';
    
    /* http request method */
    const http_get = 'GET';
    const http_put = 'PUT';
    const http_post = 'POST';
    const http_delete = 'DELETE';
    
    /* max file size : 2G */
    const max_file_size = 2147483648;

    /* user authorize using access-token */
    private $host_name = '';
    private $access_token = '';
    private $cookie = '';
    private $app_id = 0;
    /* return error */
    private $error_code = 0;
    private $error_message = '';
    /* setting */
    private $debug_mode = false;
    private $use_ssl = false;

    /**
     * construct function
     * 
     * @param array $auth Authorization type, such as token/cookie/user_id
     * @return (none)
     */
    public function __construct($auth) {
        /* access-token or cookie must be needed */
        if ((!isset($auth['access_token']) || empty($auth['access_token'])) &&
        	(!isset($auth['cookie']) || empty($auth['cookie']))) {
            throw new PCSException('miss access token or cookie in ' . __FUNCTION__);
        }
        
        if (isset($auth['access_token'])) {
        	$this->access_token = $auth['access_token'];
        } else if (isset($auth['cookie'])) {
        	if (!isset($auth['app_id']) || empty($auth['app_id']) || 0 === $auth['app_id']) {
        		throw new PCSException('miss app id with cookie in ' . __FUNCTION__);
        	}
        	$this->cookie = $auth['cookie'];
        	$this->app_id = $auth['app_id'];
        }

        /* host name can't be changed */
        $this->host_name = self::default_hostname;
    }

    /**
     * generate url, and send head/get/put/post/delete request to pcs server
     * 
     * @param string $requesttype	PCS request type, such as file, inotify, share etc.
     * @param string $method		Http method, get/put/post/delete
     * @param array  $opt			PCS request argument, including 'url_opts' and 'req_opts'
     * @return array				Http response
     */
    private function authenticate($requesttype, $method, $opt = NULL) {
        /* generate full request url, including hostname and querystring */
        $url_opts = array (
            'request_type' => $requesttype, 
        );
        if (is_array($opt) && isset($opt['url_opts'])) {
            $url_opts = array_merge_recursive($url_opts, $opt['url_opts']);
        }
        $url = $this->format_url($url_opts);

        /* set request option, including method, header and body */
        $req_opts = array (
            'method' => $method,
        );
    	if (!empty($this->cookie)){
			$req_opts['header']['cookie'] = $this->cookie;
		}
        if (is_array($opt) && isset($opt["req_opts"])) {
            $req_opts = array_merge_recursive($req_opts, $opt["req_opts"]);
        }        
        $request = new RequestCore($url);
        $this->set_request($request, $req_opts);

        /* send request */
        $request->send_request();
        $response = new ResponseCore($request->get_response_header(), $request->get_response_body(), $request->get_response_code());

        /* handle general error */
        $json_body = rawurldecode($response->body);
        $arr_body = json_decode($json_body, true);
        if (is_array($arr_body)) {
        	if (isset($arr_body['error_code'])) {
	        	$this->error_code = $arr_body['error_code'];
	        	$this->error_message = $arr_body['error_msg'];
	        	return false;
        	}
        	return $arr_body;
    	} else {
    		$this->error_code = 0;
	        $this->error_message = '';
    		return $response->body;
    	}
    }

    /**
     * generate http url address
     * 
     * @param array $opt URL argument
     * @return string URL address
     */
    private function format_url($opt) {
        if (!isset($opt['request_type'])) {
            throw new PCSException('miss pcs request type in ' . __FUNCTION__);
        }

        if ((self::account_type != $opt['request_type']) && 
            (self::quota_type != $opt['request_type']) && 
            (self::tag_type != $opt['request_type']) &&
            (self::file_type_normal != $opt['request_type']) && 
            (self::file_type_super != $opt['request_type']) && 
            (self::share_type != $opt['request_type']) && 
            (self::callback_type) != $opt['request_type']) {
            throw new PCSException('invalid pcs request type in ' . __FUNCTION__);
        }

        $url = "";
       	$url .= $this->use_ssl ? 'https://' : 'http://';
       	$url .= $this->host_name;
        $url .= self::default_entrance;
        $url .= $opt['request_type'];

        if (!empty($this->access_token)) {
        	$opt['query_string']['access_token'] = $this->access_token;
        } else if (0 !== $this->app_id) {
	       	$opt['query_string']['app_id'] = $this->app_id;
        }
        if (isset($opt ['query_string'])) {
            $url .= '?';

            /* append query string in the end of request url */
            $query_string = "";
            if(is_array($opt['query_string'])) {
                foreach ( $opt ['query_string'] as $key => $value ) {
                    $query_string .= '&' . $key . '=' . rawurlencode($value);
                }
            } else {
                $query_string .= '&' . $opt['query_string'];
            }
            /* remove the first '&' */
            $query_string = substr($query_string, 1, strlen($query_string) - 1);
            
            $url .= $query_string;
        }

        return $url;
    }

    /**
     * set request option
     * 
     * @param object $request Http Request
     * @param array  $opt Request argument
     * @return (none)
     */
    private function set_request($request, $opt) {
        /* method */
        if (!isset($opt['method'])) {
            throw new PCSException('miss request method in ' . __FUNCTION__);
        }
        $method = $opt['method'];
        $request->set_method($method);

        /* header */
        $headers = array();
        if (isset($opt['header'])) {
            if (!is_array($opt['header'])) {
                throw new PCSException('parameter header is not a array in ' . __FUNCTION__);
            }
            $headers = $opt['header'];
        }
        foreach($headers as $key => $value) {
            $request->add_header($key, $value);
        }

        /* body */
        /* 1) content */
        if (isset($opt['body'])) {
            $request->set_body($opt['body']);
        }

        /* 2) upload file */
        if (isset($opt['upload_file'])) {
            if (!file_exists($opt['upload_file'])) {
                throw new PCSException('the file does not exist : ' . $opt['upload_file'] . ' in ' . __FUNCTION__);
            }
            $request->set_read_file($opt['upload_file']);
        }

        /* 3) follow */
        if (isset($opt['follow']) && $opt['follow'] == true) {
            $request->follow = true;
        } 

        /* 4) write file */
        if (isset($opt['write_file'])) {
            $request->set_write_file($opt['write_file']);
        }

        /* 5) debug mode */
        $request->debug_mode = $this->debug_mode;
    }
    
    /**
     * check path, must not empty and use '/' as prefix character
     * 
     * @param string $path File or directory path
     * @return bool If failed, return false; otherwise return true
     */
    private function check_path($path) {
        if (isset($path) && !empty($path) && '/' == substr($path, 0, 1)) {
    		return true;
    	} else {
    		return false;
    	}
    }
    
    /**
     * clear error code and error mesage
     * 
     * @param (none)
     * @return (none)
     */
    private function clear_error() {
    	$this->error_code = 0;
    	$this->error_message = '';
    }

    /**
     * get error code
     * 
     * @param (none)
     * @return int Error Code
     */
    public function get_error_code() {
        return $this->error_code;
    }

    /**
     * get error message
     * 
     * @param (none)
     * @return string Error Message
     */
    public function get_error_message() {
        return $this->error_message;
    }

    /**
     * set debug mode
     * 
     * @param bool $debugmode If true, start debug mode; otherwise stop debug mode
     * @return (none)
     */
    public function set_debug_mode($debugmode) {
    	$this->clear_error();
        $this->debug_mode = $debugmode;
    }

    /**
     * set https protocol
     * 
     * @param bool $ssl If true, use https protocol; otherwise use http protocol
     * @return (none)
     */
    public function set_ssl($ssl) {
    	$this->clear_error();
        $this->use_ssl = $ssl;
    }

    /**
     * get app space quota
     * 
     * @param (none)
     * @return array If failed, return false; otherwise return array
     */
    public function info_quota() {
    	$this->clear_error();
    	$requesttype = self::quota_type;
    	$method = self::http_get;

    	$opt['url_opts']['query_string']['method'] = 'info';
    	
    	try {
    		$ret = $this->authenticate($requesttype, $method, $opt);
    		unset($ret['request_id']);
    		return $ret;
    	} catch (Exception $ex) {
    		$this->error_code = -1;
        	$this->error_message = $ex->getMessage();
        	return false;
    	}
    }
    
    /**
     * create directory with the specific path
     * 
     * @param string $dir Directory path
     * @return array If failed, return false; otherwise return array
     */
    public function create_dir($dir) {
    	$this->clear_error();
    	$requesttype = self::file_type_normal;
    	$method = self::http_post;
    	
    	if (!$this->check_path($dir)) {
    		$this->error_code = -1;
    		$this->error_message = 'invalid directory : ' . $dir . ' in ' . __function__;
    		return false;
    	}
    	
    	$opt['url_opts']['query_string']['method'] = 'mkdir';
    	$opt['url_opts']['query_string']['path'] = $dir;
    	
        try {
    		$ret = $this->authenticate($requesttype, $method, $opt);
    		unset($ret['request_id']);
    		return $ret;
    	} catch (Exception $ex) {
    		$this->error_code = -1;
        	$this->error_message = $ex->getMessage();
        	return false;
    	}
    }

    /**
     * upload data to pcs server
     * 
     * @param string $file File absolute location path or file content
     * @param bool   $flag If false, is file path; otherwise is file content
     * @param string $dir Target directory
     * @param string $filename New file name, default as orginal file name
     * @return array If failed, return false; otherwise return array
     */
    private function upload_data($file, $flag, $dir, $filename=NULL) {
    	$this->clear_error();
    	$requesttype = self::file_type_normal;
    	
    	if ((!isset($file) || empty($file)) ||
    		(!$this->check_path($dir))) {
    		$this->error_code = -1;
    		$this->error_message = 'parameter error in ' . __function__;
    		return false;
    	}
    	if ($flag) {
    		if (!isset($filename)) {
				$this->error_code = -1;
	    		$this->error_message = 'miss new file name in ' . __function__;
	    		return false;
    		}
    		if (self::max_file_size < strlen($file)) {
				$this->error_code = -1;
    			$this->error_message = 'the file size must be less than 2GB : fd[' . $file . '] in ' . __function__;
    			return false;
    		}
    		$method = self::http_put;
    		$opt['req_opts']['body'] = $file;
    	} else {
	    	if (!file_exists($file)) {
	    		$this->error_code = -1;
	    		$this->error_message = 'the file does not exist : ' . $file . ' in ' . __function__;
	    		return false;
	    	}
	    	if (self::max_file_size < filesize($file)) {
				$this->error_code = -1;
	    		$this->error_message = 'the file size must be less than 2GB : ' . $file . ' in ' . __function__;
	    		return false;
	    	}
	    	$method = self::http_post;
	    	$opt['req_opts']['body']['file'] = "@$file";
    	}
    	
    	$opt['url_opts']['query_string']['method'] = 'upload';
    	$opt['url_opts']['query_string']['dir'] = $dir;
		if (isset($filename)) {
			if (empty($filename)) {
				$this->error_code = -1;
    			$this->error_message = 'new file name is empty in ' . __function__;
    			return false;
			}
			$opt['url_opts']['query_string']['filename'] = $filename;
		} else {
			$opt['url_opts']['query_string']['filename'] = basename($file);
		}
		
        try {
    		$ret = $this->authenticate($requesttype, $method, $opt);
    		unset($ret['request_id']);
    		return $ret;
    	} catch (Exception $ex) {
    		$this->error_code = -1;
        	$this->error_message = $ex->getMessage();
        	return false;
    	}
    }
    
    /**
     * upload local file to pcs server
     * 
     * @param string $file File absolute location path
     * @param string $dir Target directory
     * @param string $filename New file name, default as orginal file name
     * @return array If failed, return false; otherwise return array
     */
    public function upload_file($file, $dir, $filename=NULL) {
   		return $this->upload_data($file, false, $dir, $filename);
    }
    
    /**
     * upload string to pcs server
     * 
     * @param string $file File content
     * @param string $dir Target directory
     * @param string $filename New file name
     * @return array If failed, return false; otherwise return array
     */
    public function upload_file_by_content($file, $dir, $filename) {
    	return $this->upload_data($file, true, $dir, $filename);
    }    

    /**
     * download file to localhost from pcs server
     * 
     * @param string $path The file path in pcs server
     * @param string $dir The directory in localhost, default as return file content
     * @param string $filename New file name, default as orginal file name
     * @return bool If failed, return false; otherwise return true
     */
    public function download_file($path, $dir=NULL, $filename=NULL) {
    	$this->clear_error();
        $requesttype = self::file_type_normal;
    	$method = self::http_get;
    	
    	if (!$this->check_path($path)) {
    		$this->error_code = -1;
    		$this->error_message = 'invalid path : ' . $path . ' in ' . __function__;
    		return false;
    	}
    	
    	if ((isset($dir)) && (!file_exists($dir) || !is_dir($dir))) {
    		$this->error_code = -1;
    		$this->error_message = 'invalid directory : ' . $dir . ' in ' . __function__;
    		return false;
    	}
    	
    	$opt['url_opts']['query_string']['method'] = 'download';
    	$opt['url_opts']['query_string']['path'] = $path;
    	
        try {
    		$data = $this->authenticate($requesttype, $method, $opt);
    		/* filter situation : if file is blank, return false, too */
    		if (!$data && 0 != $this->error_code) {
				return false;
    		}
    		if (!isset($dir)) {
    			return $data;
    		}
    		$savefile = $dir;
    		if ((strpos($dir, '/') !== strlen($dir) - 1) && (strpos($dir, '\\') !== strlen($dir) - 1)) {
    			$savefile .= '/';
    		}
    		if (!isset($filename)) {
    			$savefile .= basename($path);
    		} else {
    			$savefile .= $filename;
    		}
    		
    		$handle = null;
    		if (!$handle = fopen($savefile, 'w')) {
	    		$this->error_code = -1;
	    		$this->error_message = 'unable to create file '. $savefile . ' in ' . __function__;
	    		return false;
    		}
    		fwrite($handle, $data);
    		fclose($handle);
    		return true;
    	} catch (Exception $ex) {
    		$this->error_code = -1;
        	$this->error_message = $ex->getMessage();
        	return false;
    	}    	
    }
    
    /**
     * search file by keyword in directory
     * 
     * @param string $dir Directory path
     * @param string $keyword Keyword in file name
     * @param int    $re If 0, not recursively search; otherwise recursively search, default as 0
     * @return array If failed, return false; otherwise return array
     */
    public function search_file($dir, $keyword, $re=NULL) {
    	$this->clear_error();
    	$requesttype = self::file_type_normal;
    	$method = self::http_get;

        if ((!$this->check_path($dir)) ||
        	(!isset($keyword) || empty($keyword))) {
    		$this->error_code = -1;
    		$this->error_message = 'parameter error in ' . __function__;
    		return false;
    	}

    	$opt['url_opts']['query_string']['method'] = 'search';
    	$opt['url_opts']['query_string']['dir'] = $dir;
    	$opt['url_opts']['query_string']['wd'] = $keyword;
    	if (isset($re)) {
    		if ((0 != $re) && (1 != $re)) {
    			$this->error_code = -1;
    			$this->error_message = 'the third parameter is not 0 or 1 in ' . __function__;
    			return false;
    		}
    		$opt['url_opts']['query_string']['re'] = $re;
    	}
    	
        try {
    		$ret = $this->authenticate($requesttype, $method, $opt);
    		if (false === $ret) {
    			return false;
    		} else {
    			return $ret['list'];
    		}
    	} catch (Exception $ex) {
    		$this->error_code = -1;
        	$this->error_message = $ex->getMessage();
        	return false;
    	}
    }

    /**
     * list all of files and subdirectories in a directory
     * 
     * @param string $dir Directory path, default as app root
     * @param string $by File or directory property, default as file or directory type
     * @param string $order Desc or asc, default as desc
     * @param string $limit Format as 'n1-n2', select records from n1 to n2, default as all records
     * @return array If failed, return false; otherwise return array
     */
    public function list_file($dir, $by=NULL, $order=NULL, $limit=NULL) {
    	$this->clear_error();
    	$requesttype = self::file_type_normal;
    	$method = self::http_get;

    	$opt['url_opts']['query_string']['method'] = 'list';
   		if (!$this->check_path($dir)) {
			$this->error_code = -1;
   			$this->error_message = 'invalid directory : ' . $dir . ' in ' . __function__;
   			return false;
		}
   		$opt['url_opts']['query_string']['dir'] = $dir;
    	if (isset($by)) {
    		if (('time' != $by) && ('name' != $by) && ('size' != $by)) {
    			$this->error_code = -1;
    			$this->error_message = 'the second parameter is must be "time", "name" or "size" in ' . __function__;
    			return false;
    		}
    		$opt['url_opts']['query_string']['by'] = $by;
    	}
    	if (isset($order)) {
    	    if (('desc' != $order) && ('asc' != $order)) {
    			$this->error_code = -1;
    			$this->error_message = 'the third parameter must be "desc" or "asc" in ' . __function__;
    			return false;
    		}
    		$opt['url_opts']['query_string']['order'] = $order;
    	}
    	if (isset($limit)) {
    		if (!strpos($limit, '-') || strpos($limit, '-') === 0 || strpos($limit, '-') === strlen($limit) - 1) {
    			$this->error_code = -1;
    			$this->error_message = 'the fourth parameter must be "number1-number2" in ' . __function__;
    			return false;
    		}
    		$opt['url_opts']['query_string']['limit'] = $limit;
    	}
    	
    	try {
    		$ret = $this->authenticate($requesttype, $method, $opt);
    		if (false === $ret) {
    			return false;
    		} else {
    			return $ret['list'];
    		}
    	} catch (Exception $ex) {
    		$this->error_code = -1;
        	$this->error_message = $ex->getMessage();
        	return false;
    	}
    }

    /**
     * delete file or directory in paths
     * 
     * @param array $paths File or directory paths. If only one path, it can be a string 
     * @return bool If failed, return false; otherwise return true
     */
    public function delete_file($paths) {
    	$this->clear_error();
    	$requesttype = self::file_type_normal;
    	$method = self::http_post;
    	
        if (!isset($paths) || empty($paths)) {
    		$this->error_code = -1;
    		$this->error_message = 'parameter error in ' . __function__;
    		return false;
    	}

    	$opt['url_opts']['query_string']['method'] = 'delete';
     	$list_path = array (
    		'list' => array (
    		),
    	);
    	if (is_array($paths)) {
    		for ($i = 0; $i < count($paths); $i++) {
    			if (!$this->check_path($paths[$i])) {
					$this->error_code = -1;
	    			$this->error_message = 'invalid path : ' . $paths[$i] . ' in ' . __function__;
	    			return false;
    			}
    			$list_path['list'][] = array (
    			 	'path' => $paths[$i],
    			);
    		}
    	} else {
    	    if (!$this->check_path($paths)) {
				$this->error_code = -1;
	    		$this->error_message = 'invalid path : ' . $paths . ' in ' . __function__;
	    		return false;
    		}
    		$list_path['list'][] = array (
    			'path' => $paths,
    		);
    	}
    	$opt['req_opts']['body']['param'] = json_encode($list_path);
    	
        try {
    		if (false === $this->authenticate($requesttype, $method, $opt)) {
    			return false;
    		} else {
    			return true;
    		}
    	} catch (Exception $ex) {
    		$this->error_code = -1;
        	$this->error_message = $ex->getMessage();
        	return false;
    	}
    }

    /**
     * get meta data of file or directory in paths
     * 
     * @param array $paths File or directory paths. If only one path, it can be a string
     * @return array If failed return false; otherwise return array
     */
    public function meta_file($paths) {
    	$this->clear_error();
    	$requesttype = self::file_type_normal;
    	$method = self::http_post;
    	
        if (!isset($paths) || empty($paths)) {
    		$this->error_code = -1;
    		$this->error_message = 'parameter error in ' . __function__;
    		return false;
    	}

    	$opt['url_opts']['query_string']['method'] = 'meta';
    	$list_path = array (
    		'list' => array (
    		),
    	);
    	if (is_array($paths)) {
    		for ($i = 0; $i < count($paths); $i++) {
    		    if (!$this->check_path($paths[$i])) {
					$this->error_code = -1;
		    		$this->error_message = 'invalid path : ' . $paths[$i] . ' in ' . __function__;
		    		return false;
    			}    			
    			$list_path['list'][] = array (
    			 	'path' => $paths[$i],
    			);
    		}
    	} else {
    	   	if (!$this->check_path($paths)) {
				$this->error_code = -1;
	    		$this->error_message = 'invalid path : ' . $paths . ' in ' . __function__;
	    		return false;
    		}
    		$list_path['list'][] = array (
    			'path' => $paths,
    		);
    	}
    	$opt['req_opts']['body']['param'] = json_encode($list_path);
    	
        try {
            $ret = $this->authenticate($requesttype, $method, $opt);
    		if (false === $ret) {
    			return false;
    		} else {
    			return $ret['list'];
    		}
    	} catch (Exception $ex) {
    		$this->error_code = -1;
        	$this->error_message = $ex->getMessage();
        	return false;
    	}
    }

    /**
     * move file or directory from one path to another path
     * 
     * @param array $paths, File or directory source/destination path pairs
     * @return bool If failed, return false; otherwise return true
     */
    public function move_file($paths) {
    	$this->clear_error();
        $requesttype = self::file_type_normal;
    	$method = self::http_post;

    	if (!isset($paths) || empty($paths) || !is_array($paths)) {
    		$this->error_code = -1;
    		$this->error_message = 'parameter error in ' . __function__;
    		return false;
    	}

    	$opt['url_opts']['query_string']['method'] = 'move';
    	$list_path_pair = array (
    		'list' => array (
    		),
    	);
    	if (!isset($paths['from'])) {
    		for ($i = 0; $i < count($paths); $i++) {
	    	   	if (!$this->check_path($paths[$i]['from'])) {
					$this->error_code = -1;
		    		$this->error_message = 'invalid source path : ' . $paths[$i]['from'] . ' in ' . __function__;
		    		return false;
	    		}
    			if (!$this->check_path($paths[$i]['to'])) {
					$this->error_code = -1;
		    		$this->error_message = 'invalid destination path : ' . $paths[$i]['to'] . ' in ' . __function__;
		    		return false;
	    		}
    			$list_path_pair['list'][] = array (
    			 	'from' => $paths[$i]['from'],
    				'to' => $paths[$i]['to'],
    			);
    		}
    	} else {
    		if (!$this->check_path($paths['from'])) {
				$this->error_code = -1;
		    	$this->error_message = 'invalid source path : ' . $paths['from'] . ' in ' . __function__;
		    	return false;
	    	}
    		if (!$this->check_path($paths['to'])) {
				$this->error_code = -1;
		    	$this->error_message = 'invalid destination path : ' . $paths['to'] . ' in ' . __function__;
		    	return false;
	    	}
    		$list_path_pair['list'][] = array (
    			'from' => $paths['from'],
    			'to' => $paths['to'],
    		);
    	}
    	$opt['req_opts']['body']['param'] = json_encode($list_path_pair);

        try {
    		if (false === $this->authenticate($requesttype, $method, $opt)) {
    			return false;
    		} else {
    			return true;
    		}
    	} catch (Exception $ex) {
    		$this->error_code = -1;
        	$this->error_message = $ex->getMessage();
        	return false;
    	}    	
    }

    /**
     * copy file or directory from one path to another path
     * 
     * @param array $paths File or directory source/destination path pairs
     * @return bool If failed, return false; otherwise return true
     */
    public function copy_file($paths) {
    	$this->clear_error();
    	$requesttype = self::file_type_normal;
    	$method = self::http_post;

    	if (!isset($paths) || empty($paths) || !is_array($paths)) {
    		$this->error_code = -1;
    		$this->error_message = 'parameter error in ' . __function__;
    		return false;
    	}

    	$opt['url_opts']['query_string']['method'] = 'copy';
    	$list_path_pair = array (
    		'list' => array (
    		),
    	);
    	if (!isset($paths['from'])) {
    		for ($i = 0; $i < count($paths); $i++) {
    			if (!$this->check_path($paths[$i]['from'])) {
					$this->error_code = -1;
		    		$this->error_message = 'invalid source path : ' . $paths[$i]['from'] . ' in ' . __function__;
		    		return false;
	    		}
    			if (!$this->check_path($paths[$i]['to'])) {
					$this->error_code = -1;
		    		$this->error_message = 'invalid destination path : ' . $paths[$i]['to'] . ' in ' . __function__;
		    		return false;
	    		}
    			$list_path_pair['list'][] = array (
    			 	'from' => $paths[$i]['from'],
    				'to' => $paths[$i]['to'],
    			);
    		}
    	} else {
    	    if (!$this->check_path($paths['from'])) {
				$this->error_code = -1;
		    	$this->error_message = 'invalid source path : ' . $paths['from'] . ' in ' . __function__;
		    	return false;
	    	}
    		if (!$this->check_path($paths['to'])) {
				$this->error_code = -1;
		    	$this->error_message = 'invalid destination path : ' . $paths['to'] . ' in ' . __function__;
		    	return false;
	    	}    		
    		$list_path_pair['list'][] = array (
    			'from' => $paths['from'],
    			'to' => $paths['to'],
    		);
    	}
    	$opt['req_opts']['body']['param'] = json_encode($list_path_pair);

        try {
    		if (false === $this->authenticate($requesttype, $method, $opt)) {
    			return false;
    		} else {
    			return true;
    		}
    	} catch (Exception $ex) {
    		$this->error_code = -1;
        	$this->error_message = $ex->getMessage();
        	return false;
    	}
    }
}
?>
