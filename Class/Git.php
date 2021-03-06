<?php

/*
 * Git.php
 *
 * A PHP git library
 *
 * @package    Git.php
 * @version    0.1.1-a
 * @author     James Brumond
 * @copyright  Copyright 2010 James Brumond
 * @license    http://github.com/kbjr/Git.php
 * @link       http://code.kbjrweb.com/project/gitphp
 */

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) die('Bad load order');

// ------------------------------------------------------------------------

/**
 * Git Interface Class
 *
 * This class enables the creating, reading, and manipulation
 * of git repositories.
 *
 * @class  Git
 */
class Git {

	/**
	 * Create a new git repository
	 *
	 * Accepts a creation path, and, optionally, a source path
	 *
	 * @access  public
	 * @param   string  repository path
	 * @param   string  directory to source
	 * @return  GitRepo
	 */	
	public static function &create($repo_path, $source = null) {
		return GitRepo::create_new($repo_path, $source);
	}

	/**
	 * Open an existing git repository
	 *
	 * Accepts a repository path
	 *
	 * @access  public
	 * @param   string  repository path
	 * @return  GitRepo
	 */	
	public static function open($repo_path) {
		return new GitRepo($repo_path);
	}

	/**
	 * Checks if a variable is an instance of GitRepo
	 *
	 * Accepts a variable
	 *
	 * @access  public
	 * @param   mixed   variable
	 * @return  bool
	 */	
	public static function is_repo($var) {
		return (get_class($var) == 'GitRepo');
	}
	
}


function exec_command($command, $cwd) {
  $descriptorspec = array(
    1 => array("pipe", "w"),
    2 => array("pipe", "w")
  );
  $env = array('HOME' => $_SERVER["DOCUMENT_ROOT"]);
  $proc=proc_open($command, $descriptorspec, $pipes, $cwd, $env);
  $stdout = stream_get_contents($pipes[1]);
  $stderr = stream_get_contents($pipes[2]);
  foreach ($pipes as $pipe) {
	  fclose($pipe);
  }

  $status = trim(proc_close($proc));
  if ($status && ($stderr!='')) throw new Exception($stderr);

  $stdout=trim("$stdout\n$stderr");

  return $stdout;
}

function ssh_add_config($ssh_user,$ssh_host) {
    $file_config = $_SERVER["DOCUMENT_ROOT"]."/.ssh/config";
	try {
		$content=exec_command("cat $file_config", $_SERVER["DOCUMENT_ROOT"]);
		$content=explode("\n",$content);
	} catch(Exception $e) {
		$content=array("HashKnownHosts no");
	}
    $is_exist=false;
    foreach($content as $line)
	if (strpos($line,"Host ".$ssh_host)!==false)
	    $is_exist=true;
    if (!$is_exist) {
		$content[]="";
		$content[]="Host ".$ssh_host;
		$content[]="    user ".$ssh_user;
		$content[]="    StrictHostKeyChecking=no";
		$content[]="    UserKnownHostsFile=/dev/null";
		$content[]="    LogLevel=quiet";
	
		$ret = exec_command("echo '".implode("\n",$content)."' > $file_config", $_SERVER["DOCUMENT_ROOT"]);
    }
}

function check_git_server($new_server) {
	if (substr($new_server,0,6)=='ssh://') {
		$ssh_url = substr($new_server,6);
		$pos = strpos($ssh_url, '/');
		if ($pos!==false)
		  $ssh_url = substr($ssh_url,0,$pos); 
		list($ssh_user,$ssh_host)=explode('@',$ssh_url);
		print "<!-- paramAddRepo SSH ssh_url=$ssh_url, ssh_user=$ssh_user, ssh_host=$ssh_host -->\n";
		ssh_add_config($ssh_user,$ssh_host);
	}
}

// ------------------------------------------------------------------------

/**
 * Git Repository Interface Class
 *
 * This class enables the creating, reading, and manipulation
 * of a git repository
 *
 * @class  GitRepo
 */
class GitRepo {

	protected $repo_path = null;
	
	public $git_path = '/usr/bin/git';

