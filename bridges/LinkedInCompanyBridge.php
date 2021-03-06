<?php
class LinkedInCompanyBridge extends BridgeAbstract{

	public $maintainer = "regisenguehard";
	public $name = "LinkedIn Company";
	public $uri = "https://www.linkedin.com/";
	public $description = "Returns most recent actus from Company on LinkedIn. (https://www.linkedin.com/company/<strong style=\"font-weight:bold;\">apple</strong>)";

    public $parameters = array( array(
        'c'=>array(
            'name'=>'Company name',
            'required'=>true
        )
    ));

    public function collectData(){
        $html = '';
        $link = $this->uri.'company/'.$this->getInput('c');

        $html = $this->getSimpleHTMLDOM($link)
            or $this->returnServerError('Could not request LinkedIn.');

        foreach($html->find('//*[@id="my-feed-post"]/li') as $element) {
            $title = $element->find('span.share-body', 0)->innertext;
            if ($title) {
                $item = array();
                $item['uri'] = $link;
                $item['title'] = mb_substr(strip_tags($element->find('span.share-body', 0)->innertext), 0 ,100);
                $item['content'] = strip_tags($element->find('span.share-body', 0)->innertext);
                $this->items[] = $item;
                $i++;
            }
        }
    }

    public function getCacheDuration(){
        return 21600; // 6 hours
    }
}
