<?php

/**
 * 分页器
 * 需要添加的功能：
 * 1.获取总记录数的属性；
 * 2.获取当前网页的总记录数的方法；
 * 3.添加一个GoPage方法：可以在输入框输入一个页码，然后跳转到输入的页码的页面；
 * 4.添加一个下拉框，列出所有的页面，用于从下拉框选择一个页面；
 * 5.将首页，上一页，下一页，末页的文字进行参数化：用变量替换。
 */
class Paginator {

    // 第一页的页码
    private $first = null;
    // 最后一页的页码
    private $last = null;
    // 上一页的页码
    private $prev = null;
    // 下一页的页码
    private $next = null;
    // 当前页码
    private $current = null;
    // 总记录数（一共有几条记录）
    private $totalItemCount = null;
    // 分页大小（每页最多可以显示几条记录）：默认每页显示10个记录
    private $pageSize = null;
    // 总页数（一共多少页）
    private $pageCount = null;
    // 显示在网页上的页码数组
    private $pagesInRange;
    // 请求的uri
    private $uri = null;
    //
    //当前页上第一条记录是整个记录集的第几条
    private $firstItemNumber;
    //第一个显示的页码
    private $firstPageInRange;
    //本页上的记录有几条
    private $currentItemCount;
    //当前页上最后一条记录是整个记录集种的第几条
    private $lastItemNumber;
    //最后一个显示的页码
    private $lastPageInRange;

    //获取页码数组
    public function getPagesInRange() {
        return $this->pagesInRange;
    }

    public function getFirstItemNumber() {
        if (count($this->pagesInRange) > 0) {
            return $this->pagesInRange[0];
        } else {
            return null;
        }
    }

    public function getLastItemNumber() {
        $rangeCount = count($this->pagesInRange);
        if ($rangeCount > 0) {
            return $this->pagesInRange[$rangeCount];
        } else {
            return null;
        }
    }

    /**
     * 
     * @param type $uri：页码上的请求地址
     * @param type $totalItemCount：总记录数
     * @param type $current：当前页码
     * @param type $pageSize：分页大小
     * @param type $pageRange：要显示的页码范围，默认显示5个分页。
     */
    public function __construct($uri, $totalItemCount, $current = 1, $pageSize = 10, $pageRange = 5) {
        $this->uri = $uri;
        $this->totalItemCount = $totalItemCount; // 总记录数
        $this->pageSize = $pageSize; // 每页显示多少条记录
        $this->current = $current; // 当前页码
        $this->pageCount = ceil($this->totalItemCount / $this->pageSize); // 总页数
        //$this->pagesInRange = array();
        if ($this->pageCount > 0) {
            // 首页页码
            $this->first = 1;
            // 末页页码
            $this->last = $this->pageCount;
        }
        // 上一页页码
        if ($this->current > 1) {
            $this->prev = $this->current - 1;
        }
        // 下一页页码
        if ($this->current < $this->pageCount) {
            $this->next = $this->current + 1;
        }
        // 要显示的页码范围
        if ($this->pageCount <= $pageRange) {
            /*
              for ($i = 1; $i <= $this->pageCount; $i ++) {
              array_push($this->pagesInRange, $i);
              }
             */
            $this->pagesInRange = range(1, $this->pageCount);
        } else {
            $half = ceil($pageRange / 2);
            if ($this->current < $half) {
                /*
                  for ($i = 1; $i <= $pageRange; $i ++) {
                  array_push($this->pagesInRange, $i);
                  }
                 */
                $this->pagesInRange = range(1, $pageRange);
            } else if ($this->current + $half > $this->pageCount) {
                /*
                  for ($i = $this->pageCount - $pageRange + 1; $i <= $this->pageCount; $i ++) {
                  array_push($this->pagesInRange, $i);
                  }
                 */
                $start = $this->pageCount - $pageRange + 1;
                $end = $this->pageCount;
                $this->pagesInRange = range($start, $end);
            } else {
                /*
                  for ($i = $this->current - $half + 1; $i <= $this->current + $half - 1; $i ++) {
                  array_push($this->pagesInRange, $i);
                  }
                 */
                $start = $this->current - $half + 1;
                $end = $this->current + $half - 1;
                $this->pagesInRange = range($start, $end);
            }
        }
    }

    /**
     * 生成页码
     */
    public function createPageLinks() {
        $html = "<div>";
        if ($this->first < $this->current) {
            $html.="<a href='" . $this->uri . "?page=1'>首页</a>&nbsp;";
            $html.="<a href='" . $this->uri . "?page=" . ($this->current - 1) . "'>上一页</a>&nbsp;";
        }

        foreach ($this->pagesInRange as $page) {
            if ($this->current == $page) {
                $html.="<span>" . $this->current . "</span>&nbsp;";
            } else {
                $html.="<a href='" . $this->uri . "?page=" . $page . "'>" . $page . "</a>&nbsp;";
            }
        }

        if ($this->last > $this->current) {
            $html.="<a href='" . $this->uri . "?page=" . ($this->current + 1) . "'>下一页</a>&nbsp;";
            $html.="<a href='" . $this->uri . "?page=" . $this->pageCount . "'>尾页</a>&nbsp;";
        }
        $html.="</div>";

        echo $html;
    }

    /**
     * 生成bootstrap风格页码
     */
    public function createBootstrapPageLinks() {
        $html = "<div class='pagination pagination-small'><ul>";
        if ($this->first < $this->current) {
            $html.="<li><a href='" . $this->uri . "?page=1'>首页</a>&nbsp;</li>";
            $html.="<li><a href='" . $this->uri . "?page=" . ($this->current - 1) . "'>上一页</a>&nbsp;</li>";
        }

        foreach ($this->pagesInRange as $page) {
            if ($this->current == $page) {
                $html.="<li class='active'><span>" . $this->current . "</span>&nbsp;</li>";
            } else {
                $html.="<li><a href='" . $this->uri . "?page=" . $page . "'>" . $page . "</a>&nbsp;</li>";
            }
        }

        if ($this->last > $this->current) {
            $html.="<li><a href='" . $this->uri . "?page=" . ($this->current + 1) . "'>下一页</a>&nbsp;</li>";
            $html.="<li><a href='" . $this->uri . "?page=" . $this->pageCount . "'>尾页</a>&nbsp;</li>";
        }
        $html.="</ul></div>";

        echo $html;
    }

}