	/**
	 * Create a new git repository
	 *
	 * Accepts a creation path, and, optionally, a source path
	 *
	 * @access  public
	 * @param   string  repository path
	 * @param   string  directory to source
	 * @return  GitRepo
	 */	
	public static function &create_new($repo_path, $source = null) {
		if (is_dir($repo_path) && file_exists($repo_path."/.git") && is_dir($repo_path."/.git")) {
			throw new Exception('"$repo_path" is already a git repository');
		} else {
			$repo = new self($repo_path, true, false);
			if (is_string($source))
				$repo->clone_from($source);
			else $repo->run('init');
			return $repo;
		}
	}

	/**
	 * Constructor
	 *
	 * Accepts a repository path
	 *
	 * @access  public
	 * @param   string  repository path
	 * @param   bool    create if not exists?
	 * @return  void
	 */
	public function __construct($repo_path = null, $create_new = false, $_init = true) {
		if (is_string($repo_path))
			$this->set_repo_path($repo_path, $create_new, $_init);
	}

	/**
	 * Set the repository's path
	 *
	 * Accepts the repository path
	 *
	 * @access  public
	 * @param   string  repository path
	 * @param   bool    create if not exists?
	 * @return  void
	 */
	public function set_repo_path($repo_path, $create_new = false, $_init = true) {
		if (is_string($repo_path)) {
			if ($new_path = realpath($repo_path)) {
				$repo_path = $new_path;
				if (is_dir($repo_path)) {
					if (file_exists($repo_path."/.git") && is_dir($repo_path."/.git")) {
						$this->repo_path = $repo_path;
					} else {
						if ($create_new) {
							$this->repo_path = $repo_path;
							if ($_init) $this->run('init');
						} else {
							throw new Exception("'$repo_path' is not a git repository");
						}
					}
				} else {
					throw new Exception("'$repo_path' is not a directory");
				}
			} else {
				if ($create_new) {
					if ($parent = realpath(dirname($repo_path))) {
						mkdir($repo_path);
						$this->repo_path = $repo_path;
						if ($_init) $this->run('init');
					} else {
						throw new Exception('cannot create repository in non-existent directory');
					}
				} else {
					throw new Exception("'$repo_path' does not exist");
				}
			}
		}
	}

	public function get_repo_path() {
		return $this->repo_path;
	}

	/**
	 * Tests if git is installed
	 *
	 * @access  public
	 * @return  bool
	 */	
	public function test_git() {
		$descriptorspec = array(
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w'),
		);
		$pipes = array();
		$resource = proc_open($this->git_path, $descriptorspec, $pipes);

		$stdout = stream_get_contents($pipes[1]);
		$stderr = stream_get_contents($pipes[2]);
		foreach ($pipes as $pipe) {
			fclose($pipe);
		}

		$status = trim(proc_close($resource));
		return ($status != 127);
	}

	/**
	 * Run a command in the git repository
	 *
	 * Accepts a shell command to run
	 *
	 * @access  protected
	 * @param   string  command to run
	 * @return  string
	 */	
	protected function run_command($command) {
		return exec_command($command, $this->repo_path);
	}

	/**
	 * Run a git command in the git repository
	 *
	 * Accepts a git command to run
	 *
	 * @access  public
	 * @param   string  command to run
	 * @return  string
	 */	
	public function run($command) {
		return $this->run_command($this->git_path." ".$command);
	}

	/**
	 * Runs a `git add` call
	 *
	 * Accepts a list of files to add
	 *
	 * @access  public
	 * @param   mixed   files to add
	 * @return  string
	 */	
	public function add($files = "*") {
		if (is_array($files)) $files = '"'.implode('" "', $files).'"';
		return $this->run("add $files -v");
	}

	/**
	 * Runs a `git commit` call
	 *
	 * Accepts a commit message string
	 *
	 * @access  public
	 * @param   string  commit message
	 * @return  string
	 */	
	public function commit($message = "") {
		return $this->run("commit -av -m \"$message\"");
	}

	/**
	 * Runs a `git clone` call to clone the current repository
	 * into a different directory
	 *
	 * Accepts a target directory
	 *
	 * @access  public
	 * @param   string  target directory
	 * @return  string
	 */	
	public function clone_to($target) {
		return $this->run("clone --local ".$this->repo_path." $target");
	}

