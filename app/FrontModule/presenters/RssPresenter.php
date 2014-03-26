<?php

namespace FrontModule;

final class RssPresenter extends \BasePresenter {

	/* @var RssControl */
	private $__rss;
	
	/* @var RssModel */
	private $__model;
	
	public function getRss (){
		if (!isset($this->__rss)){
			$this->__rss = $this->getComponent('rss');
		}
		return $this->__rss;
	}
	
	public function setRss (\RssControl $value) {
		$this->__rss = $value;
	}
	
	public function __construct(\Nette\DI\IContainer $c) {
		parent::__construct($c);
		
		$this->__model = $this->models->rss;
	}
	
	public function renderDefault () {
			
		$this->setLayout(FALSE);
		
		// properties
		$this->rss->setChannelProperty('title',"TJ Sokol Moravičany");
		$this->rss->setChannelProperty('description',"Webová prezentace Tělovýchovné jednoty Sokol Moravičany.");
		$this->rss->setChannelProperty('link','http://www.sokolmoravicany.cz');
		$this->rss->setChannelProperty("category","aktuality,články,novinky,RSS");
		$this->rss->setChannelProperty("language","cs");
		$this->rss->setChannelProperty("copyright","TJ Sokol Moravičany");
		$this->rss->setChannelProperty('managingEditor',"editor@sokolmoravicany.cz");
		$this->rss->setChannelProperty('webmaster',"webmaster@sokolmoravicany.cz");
		$this->rss->setChannelProperty("lastBuildDate", date('r',time()));
		
		$items = $this->__model->getNews();
		
		$its = array();
		foreach ($items as $item) {
				$tmp = array();
		        $tmp["link"] = 'http://www.sokolmoravicany.cz'.$this->link(":Front:Article:show",$item['article_id']);
				$tmp["title"] = $item['article_title'];
				$tmp["category"] = "články,aktuality,novinky";
				array_push($its, $tmp);
		}

	    $this->rss->items = $its;
	}
}