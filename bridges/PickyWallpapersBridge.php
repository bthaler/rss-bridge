<?php
class PickyWallpapersBridge extends BridgeAbstract {

	public $maintainer = "nel50n";
	public $name = "PickyWallpapers Bridge";
	public $uri = "http://www.pickywallpapers.com/";
	public $description = "Returns the latests wallpapers from PickyWallpapers";

    public $parameters = array( array(
      'c'=>array(
        'name'=>'category',
        'required'=>true
      ),
        's'=>array('name'=>'subcategory'),
        'm'=>array(
            'name'=>'Max number of wallpapers',
            'defaultValue'=>12,
            'type'=>'number'
        ),
        'r'=>array(
            'name'=>'resolution',
            'exampleValue'=>'1920x1200, 1680x1050,…',
            'defaultValue'=>'1920x1200',
            'pattern'=>'[0-9]{3,4}x[0-9]{3,4}'
        )
    ));


    public function collectData(){
        $lastpage = 1;
        $num = 0;
        $max = $this->getInput('m');
        $resolution = $this->getInput('r');    // Wide wallpaper default

        for ($page = 1; $page <= $lastpage; $page++) {
          $html = $this->getSimpleHTMLDOM($this->getURI().'/page-'.$page.'/')
            or $this->returnServerError('No results for this query.');

            if ($page === 1) {
                preg_match('/page-(\d+)\/$/', $html->find('.pages li a', -2)->href, $matches);
                $lastpage = min($matches[1], ceil($max/12));
            }

            foreach($html->find('.items li img') as $element) {

                $item = array();
                $item['uri'] = str_replace('www', 'wallpaper', $this->uri).'/'.$resolution.'/'.basename($element->src);
                $item['timestamp'] = time();
                $item['title'] = $element->alt;
                $item['content'] = $item['title'].'<br><a href="'.$item['uri'].'">'.$element.'</a>';
                $this->items[] = $item;

                $num++;
                if ($num >= $max)
                    break 2;
            }
        }
    }

    public function getURI(){
        $subcategory = $this->getInput('s');
        $link = $this->uri.$this->getInput('r').'/'.$this->getInput('c').'/'.$subcategory;
        return $link;
    }

    public function getName(){
        $subcategory = $this->getInput('s');
        return 'PickyWallpapers - '.$this->getInput('c')
          .($subcategory? ' > '.$subcategory : '')
          .' ['.$this->getInput('r').']';
    }

    public function getCacheDuration(){
        return 43200; // 12 hours
    }
}