	/**
	 * Runs a `git clone` call to clone a different repository
	 * into the current repository
	 *
	 * Accepts a source directory
	 *
	 * @access  public
	 * @param   string  source directory
	 * @return  string
	 */	
	public function clone_from($source) {
		return $this->run("clone --local $source ".$this->repo_path);
	}

	/**
	 * Runs a `git clone` call to clone a remote repository
	 * into the current repository
	 *
	 * Accepts a source url
	 *
	 * @access  public
	 * @param   string  source url
	 * @return  string
	 */	
	public function clone_remote($source) {
		return $this->run("clone $source ".$this->repo_path);
	}

	/**
	 * Runs a `git clean` call
	 *
	 * Accepts a remove directories flag
	 *
	 * @access  public
	 * @param   bool    delete directories?
	 * @return  string
	 */	
	public function clean($dirs = false) {
		return $this->run("clean".(($dirs) ? " -d" : ""));
	}

	/**
	 * Runs a `git branch` call
	 *
	 * Accepts a name for the branch
	 *
	 * @access  public
	 * @param   string  branch name
	 * @return  string
	 */	
	public function create_branch($branch) {
		return $this->run("branch $branch");
	}

	/**
	 * Runs a `git branch -[d|D]` call
	 *
	 * Accepts a name for the branch
	 *
	 * @access  public
	 * @param   string  branch name
	 * @return  string
	 */	
	public function delete_branch($branch, $force = false) {
		return $this->run("branch ".(($force) ? '-D' : '-d')." $branch");
	}

	/**
	 * Runs a `git branch` call
	 *
	 * @access  public
	 * @param   bool    keep asterisk mark on active branch
	 * @return  array
	 */
	public function list_branches($keep_asterisk = false) {
		$branchArray = explode("\n", $this->run("branch"));
		foreach($branchArray as $i => &$branch) {
			$branch = trim($branch);
			if (! $keep_asterisk)
				$branch = str_replace("* ", "", $branch);
			if ($branch == "")
				unset($branchArray[$i]);
		}
		return $branchArray;
	}

	/**
	 * Returns name of active branch
	 *
	 * @access  public
	 * @param   bool    keep asterisk mark on branch name
	 * @return  string
	 */
	public function active_branch($keep_asterisk = false) {
		$branchArray = $this->list_branches(true);
		$active_branch = preg_grep("/^\*/", $branchArray);
		reset($active_branch);
		if ($keep_asterisk)
			return current($active_branch);
		else
			return str_replace("* ", "", current($active_branch));
	}

	/**
	 * Runs a `git checkout` call
	 *
	 * Accepts a name for the branch
	 *
	 * @access  public
	 * @param   string  branch name
	 * @return  string
	 */	
	public function checkout($branch) {
		return $this->run("checkout $branch");
	}

	/**
	 * Returns short file status
	 *
	 * @access  public
	 * @return  array
	 */
	public function getStatus() {
		$status_list=array();
		$status=$this->run("status -s");
		$status=explode("\n",$status);
		foreach($status as $status_item) {
		      $name=trim(substr($status_item,2));
		      if ($name!='')
			    $status_list[$name]=substr($status_item,0,2);
		}
		return $status_list;
	}

	/**
	 * Returns number of file of each status
	 *
	 * @access  public
	 * @return  array
	 */
	public function getStatusNumber() {
		$status_list=array('?'=>0,'M'=>0,'A'=>0,'D'=>0,'R'=>0,'C'=>0,'U'=>0);
		$status=$this->getStatus();
		foreach($status as $file_name=>$status_val) {
		      $status_val=trim($status_val);
		      $first_car=$status_val[0];
		      if (isset($status_list[$first_car]))
			    $status_list[$first_car]=$status_list[$first_car]+1;
		}
		return $status_list;
	}

}

function get_ssh_key() {
    $stdout = exec_command("bash Class/ssh_config.sh", null);
    return str_replace("\n","{[newline]}",$stdout);
}

/* End Of File */
