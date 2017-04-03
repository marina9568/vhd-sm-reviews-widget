jQuery(document).ready(function($) {
    $('.vhd_review_link_popup').on('click', function(e) {
        e.preventDefault();
        
        
        var id = $(this).data('popup');
        var type = $(this).data('type');
        
        var $popup = $('.vhd_review_popup.' + id);
        var $select = $popup.find('.vhd_review_popup_select_' + type);
        $select.show();
        
        var $link = $popup.find('.vhd_review_popup_continue');
        
        var url = $select.val();
        
        setLink($link, url);
        
        $popup.show('100');
    });
    
    
    $('.vhd_review_popup_close').on('click', function(e) {
        e.preventDefault();
        $('.vhd_review_popup').hide('100');
        $('.vhd_review_popup_select_add, .vhd_review_popup_select_more').hide();
    });
    
    function setLink($link, url) {
        $link.attr('href', url);
    }
    
    $('.vhd_review_popup_select>select').on('change', function() {
        $(this).each(function() {
            var url = $(this).val();
            var $popup = $(this).parent().parent();
            var $link = $popup.find('.vhd_review_popup_continue');
            setLink($link, url);
        });
    });
    
});