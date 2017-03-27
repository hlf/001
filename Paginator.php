<?php

/**
 * 分页器
 * 作者： 韩龙飞
 * 日期：2015-7-25
 * 需要添加的功能：
 * 1.获取总记录数的属性；
 * 2.获取当前网页的总记录数的方法；
 * 3.添加一个GoPage方法：可以在输入框输入一个页码，然后跳转到输入的页码的页面；
 * 4.添加一个下拉框，列出所有的页面，用于从下拉框选择一个页面；
 */
class Paginator
{
	// 首页显示文本
	private $firstPageLabel = '首页';
	// 末页显示文本
	private $lastPageLabel = '末页';
	// 上一页显示文本
	private $prevPageLabel = '上一页';
	// 下一页显示文本
	private $nextPageLabel = '下一页';
	
	// 第一页的页码
	private $first;
	// 最后一页的页码
	private $last;
	// 上一页的页码
	private $prev;
	// 下一页的页码
	private $next;
	// 当前页码
	private $current;
	// 总记录数（一共有几条记录）
	private $totalItemCount;
	// 分页大小（每页最多可以显示几条记录）：默认每页显示10个记录
	private $pageSize;
	// 总页数（一共多少页）
	private $pageCount;
	// 显示在网页上的页码数组（1，2，3，4，5...）
	private $pagesInRange;
	// 请求的uri
	private $uri;
	/**
	 * 构造函数
	 *
	 * @param unknown $uri：请求的url        	
	 * @param unknown $totalItemCount：总记录数        	
	 * @param number $current：当前页码        	
	 * @param number $pageSize：分页大小        	
	 * @param number $pageRange：页码范围        	
	 */
	public function __construct($uri, $totalItemCount, $current = 1, $pageSize = 10, $pageRange = 5, $params = array())
	{
		$this->uri = $uri;
		$this->totalItemCount = $totalItemCount; // 总记录数
		$this->pageSize = $pageSize; // 每页显示多少条记录
		$this->current = $current; // 当前页码
		$this->pageCount = ceil ( $this->totalItemCount / $this->pageSize ); // 总页数
		                                                                     
		// 创建首页，尾页，上一页，下一页的页码
		$this->createFourPageRange ();
		// 创建页码
		$this->createPageRange ( $pageRange );
		// 首页，尾页，上一页，下一页
		$this->createLabel ( $params );
	}
	/**
	 * 创建首页，尾页，上一页，下一页的页码
	 */
	private function createFourPageRange()
	{
		// $this->pagesInRange = array();
		if ($this->pageCount > 0)
		{
			// 首页页码
			$this->first = 1;
			// 末页页码
			$this->last = $this->pageCount;
		}
		// 上一页页码
		if ($this->current > 1)
		{
			$this->prev = $this->current - 1;
		}
		// 下一页页码
		if ($this->current < $this->pageCount)
		{
			$this->next = $this->current + 1;
		}
	}
	
