<?php
class ScoopItBridge extends BridgeAbstract{

	public $maintainer = "Pitchoule";
	public $name = "ScoopIt";
	public $uri = "http://www.scoop.it/";
	public $description = "Returns most recent results from ScoopIt.";

    public $parameters = array( array(
        'u'=>array(
            'name'=>'keyword',
            'required'=>true
        )
    ));

    public function collectData(){
       $this->request = $this->getInput('u');
       $link = $this->uri.'search?q=' .urlencode($this->getInput('u'));

       $html = $this->getSimpleHTMLDOM($link)
         or $this->returnServerError('Could not request ScoopIt. for : ' . $link);

       foreach($html->find('div.post-view') as $element) {
           $item = array();
           $item['uri'] = $element->find('a', 0)->href;
           $item['title'] = preg_replace('~[[:cntrl:]]~', '', $element->find('div.tCustomization_post_title',0)->plaintext);
           $item['content'] = preg_replace('~[[:cntrl:]]~', '', $element->find('div.tCustomization_post_description', 0)->plaintext);
           $this->items[] = $item;
       }
    }

    public function getCacheDuration(){
        return 21600; // 6 hours
    }
}

