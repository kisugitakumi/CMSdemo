<?php
//模板解析类
class Parser {
	//字段,保存模板内容
	private $_tpl;
	//构造方法,用于获取模板文件里的内容
	public function __construct($_tplFile){
		if(!$this->_tpl=file_get_contents($_tplFile)){
			exit('ERROR:模板文件读取错误');
		}
	}
	//解析普通变量
	private function parVar(){
		$_pattern='/\{\$([\w]+)\}/';
		if (preg_match($_pattern, $this->_tpl)) {
			$this->_tpl=preg_replace($_pattern, "<?php echo \$this->_vars['$1'];?>", $this->_tpl);
		}
	}
	//解析if语句
	private function parIf(){
		$_patternIf='/\{if\s+\$([\w]+)\}/';
		$_patternEndIf='/\{\/if\}/';
		$_patternElse='/\{else\}/';
		if (preg_match($_patternIf, $this->_tpl)) {
			if (preg_match($_patternEndIf, $this->_tpl)) {
				$this->_tpl=preg_replace($_patternIf, "<?php if(\$this->_vars['$1']){?>", $this->_tpl);
				$this->_tpl=preg_replace($_patternEndIf, "<?php }?>", $this->_tpl);
				if (preg_match($_patternElse, $this->_tpl)) {
					$this->_tpl=preg_replace($_patternElse, "<?php } else {?>", $this->_tpl);
				}
			}else{
				exit('ERROR:if语句没有关闭');
			}
		}
	}
	//解析foreach语句
	private function parForeach(){
		$_patternForeach='/\{foreach\s+\$([\w]+)\(([\w]+),([\w]+)\)\}/';
		$_patternEndForeach='/\{\/foreach\}/';
		$_patternVar='/\{@([\w]+)\}/';
		if (preg_match($_patternForeach, $this->_tpl)) {
			if (preg_match($_patternEndForeach, $this->_tpl)) {
				$this->_tpl=preg_replace($_patternForeach, "<?php foreach(\$this->_vars['$1'] as \$$2=>\$$3){?>", $this->_tpl);
				$this->_tpl=preg_replace($_patternEndForeach, "<?php }?>", $this->_tpl);
				if (preg_match($_patternVar, $this->_tpl)) {
					$this->_tpl=preg_replace($_patternVar, "<?php echo \$$1?>", $this->_tpl);
				}
			}else{
				exit('ERROR:foreach语句没有关闭');
			}
		}
	}
	//解析include语句
	private function parInclude(){
		$_pattern='/\{include\s+file=\"([\w\.\-]+)\"\}/';
		if (preg_match($_pattern, $this->_tpl ,$_file)) {
			if (!file_exists($_file[1]) || empty($_file[1])) {
				exit('ERROR:包含文件出错');
			}
			$this->_tpl=preg_replace($_pattern, "<?php include '$1';?>", $this->_tpl);
		}
	}
	//解析php代码注释
	private function parComment(){
		$_pattern='/\{#\}(.*)\{#\}/';
		if (preg_match($_pattern, $this->_tpl)) {
			$this->_tpl=preg_replace($_pattern, "<?php /* $1 */?>", $this->_tpl);
		}
	}
	//解析系统变量
	private function parConfig(){
		$_pattern='/<!--\{([\w]+)\}-->/';
		if (preg_match($_pattern, $this->_tpl)) {
			$this->_tpl=preg_replace($_pattern, "<?php echo \$this->_config['$1'];?>", $this->_tpl);
		}
	}
	//对外公共方法
	public function compile($_parFile){
		//解析模板内容
		$this->parVar();
		$this->parIf();
		$this->parForeach();
		$this->parInclude();
		$this->parComment();
		$this->parConfig();
		//生成编译文件
		if(!file_put_contents($_parFile, $this->_tpl)){
			exit('ERROR:编译文件生成出错');
		}
	}
}
?>