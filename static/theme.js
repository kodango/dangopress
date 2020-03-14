/*
 * Javascript for dangopress theme
 */
function isHidden(el) {
    var style = window.getComputedStyle(el);
    return ((style.display === 'none') || (style.visibility === 'hidden'))
}

/*
 * Include comment-reply.js
 * See: https://codex.wordpress.org/Function_Reference/comment_reply_link
 */
var addComment = {
    moveForm: function( commId, parentId, respondId, postId ) {
        var div, element, style, cssHidden,
            t           = this,
            comm        = t.I( commId ),
            respond     = t.I( respondId ),
            cancel      = t.I( 'cancel-comment-reply-link' ),
            parent      = t.I( 'comment_parent' ),
            post        = t.I( 'comment_post_ID' ),
            commentForm = respond.getElementsByTagName( 'form' )[0];

        if ( ! comm || ! respond || ! cancel || ! parent || ! commentForm ) {
            return;
        }

        t.respondId = respondId;
        postId = postId || false;

        if ( ! t.I( 'wp-temp-form-div' ) ) {
            div = document.createElement( 'div' );
            div.id = 'wp-temp-form-div';
            div.style.display = 'none';
            respond.parentNode.insertBefore( div, respond );
        }

        comm.parentNode.insertBefore( respond, comm.nextSibling );
        if ( post && postId ) {
            post.value = postId;
        }
        parent.value = parentId;
        cancel.style.display = '';

        cancel.onclick = function() {
            var t       = addComment,
                temp    = t.I( 'wp-temp-form-div' ),
                respond = t.I( t.respondId );

            if ( ! temp || ! respond ) {
                return;
            }

            t.I( 'comment_parent' ).value = '0';
            temp.parentNode.insertBefore( respond, temp );
            temp.parentNode.removeChild( temp );
            this.style.display = 'none';
            this.onclick = null;
            return false;
        };

        /*
         * Set initial focus to the first form focusable element.
         * Try/catch used just to avoid errors in IE 7- which return visibility
         * 'inherit' when the visibility value is inherited from an ancestor.
         */
        try {
            for ( var i = 0; i < commentForm.elements.length; i++ ) {
                element = commentForm.elements[i];
                cssHidden = false;

                // Modern browsers.
                if ( 'getComputedStyle' in window ) {
                    style = window.getComputedStyle( element );
                // IE 8.
                } else if ( document.documentElement.currentStyle ) {
                    style = element.currentStyle;
                }

                /*
                 * For display none, do the same thing jQuery does. For visibility,
                 * check the element computed style since browsers are already doing
                 * the job for us. In fact, the visibility computed style is the actual
                 * computed value and already takes into account the element ancestors.
                 */
                if ( ( element.offsetWidth <= 0 && element.offsetHeight <= 0 ) || style.visibility === 'hidden' ) {
                    cssHidden = true;
                }

                // Skip form elements that are hidden or disabled.
                if ( 'hidden' === element.type || element.disabled || cssHidden ) {
                    continue;
                }

                element.focus();
                // Stop after the first focusable element.
                break;
            }

        } catch( er ) {}

        return false;
    },

    I: function( id ) {
        return document.getElementById( id );
    }
};

/**
 * @author: mg12 [http://www.neoease.com/]
 * @update: 2012/12/05
 */

SidebarFollow = function() {

    this.config = {
        element: null, // 处理的节点
        distanceToTop: 0 // 节点上边到页面顶部的距离
    };

    this.cache = {
        originalToTop: 0, // 原本到页面顶部的距离
        prevElement: null, // 上一个节点
        parentToTop: 0, // 父节点的上边到顶部距离
        placeholder: document.createElement('div') // 占位节点
    }
};