	/**
	 * 创建页码
	 */
	private function createPageRange($pageRange)
	{
		// 1.总页数小于页码范围
		if ($this->pageCount <= $pageRange)
		{
			$this->pagesInRange = range ( 1, $this->pageCount );
		}
		// 2.总页数大于页码范围
		else
		{
			$half = ceil ( $pageRange / 2 );
			// 奇数个页码：页码尽量显示在中间
			if ($pageRange % 2 == 1)
			{
				if ($this->current < $half)
				{
					$this->pagesInRange = range ( 1, $pageRange );
				}
				else if ($this->current + $half > $this->pageCount)
				{
					$start = $this->pageCount - $pageRange + 1;
					$end = $this->pageCount;
					$this->pagesInRange = range ( $start, $end );
				}
				else
				{
					$start = $this->current - $half + 1;
					$end = $this->current + $half - 1;
					$this->pagesInRange = range ( $start, $end );
				}
			}
			// 偶数个页码：页码尽量显示在中间后面的位置
			else
			{
				if ($this->current - $half - 1 < 0)
				{
					$this->pagesInRange = range ( 1, $pageRange );
				}
				else if ($this->current + $half > $this->pageCount)
				{
					$start = $this->pageCount - $pageRange + 1;
					$end = $this->pageCount;
					$this->pagesInRange = range ( $start, $end );
				}
				else
				{
					$start = $this->current - $half;
					$end = $this->current + $half - 1;
					$this->pagesInRange = range ( $start, $end );
				}
			}
		}
	}
	/**
	 * 首页，尾页，上一页，下一页
	 */
	private function createLabel($params)
	{
		if ($params != null && is_array ( $params ))
		{
			if (isset ( $params ['firstPageLabel'] ))
			{
				$this->firstPageLabel = $params ['firstPageLabel'];
			}
			if (isset ( $params ['lastPageLabel'] ))
			{
				$this->lastPageLabel = $params ['lastPageLabel'];
			}
			if (isset ( $params ['prevPageLabel'] ))
			{
				$this->prevPageLabel = $params ['prevPageLabel'];
			}
			if (isset ( $params ['nextPageLabel'] ))
			{
				$this->nextPageLabel = $params ['nextPageLabel'];
			}
		}
	}
	
	/**
	 * 创建页码链接
	 */
	public function createPageLinks()
	{
		$html = "<div>";
		if ($this->first < $this->current)
		{
			$html .= "<a href='" . $this->uri . "?page=1'>" . $this->firstPageLabel . "</a>&nbsp;";
			$html .= "<a href='" . $this->uri . "?page=" . ($this->current - 1) . "'>" . $this->prevPageLabel . "</a>&nbsp;";
		}
		
		foreach ( $this->pagesInRange as $page )
		{
			if ($this->current == $page)
			{
				$html .= "<span>" . $this->current . "</span>&nbsp;";
			}
			else
			{
				$html .= "<a href='" . $this->uri . "?page=" . $page . "'>" . $page . "</a>&nbsp;";
			}
		}
		
		if ($this->last > $this->current)
		{
			$html .= "<a href='" . $this->uri . "?page=" . ($this->current + 1) . "'>" . $this->nextPageLabel . "</a>&nbsp;";
			$html .= "<a href='" . $this->uri . "?page=" . $this->pageCount . "'>" . $this->lastPageLabel . "</a>&nbsp;";
		}
		$html .= '</div>';
		
		echo $html;
	}
	
	/**
	 * 创建页码链接
	 */
	public function createPageLinksByStyle()
	{
		// $html = "<div class='pagination pagination-small'><ul>";
		$html = "<div class='pager'><ul class='yiiPager'>";
		if ($this->first < $this->current)
		{
			$html .= "<li class='page'><a href='" . $this->uri . "?page=1'>" . $this->firstPageLabel . "</a>&nbsp;</li>";
			$html .= "<li class='page'><a href='" . $this->uri . "?page=" . ($this->current - 1) . "'>" . $this->prevPageLabel . "</a>&nbsp;</li>";
		}
		
		foreach ( $this->pagesInRange as $page )
		{
			if ($this->current == $page)
			{
				$html .= "<li class='page selected'><a href='javascript:void;'>" . $this->current . "&nbsp;</a></li>";
			}
			else
			{
				$html .= "<li class='page'><a href='" . $this->uri . "?page=" . $page . "'>" . $page . "</a>&nbsp;</li>";
			}
		}
		
		if ($this->last > $this->current)
		{
			$html .= "<li class='page'><a href='" . $this->uri . "?page=" . ($this->current + 1) . "'>" . $this->nextPageLabel . "</a>&nbsp;</li>";
			$html .= "<li class='page'><a href='" . $this->uri . "?page=" . $this->pageCount . "'>" . $this->lastPageLabel . "</a>&nbsp;</li>";
		}
		$html .= "</ul></div>";
		
		echo $html;
	}
	/*
	 * 当前页
	 */
	public function getCurrent()
	{
		return $this->current;
	}
	/**
	 * 总页数
	 */
	public function getPageCount()
	{
		return $this->pageCount;
	}
}
