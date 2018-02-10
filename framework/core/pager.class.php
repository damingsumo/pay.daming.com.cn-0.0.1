<?php
/**
 * @package     PeaFramework
 * @copyright   PeaFramework
 * @author      Peak
 * @version     $ create by Peak 2009-09-19 $
 */

/**
 * pager class
 *
 */
class pager {
	
	private $MAXTOTAL = 10000;
	
	public $total = 0;
	public $page = 1;
	public $pageSize = 20;
	public $showNumber = 10;
	private $preString = '上一页';
	private $nextString = '下一页';
	private $htmlTag = 'SPAN';
	public $totalPage = 0;
	
	private $prefixURL = '';
	private $showPreAndNext = true;
	private $showPage1InURL = false;
	private $listSeparator = '';
	
	// for styles
	private $stylePre = 'pagerPer';
	private $styleNext = 'pagerNext';
	private $styleLink = 'pagerLink';
	private $styleCurrent = 'pagerCurrent';
	private $styleMore = 'pagerMore';
	
	// for URL
	private $URLSeparators_Param = '&';
	private $URLSeparators_Value = '=';
	private $URLSeparators_First = '?';
	private $URLPageName = 'page';
	private $type = 'default';
	
	public function __construct($mixTotal = null, $page = null, $pageSize = null, $showNumber = null, $type = "default") {
		$this->type = $type;
		if (is_object ( $mixTotal ) && $mixTotal instanceof stdClass && isset ( $mixTotal->total ) && isset ( $mixTotal->page ) && isset ( $mixTotal->pageSize )) {
			$this->total = $mixTotal->total > 0 ? ( integer ) $mixTotal->total : count ( $mixTotal->records );
			$this->page = $mixTotal->page > 0 ? ( integer ) $mixTotal->page : http::GET ( $this->URLPageName, 1 );
			$this->pageSize = $mixTotal->pageSize > 0 ? ( integer ) $mixTotal->pageSize : $this->pageSize;
		} else {
			if (is_numeric ( $mixTotal ) && $mixTotal > 0)
				$this->total = ( integer ) $mixTotal;
			if (is_numeric ( $pageSize ) && $pageSize > 0)
				$this->pageSize = ( integer ) $pageSize;
			if (is_numeric ( $showNumber ) && $showNumber > 0)
				$this->showNumber = ( integer ) $showNumber;
			if (is_numeric ( $page ) && $page > 0) {
				$this->page = ( integer ) $page;
			} elseif (http::GET ( $this->URLPageName, 1 ) > 0) {
				$this->page = ( integer ) http::GET ( $this->URLPageName, 1 );
			}
		}
		
		$this->totalPage = $this->totalPage ();
	}
	
	public function __toString() {
		return $this->toHTML ();
	}
	
	public function toHTML($prefixURL = "", $styles = array()) {
		$this->setStyles ( $styles );
		$this->setPrefixURL ( $prefixURL );
		$this->initData ();
		return $this->getPre () . "&nbsp;" . $this->getPageList () . "&nbsp;" . $this->getNext ();
	}
	
	public function toSimpleHTML($prefixURL = "", $styles = array()) {
		$this->setStyles ( $styles );
		$this->setPrefixURL ( $prefixURL );
		$this->initData ();
		return $this->getPre () . "&nbsp;" . $this->getNext ();
	}
	
	public function toClassicalHTML() {
		// todo......
	}
	
	public function toSimpleHTML2($prefixURL = "", $styles = array()) {
		$this->setStyles ( $styles );
		$this->setPrefixURL ( $prefixURL );
		$this->setPreAndNext ( "[上一页]", "[下一页]" );
		$this->setHtmlTag ( "" );
		$this->initData ();
		return $this->page . "/" . $this->totalPage () . "&nbsp;" . $this->getPre () . "&nbsp;" . $this->getNext ();
	}
	
	public function toJOSN($prefixURL, $jsFuncName) {
		//todo......
	}
	
	public function setShowPreAndNext($isShow) {
		$this->showPreAndNext = ( boolean ) $isShow;
	}
	
	public function setTotal($total) {
		$this->total = $total;
		if ($this->total > $this->MAXTOTAL) {
			$this->total = $this->MAXTOTAL;
		}
	}
	
	public function setPage($page) {
		$this->page = $page;
	}
	
	public function setPageSize($pageSize) {
		$this->pageSize = $pageSize;
	}
	
	public function setShowNumber($showNumber) {
		$this->showNumber = $showNumber;
	}
	
	public function setPreAndNext($pre, $next) {
		$this->preString = $pre;
		$this->nextString = $next;
	}
	
	// list.php<param|first>page<value>
	public function setURLSeparators($param, $value, $firstParam) {
		$this->URLSeparators_Param = $param;
		$this->URLSeparators_Value = $value;
		$this->URLSeparators_First = $firstParam;
	}
	
	public function setShowPage1InURL($isShow = true) {
		$this->showPage1InURL = ( boolean ) $isShow;
	}
	
	public function setURLPageName($pageName) {
		$this->URLPageName = $pageName;
	}
	
	public function setListSeparator($separator) {
		$this->listSeparator = $separator;
	}
	
	private function formatHTML($value, $style) {
		$tag = $this->htmlTag;
		if (! empty ( $tag ) && ! empty ( $style )) {
			return "<$tag class='" . $style . "'>" . $value . "</$tag>";
			//}elseif(!empty($tag) && empty($style)){
		//	return "<$tag>".$value."</$tag>";
		} else {
			return $value;
		}
	}
	