SidebarFollow.prototype = {

    init: function(config) {
        this.config = config || this.config;
        var _self = this;
        var element = document.getElementById(_self.config.element);
        var prevElement =  document.getElementById(_self.config.prevElement);

        // 如果没有找到节点, 不进行处理
        if(!element) {
            return;
        }

        // 获取上一个节点
        var prevElement = _self._getPrevElement(element);
        while(prevElement.offsetHeight < 0) {
            prevElement = _self._getPrevElement(prevElement);
            if(!prevElement) {
                break;
            }
        }
        _self.cache.prevElement = prevElement;

        // 计算父节点的上边到顶部距离
        var parent = element.parentNode;
        var parentToTop = _self._getCumulativeOffset(parent).top;
        var parentBorderTop = parseInt(parent.style.borderTop, 10);
        var parentPaddingTop = parseInt(parent.style.paddingTop, 10);
        _self.cache.parentToTop = parentToTop + parentBorderTop + parentPaddingTop;

        // 滚动屏幕
        _self._addListener(window, 'scroll', function() {
            _self._scrollScreen({element:element, prevElement:prevElement, _self:_self});
        });

        // 改变屏幕尺寸
        _self._addListener(window, 'resize', function() {
            _self._scrollScreen({element:element, prevElement:prevElement, _self:_self});
        });
    },

    /**
     * 修改节点位置
     */
    _scrollScreen: function(args) {
        var _self = args._self;
        var element = args.element;
        var prevElement = args.prevElement;
        var toTop = _self.config.distanceToTop;

        // 如果 body 有 top 属性, 消除这些位移
        var bodyToTop = parseInt(document.getElementsByTagName('body')[0].style.top, 10);
        if(!isNaN(bodyToTop)) {
            toTop += bodyToTop;
        }

        var elementToTop = 0;
        if(element.style.position === 'fixed') {
            elementToTop = _self._getScrollY();
        } else {
            elementToTop = _self._getCumulativeOffset(element).top - toTop;
        }
        var elementToPrev = _self._getCumulativeOffset(prevElement).top + _self._getVisibleSize(prevElement).height;

        // 当节点进入跟随区域, 跟随滚动
        if(_self._getScrollY() > elementToTop) {
            // 添加占位节点
            var elementHeight = _self._getVisibleSize(element).height;
            _self.cache.placeholder.style.height = elementHeight + 'px';
            element.parentNode.insertBefore(_self.cache.placeholder, element);
            // 记录原位置
            _self.cache.originalToTop = elementToTop;
            // 修改样式
            element.style.top = toTop + 'px';
            element.style.position = 'fixed';

        // 否则回到原位
        } else if(_self.cache.originalToTop > elementToTop || elementToPrev > elementToTop) {
            var parent = _self.cache.placeholder.parentNode;
            if(parent) {
                // 删除占位节点
                parent.removeChild(_self.cache.placeholder);
                // 修改样式
                element.style.position = 'static';
            }
        }
    },

    /**
     * 获取累计偏移量, 即元素到页面左上角的横行和纵向距离
     */
    _getCumulativeOffset: function(element) {
        var offset = {
            left:0,
            top:0
        };

        do {
            offset.left += element.offsetLeft || 0;
            offset.top += element.offsetTop  || 0;
            element = element.offsetParent;
        } while (element);

        return offset;
    },

    /**
     * 获取元素可见尺寸 (包括边线和滚动条)
     */
    _getVisibleSize: function(element) {
        var dimension = {
            width:0,
            height:0
        };

        dimension.width = element.offsetWidth;
        dimension.height = element.offsetHeight;

        return dimension;
    },

    /**
     * 获得滚动条纵向距离
     */
    _getScrollY: function() {
        if(typeof window.pageYOffset != 'undefined') {
            return window.pageYOffset;
        }

        if(typeof document.compatMode != 'undefined' && document.compatMode != 'BackCompat') {
            return document.documentElement.scrollTop;
        }

        return document.body.scrollTop;
    },

    /**
     * 添加监听事件
     */
    _addListener: function(node, type, listener) {
        if(node.addEventListener) {
            node.addEventListener(type, listener, false);
            return true;
        } else if(node.attachEvent) {
            node['e' + type + listener] = listener;
            node[type + listener] = function() {
                node['e' + type + listener](window.event);
            };
            node.attachEvent('on' + type, node[type + listener]);
            return true;
        }
        return false;
    },

    /**
     * 获取上一个节点
     */
    _getPrevElement: function(element) {
        var prev = element.previousSibling;
        while(prev.nodeType !== 1) {
            prev = prev.previousSibling;
        }
        return prev;
    }
};

function switchTab(event) {
    var tabs = this.parentNode.children;
    var selectedIndex = this.parentNode.dataset.selected;
        
    // Toggle the selected tab
    tabs[selectedIndex].classList.remove('selected');
    this.classList.add('selected');
    this.parentNode.dataset.selected = this.dataset.position;

    var contentParent = this.parentNode.parentNode.nextSibling;
    var contents = contentParent.children;
    selectedIndex = contentParent.dataset.selected;

    // Toggle the article list displayed
    contents[selectedIndex].classList.add('hide');
    contents[this.dataset.position].classList.remove('hide');
    contentParent.dataset.selected = this.dataset.position;
}

document.addEventListener('DOMContentLoaded', function(event) {
    var sf = new SidebarFollow();
    sf.init({ element: 'sidebar-follow', distanceToTop: 70 });

    /* Dynamically fill the google adsense ads */
    var ads = document.querySelectorAll('.adsbygoogle')
    ads.forEach(function (value, index) {
        if (isHidden(value))
            return; 

        (adsbygoogle = window.adsbygoogle || []).push({});
    });

    var tabs = document.querySelectorAll('#sidebar-main .tabnav li');
    tabs.forEach(function(tab) {
        tab.addEventListener('click', switchTab, true);
    });
});

document.addEventListener('click', function(event) {
    var t = event.target;

    if (t.className == 'comment-reply-link') {
        event.stopPropagation();
        event.preventDefault();
        addComment.moveForm("comment-" + t.dataset.commentid, t.dataset.commentid, "respond", t.dataset.postid);
    } else if (t.className == 'backtop') {
        event.stopPropagation();
        event.preventDefault();
        document.scrollingElement.scroll({ top: 0, left: 0, behavior: 'smooth' });
    } else if (t.className == 'comment-tab') {
        event.stopPropagation();
        event.preventDefault();

        var sibling = t.nextElementSibling || t.previousElementSibling;
        t.classList.add('selected');
        sibling.classList.remove('selected');
    } else if (t.id == 'toggle-author') {
        var formAuthor = document.querySelector('.comment-form-author')
        var formEmail = document.querySelector('.comment-form-email')
        var formUrl = document.querySelector('.comment-form-url')

        var author = document.getElementById('author').value;
        var welcomeAuthor = document.getElementById('welcome-login').querySelector('strong');

        if (isHidden(formAuthor)) {
            t.innerText = '隐藏';

            formAuthor.classList.remove('hide');
            formEmail.classList.remove('hide');
            formUrl.classList.remove('hide');
        } else {
            t.innerText = '更改';

            formAuthor.classList.add('hide');
            formEmail.classList.add('hide');
            formUrl.classList.add('hide');

            welcomeAuthor.innerText = author;
        }
    }
});
