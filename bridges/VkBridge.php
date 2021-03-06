<?php

class VkBridge extends BridgeAbstract {

    public $maintainer = "ahiles3005";
    public $name = "VK.com";
    public $uri = "http://vk.com/";
    public $description = "Working with open pages";
    public $parameters=array(
        'Url on page group or user' => array(
            'u'=>array(
                'name'=>'Url',
                'required'=>true
            )
        )
    );

    public function collectData(){
        $text_html = $this->getContents(urldecode($this->getInput('u')))
            or $this->returnServerError('No results for this query.');
        $text_html = iconv('windows-1251', 'utf-8', $text_html);
        $html = str_get_html($text_html);
        foreach ($html->find('div.post_table') as $post) {
            if (is_object($post->find('a.wall_post_more', 0))) {
                $post->find('a.wall_post_more', 0)->outertext = ''; //delete link "show full" in content
            }
            $item = array();
            $item['content'] = strip_tags($post->find('div.wall_post_text', 0)->innertext);
            if (is_object($post->find('a.page_media_link_title', 0))) {
                $link = $post->find('a.page_media_link_title', 0)->getAttribute('href');
                $item['content'] .= "\n\rExternal link: " . str_replace('/away.php?to=', '', urldecode($link)); //external link in the post
            }
            //get video on post
            if (is_object($post->find('span.post_video_title_content', 0))) {
                $titleVideo = $post->find('span.post_video_title_content', 0)->plaintext;
                $linkToVideo = $this->uri . $post->find('a.page_post_thumb_video', 0)->getAttribute('href');
                $item['content'] .= "\n\r {$titleVideo}: {$linkToVideo}";
            }
            $item['uri'] = $this->uri . $post->find('.reply_link_wrap', 0)->find('a', 0)->getAttribute('href'); // get post link
            $item['date'] = $post->find('span.rel_date', 0)->plaintext;
            $this->items[] = $item;
            // var_dump($item['date']);
        }
    }

    public function getCacheDuration() {
        return 300; // 5 minutes
    }

}
