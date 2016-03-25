using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace My.Utils
{
    public class Paginator
    {
        // 首页显示文本
        private string firstPageLabel = "首页";
        // 末页显示文本
        private string lastPageLabel = "末页";
        // 上一页显示文本
        private string prevPageLabel = "上一页";
        // 下一页显示文本
        private string nextPageLabel = "下一页";

        // 第一页的页码
        private int first;
        // 最后一页的页码
        private int last;
        // 上一页的页码
        private int prev;
        // 下一页的页码
        private int next;
        // 当前页码
        private int current;
        // 总记录数（一共有几条记录）
        private int totalItemCount;
        // 分页大小（每页最多可以显示几条记录）：默认每页显示10个记录
        private int pageSize;
        // 总页数（一共多少页）
        private int pageCount;
        // 显示在网页上的页码数组（1，2，3，4，5...）
        private IEnumerable<int> pagesInRange = new List<int>();
        // 请求的uri
        private string uri;

        //构造函数
        public Paginator(string uri, int totalItemCount, int current = 1, int pageSize = 10, int pageRange = 5, Dictionary<string, string> @params = null)
        {
            this.uri = uri;
            this.totalItemCount = totalItemCount; // 总记录数
            this.pageSize = pageSize; // 每页显示多少条记录
            this.current = current; // 当前页码
            this.pageCount = (int)Math.Ceiling(this.totalItemCount / (double)this.pageSize); // 总页数

            // 创建首页，尾页，上一页，下一页的页码
            this.createFourPageRange();
            // 创建页码
            this.createPageRange(pageRange);
            // 首页，尾页，上一页，下一页
            this.createLabel(@params);
        }

        //创建首页，尾页，上一页，下一页的页
        private void createFourPageRange()
        {
            if (this.pageCount > 0)
            {
                // 首页页码
                this.first = 1;
                // 末页页码
                this.last = this.pageCount;
            }
            // 上一页页码
            if (this.current > 1)
                this.prev = this.current - 1;
            // 下一页页码
            if (this.current < this.pageCount)
                this.next = this.current + 1;
        }

        //创建页码
        private void createPageRange(int pageRange)
        {
            // 1.总页数小于页码范围
            if (this.pageCount <= pageRange)
            {
                this.pagesInRange = Enumerable.Range(1, this.pageCount);
            }
            // 2.总页数大于页码范围
            else
            {
                int half = (int)Math.Ceiling(pageRange / 2.0);
                // 奇数个页码：页码尽量显示在中间
                if (pageRange % 2 == 1)
                {
                    if (this.current < half)
                    {
                        this.pagesInRange = Enumerable.Range(1, pageRange);
                    }
                    else if (this.current + half > this.pageCount)
                    {
                        int start = this.pageCount - pageRange + 1;
                        this.pagesInRange = Enumerable.Range(start, pageRange);
                    }
                    else
                    {
                        int start = this.current - half + 1;
                        this.pagesInRange = Enumerable.Range(start, pageRange);
                    }
                }
                // 偶数个页码：页码尽量显示在中间后面的位置
                else
                {
                    if (this.current - half - 1 < 0)
                    {
                        this.pagesInRange = Enumerable.Range(1, pageRange);
                    }
                    else if (this.current + half > this.pageCount)
                    {
                        int start = this.pageCount - pageRange + 1;
                        this.pagesInRange = Enumerable.Range(start, pageRange);
                    }
                    else
                    {
                        int start = this.current - half;
                        this.pagesInRange = Enumerable.Range(start, pageRange);
                    }
                }
            }
        }

        //首页，尾页，上一页，下一页
        private void createLabel(Dictionary<string, string> @params)
        {
            if (@params != null)
            {
                if (@params.ContainsKey("firstPageLabel"))
                    this.firstPageLabel = @params["firstPageLabel"];
                if (@params.ContainsKey("lastPageLabel"))
                    this.lastPageLabel = @params["lastPageLabel"];
                if (@params.ContainsKey("prevPageLabel"))
                    this.prevPageLabel = @params["prevPageLabel"];
                if (@params.ContainsKey("nextPageLabel"))
                    this.nextPageLabel = @params["nextPageLabel"];
            }
        }

        //创建页码链接
        public string createPageLinks()
        {
            StringBuilder html = new StringBuilder();
            if (this.first < this.current)
            {
                html.AppendFormat("<a href='{0}?page=1'>{1}</a>&nbsp", this.uri, this.firstPageLabel);
                html.AppendFormat("<a href='{0}?page={1}'>{2}</a>&nbsp;", this.uri, this.current - 1, this.prevPageLabel);
            }
            foreach (int page in pagesInRange)
            {
                if (this.current == page)
                    html.AppendFormat("<span>{0}</span>&nbsp;", this.current);
                else
                    html.AppendFormat("<a href='{0}?page={1}'>{2}</a>&nbsp;", this.uri, page, page);
            }
            if (this.last > this.current)
            {
                html.AppendFormat("<a href='{0}?page={1}'>{2}</a>&nbsp;", this.uri, this.current + 1, this.nextPageLabel);
                html.AppendFormat("<a href='{0}?page={1}'>{2}</a>&nbsp;", this.uri, this.pageCount, this.lastPageLabel);
            }
            return html.ToString();
        }

        //创建页码链接
        public string createPageLinksWithStyle()
        {
            StringBuilder html = new StringBuilder();
            html.Append("<ul class='pagination'>");
            if (this.first < this.current)
            {
                html.Append("<li><a href='" + this.uri + "?page=1'>" + this.firstPageLabel + "</a>&nbsp;</li>");
                html.Append("<li><a href='" + this.uri + "?page=" + (this.current - 1) + "'>" + this.prevPageLabel + "</a>&nbsp;</li>");
            }
            foreach (int page in pagesInRange)
            {
                if (this.current == page)
                    html.Append("<li class='active'><a href='javascript:void;'>" + this.current + "&nbsp;</a></li>");
                else
                    html.Append("<li><a href='" + this.uri + "?page=" + page + "'>" + page + "</a>&nbsp;</li>");
            }
            if (this.last > this.current)
            {
                html.Append("<li class='page'><a href='" + this.uri + "?page=" + (this.current + 1) + "'>" + this.nextPageLabel + "</a>&nbsp;</li>");
                html.Append("<li class='page'><a href='" + this.uri + "?page=" + this.pageCount + "'>" + this.lastPageLabel + "</a>&nbsp;</li>");
            }
            html.Append("</ul>");
            return html.ToString();
        }

        //当前页
        public int getCurrent()
        {
            return this.current;
        }

        //总页数
        public int getPageCount()
        {
            return this.pageCount;
        }
    }
}