	private function makeLinkHTML($showName, $pageValue) {
		if ($this->type == 'js') {
			return "<a href='javascript:void(0);' title='" . $showName . "'>" . $showName . "</a>";
		} else {
			return "<a href='" . $this->makeLinkURL ( $pageValue ) . "' title='" . $showName . "'>" . $showName . "</a>";
		}
	}
	
	private function makeLinkURL($pageValue) {
		if ($this->showPage1InURL === false && $pageValue == 1) {
			return $this->prefixURL;
		}
		
		$url = "";
		$urlFirst = $this->URLSeparators_Param;
		if (strpos ( $this->prefixURL, $this->URLSeparators_First ) === false) {
			$urlFirst = $this->URLSeparators_First;
		}
		if (substr ( $this->prefixURL, - 1 ) != $urlFirst) {
			$url .= $urlFirst;
		}
		$url .= $this->URLPageName . $this->URLSeparators_Value . $pageValue;
		
		return $this->prefixURL . $url;
	}
	
	private function getPre() {
		if ($this->page == 1 && $this->showPreAndNext && ! empty ( $this->preString )) {
			return $this->formatHTML ( $this->preString, $this->stylePre );
		} elseif ($this->page > 1 && $this->showPreAndNext && ! empty ( $this->preString )) {
			$link = $this->makeLinkHTML ( $this->preString, $this->page - 1 );
			return $this->formatHTML ( $link, $this->stylePre );
		}
		return "";
	}
	
	private function getPageList() {
		$start = 1;
		$end = $start + $this->showNumber;
		$totalPage = $this->totalPage ();
		$pageAreaNo = ceil ( $this->page / $this->showNumber );
		$pageTotalArea = ceil ( $totalPage / $this->showNumber );
		
		$first = 0;
		$last = 0;
		
		if ($totalPage <= $this->showNumber + 2) {
			$start = 1;
			$end = $totalPage;
		}
		
		if ($totalPage > $this->showNumber + 2) {
			$start = ($pageAreaNo - 1) * $this->showNumber + 1;
			$end = $start - 1 + $this->showNumber;
			if ($end > $totalPage) {
				$end = $totalPage;
			}
			if ($this->page > 1 && $pageAreaNo > 1) {
				$first = 1;
			}
			
			if ($this->page < $totalPage && $pageAreaNo < $pageTotalArea) {
				$last = $totalPage;
			}
		}
		$results = "";
		for($i = $start; $i <= $end; $i ++) {
			if ($i == $this->page) {
				$results .= $this->formatHTML ( $i, $this->styleCurrent );
			} else {
				$results .= $this->formatHTML ( $this->makeLinkHTML ( $i, $i ), $this->styleLink );
			}
			if ($end != $i) {
				$results .= $this->listSeparator;
			}
		}
		if ($first) {
			$results = $this->formatHTML ( $this->makeLinkHTML ( 1, 1 ), $this->styleLink ) . $this->listSeparator . $this->formatHTML ( $this->makeLinkHTML ( "...", $start - 1 ), $this->styleMore ) . $this->listSeparator . $results;
		}
		if ($last) {
			$results .= $this->listSeparator;
			$results .= $this->formatHTML ( $this->makeLinkHTML ( "...", $end + 1 ), $this->styleMore );
			$results .= $this->listSeparator;
			$results .= $this->formatHTML ( $this->makeLinkHTML ( $last, $last ), $this->styleLink );
		}
		return $results;
	}
	
	private function getNext() {
		if ($this->page == $this->totalPage () && $this->showPreAndNext && ! empty ( $this->nextString )) {
			return $this->formatHTML ( $this->nextString, $this->styleNext );
		} elseif ($this->page < $this->totalPage () && $this->showPreAndNext && ! empty ( $this->nextString )) {
			$link = $this->makeLinkHTML ( $this->nextString, $this->page + 1 );
			return $this->formatHTML ( $link, $this->styleNext );
		}
		return "";
	}
	
	private function totalPage() {
		return ceil ( $this->total / $this->pageSize );
	}
	
	private function setStyles($styles) {
		if (is_array ( $styles ) && count ( $styles )) {
			$this->stylePre = isset ( $styles ['pre'] ) ? $styles ['pre'] : $this->stylePre;
			$this->styleNext = isset ( $styles ['next'] ) ? $styles ['next'] : $this->styleNext;
			$this->styleLink = isset ( $styles ['link'] ) ? $styles ['link'] : $this->styleLink;
			$this->styleCurrent = isset ( $styles ['current'] ) ? $styles ['current'] : $this->styleCurrent;
			$this->styleMore = isset ( $styles ['more'] ) ? $styles ['more'] : $this->styleMore;
		}
	}
	
	public function setPrefixURL($prefixURL) {
		if (empty ( $prefixURL )) {
			$prefixURL = $_SERVER ['REQUEST_URI'];
		}
		$this->prefixURL = preg_replace ( "/[\/\&\?]{1}" . $this->URLPageName . "[\=\/\_]{1}[0-9]+/i", "", $prefixURL );
	}
	
	public function setHtmlTag($tag) {
		$this->htmlTag = $tag;
	}
	
	private function initData() {
		if ($this->page > $this->totalPage ()) {
			$this->page = $this->totalPage () > 0 ? $this->totalPage () : 1;
		}
		if (! is_numeric ( $this->page ) || $this->page < 1) {
			$this->page = 1;
		}
	}
}
?>