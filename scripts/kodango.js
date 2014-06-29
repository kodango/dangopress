/*
 * Javascript for kodango.com
 */

/**
 * SidebarFollow
 *
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
        placeholder: jQuery('<div>') // 占位节点
    }
};

SidebarFollow.prototype = {

    init: function(config) {
        this.config = config || this.config;
        var _self = this;
        var element = jQuery(_self.config.element);

        // 如果没有找到节点, 不进行处理
        if(element.length <= 0) {
            return;
        }

        // 获取上一个节点
        var prevElement = element.prev();
        while(prevElement.is(':hidden')) {
            prevElement = prevElement.prev();
            if(prevElement.length <= 0) {
                break;
            }
        }
        _self.cache.prevElement = prevElement;

        // 计算父节点的上边到顶部距离
        var parent = element.parent();
        var parentToTop = parent.offset().top;
        var parentBorderTop = parseFloat(parent.css('border-top-width'), 10);
        var parentPaddingTop = parseFloat(parent.css('padding-top'), 10);
        _self.cache.parentToTop = parentToTop + parentBorderTop + parentPaddingTop;

        // 滚动屏幕
        jQuery(window).scroll(function() {
            _self._scrollScreen({element:element, _self:_self});
        });

        // 改变屏幕尺寸
        jQuery(window).resize(function() {
            _self._scrollScreen({element:element, _self:_self});
        });
    },

    /**
     * 修改节点位置
     */
    _scrollScreen: function(args) {
        var _self = args._self;
        var element = args.element;
        var prevElement = _self.cache.prevElement;

        if (element.is(':hidden'))
            return;

        // 获得到顶部的距离
        var toTop = _self.config.distanceToTop;

        // 如果 body 有 top 属性, 消除这些位移
        var bodyToTop = parseFloat(jQuery('body').css('top'), 10);
        if(!isNaN(bodyToTop)) {
            toTop += bodyToTop;
        }

        // 获得到顶部的绝对距离
        var elementToTop = element.offset().top - toTop;

        // 如果存在上一个节点, 获得到上一个节点的距离; 否则计算到父节点顶部的距离
        var referenceToTop = 0;
        if(prevElement && prevElement.length === 1) {
            referenceToTop = prevElement.offset().top + prevElement.outerHeight();
        } else {
            referenceToTop = _self.cache.parentToTop - toTop;
        }

        // 当节点进入跟随区域, 跟随滚动
        if(jQuery(document).scrollTop() > elementToTop) {
            // 添加占位节点
            var elementHeight = element.outerHeight();
            _self.cache.placeholder.css('height', elementHeight).insertBefore(element);
            // 记录原位置
            _self.cache.originalToTop = elementToTop;
            // 修改样式
            element.css({
                top: toTop + 'px',
                position: 'fixed'
            });

        // 否则回到原位
        } else if(_self.cache.originalToTop > elementToTop || referenceToTop > elementToTop) {
            // 删除占位节点
            _self.cache.placeholder.remove();
            // 修改样式
            element.css({
                position: 'static'
            });
        }
    }
};

/*
 * Initialize the function
 */
jQuery(function($) {
    /* scroll back to top */
    $(".backtop").click(function(event){
        event.preventDefault();
        $('html,body').animate({ scrollTop:0 }, 'fast');
    });


    /* Sidebar tabber widget */
    $('.tabber-title li').click(function() {
        if (this.className == 'selected')
             return;

        var $cur_tab = $(this);
        var $tabber = $cur_tab.parents('.widget-tabber');

        $cur_tab.addClass("selected")
                .siblings().removeClass("selected");

        $tabber.find('.tabber-content ul').slideUp('fast')
               .eq($tabber.find('.tabber-title li').index(this)).slideDown('fast');
    });

    /* Comment tabber */
    $('#comments-tabber a').on('click', function() {
         if (this.className == 'curtab')
             return;

         $(this).attr('class', 'curtab')
               .siblings().attr('class', 'tab');

         $('#comments-tabber').nextAll().toggle();
    });

    /* Sidebar follow */
    (new SidebarFollow()).init({
        element: '#sidebar-follow',
        distanceToTop: 70
    });

    /* Mobile menu switch */
    $('#mobile-menu').click(function() {
        var $header = $('#header');
        var $menu = $header.find('.header-menu');
        var $searchbox = $header.find('.search-box')

        $searchbox.fadeToggle();

        if ($header.hasClass('clicked')) {
            $menu.slideToggle(function() {
                $header.removeClass('clicked');
            });
        } else {
            $header.addClass('clicked');
            $menu.slideToggle();
        }
    });

    /* Toggle comment user */
    $('#comments').on('click', '#toggle-author', function () {
        $('#author-info').slideToggle(function () {
            if ($(this).is(':hidden')) {
                /* Update author name in the welcome messages */
                $('#welcome-login strong').html($('#author').val());

                /* Update the toggle action name */
                $('#toggle-author u').html('更改');
            } else {
                /* Update the toggle action name */
                $('#toggle-author u').html('隐藏');
            }
        });
    });
});
