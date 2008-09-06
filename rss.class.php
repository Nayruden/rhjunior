<?php
class RSSFeed {
	var $channel_title;
	var $channel_url;
	var $channel_description;
	var $channel_language;
	var $channel_copyright;
	var $channel_managingEditor;
	var $channel_webMaster;
	var $channel_pubDate;
	var $channel_lastBuildDate;
	var $channel_generator;
	var $channel_docs;
	var $channel_image;
	var $channel_items;
	
	var $initialized;
	
    function RSSFeed() {
    }
	
	function setImage( $url, $title, $link, $width=NULL, $height=NULL, $description=NULL ) {
		$this->channel_image = array( 'url'=>$url, 'title'=>$title, 'link'=>$link, 'width'=>$width, 'height'=>$height, 'description'=>$description );
	}
	
	function setChannel( $title, $url, $description ) {
		$this->channel_title = $title;
		$this->channel_url = $url;
		$this->channel_description = $description;
		
		$this->initialized = TRUE;
		$this->channel_items = array();
	}
	
	function addItem( $title, $link, $description, $author=NULL, $comments=NULL, $guid=NULL, $pubDate=NULL ) {
		$this->channel_items[] = array( 'title'=>$title, 'link'=>$link, 'description'=>$description, 'author'=>$author, 'comments'=>$comments,
			'guid'=>$guid, 'pubDate'=>$pubDate );
	}
	
	function buildRSS( $tab="\t", $nl="\n" ) {
		$output = '';
		
		$output .= '<?xml version="1.0"?>' . $nl;
		$output .= '<rss version="2.0">' . $nl;
		$output .= str_repeat( $tab, 1 ) . '<channel>' . $nl;
		$output .= str_repeat( $tab, 2 ) . '<title>' . $this->channel_title . '</title>' . $nl;
		$output .= str_repeat( $tab, 2 ) . '<link>' . $this->channel_url . '</link>' . $nl;
		$output .= str_repeat( $tab, 2 ) . '<description>' . $this->channel_description . '</description>' . $nl;
		
		foreach ( $this->channel_items as $data ) {
			$output .= str_repeat( $tab, 2 ) . '<item>' . $nl;
			$output .= str_repeat( $tab, 3 ) . '<title>' . $data[ 'title' ] . '</title>' . $nl;
			$output .= str_repeat( $tab, 3 ) . '<link>' . $data[ 'link' ] . '</link>' . $nl;
			$output .= str_repeat( $tab, 3 ) . '<description>' . $data[ 'description' ] . '</description>' . $nl;
			if ( !empty( $data[ 'author' ] )
				$output .= str_repeat( $tab, 3 ) . '<author>' . $data[ 'author' ] . '</author>' . $nl;
			if ( !empty( $data[ 'comments' ] )
				$output .= str_repeat( $tab, 3 ) . '<comments>' . $data[ 'comments' ] . '</comments>' . $nl;
			if ( !empty( $data[ 'guid' ] )
				$output .= str_repeat( $tab, 3 ) . '<guid>' . $data[ 'guid' ] . '</guid>' . $nl;
			if ( !empty( $data[ 'pubDate' ] )
				$output .= str_repeat( $tab, 3 ) . '<pubDate>' . $data[ 'pubDate' ] . '</pubDate>' . $nl;
			$output .= str_repeat( $tab, 2 ) . '</item>' . $nl;
		}
		
		$output .= str_repeat( $tab, 1 ) . '</channel>' . $nl;
		$output .= '</rss>' . $nl;
		
		return $output;
	}
};
?>