<?php
//模板类
class Templates{
	//动态接收变量的字段
	private $_vars=array();
	//保存系统变量
	private $_config=array();
	//创建一个构造方法,来验证各个目录是否存在
	public function __construct(){
		if (!is_dir(TPL_DIR) || !is_dir(TPL_C_DIR) || !is_dir(CACHE)) {
			exit('ERROR:模板目录或者编译目录或者缓存目录不存在！');
		}
		//保存系统变量
		$_sxe=simplexml_load_file('config/profile.xml');
		$_tagLib=$_sxe->xpath('/root/taglib');
		foreach ($_tagLib as $_tag){
			$this->_config["$_tag->name"]=$_tag->value;
		}
	}
	//assign()方法，用于注入变量
	public function assign($_var,$_value){
		//$_var用于同步模板里的变量名，例如index.php是name，那么index.tpl就是{$name}
		//$_value表示index.php里的$_name的值
		if (isset($_var) && !empty($_var)) {
			$this->_vars[$_var]=$_value;
		}else{
			exit('ERROR:请设置模板变量');
		}
	}
	//display方法
	public function display($_file){
		//设置模板的路径
		$_tplFile=TPL_DIR.$_file;
		//判断模板是否存在
		if (!file_exists($_tplFile)) {
			exit('ERROR:模板文件不存在');
		}
		//生成编译文件
		$_parFile=TPL_C_DIR.md5($_file).$_file.'.php';
		//缓存文件
		$_cacheFile=CACHE.md5($_file).$_file.'.html';
		//第二次运行相同网页时直接载入缓存文件
		if (IS_CACHE) {
			//缓存文件和编译文件都存在
			if (file_exists($_cacheFile) && file_exists($_parFile)) {
				//判断模板文件是否修改过，并且判断编译文件是否修改过
				if (filemtime($_parFile)>=filemtime($_tplFile) && filemtime($_cacheFile) >= filemtime($_parFile)) {
					//载入缓存文件
					include $_cacheFile;
					return ;
				}
			}
		}
		//当编译文件不存在或者模板文件被修改过,则生成编译文件
		if (!file_exists($_parFile) || filemtime($_parFile)<filemtime($_tplFile)) {
			//引入模板解析类
			require ROOT_PATH.'/includes/Parser.class.php';
			//模板文件
			$_parser=new Parser($_tplFile);
			//编译文件
			$_parser->compile($_parFile);
		}
		//载入编译文件
		include $_parFile;
		if (IS_CACHE) {
			//获取缓冲区里的数据，并且创建缓存文件
			file_put_contents($_cacheFile, ob_get_contents());
			//清除缓冲区（即是清除编译文件加载的内容）
			ob_end_clean();
			//载入缓存文件
			include $_cacheFile;
		}
	}
}
?>