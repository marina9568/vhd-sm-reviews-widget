<?php
class vhd_sm_reviews_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'vhd_sm_reviews_widget',


            __( 'Vet Help Direct API Reviews Widget', 'vhd_sm_reviews_widget_domain' ),


            array( 'description' => __( 'Displays reviews via Vet Help Direct API', 'vhd_sm_reviews_widget_domain' ), )
        );
    }
    
    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );
        
        $params = array (
            'api_url'     => apply_filters( 'widget_api_url', $instance['api_url'] ),
            'api_key'     => apply_filters( 'widget_api_key', $instance['api_key'] ),
            'max_items'   => apply_filters( 'widget_max_items', $instance['max_items'] )
        );
        
        
        echo $args['before_widget'];
        if ( !empty( $title ) ) {
            echo '<h2 class="widget-title">' . $title . '</h2>' . $args['after_title'];
        }
        
        $vhd_sm_dw = new vhd_sm_display_reviews();
        
        
        $content = $vhd_sm_dw->get_reviews_content( $params );
        
        echo __( $content, 'vhd_sm_reviews_widget_domain' );
        
        echo $args['after_widget'];
    }
    
    public function form( $instance ) {
        $title     = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __( 'Clients Reviews', 'vhd_sm_reviews_widget_domain' );
        $api_url   = isset( $instance[ 'api_url' ] ) ? $instance[ 'api_url' ] : __( 'http://vethelpdirect.com/dashboard/api', 'vhd_sm_reviews_widget_domain' );
        $api_key   = isset( $instance[ 'api_key' ] ) ? $instance[ 'api_key' ] : __( '', 'vhd_sm_reviews_widget_domain' );
        $max_items = isset( $instance[ 'max_items' ] ) ? $instance[ 'max_items' ] : __( '5', 'vhd_sm_reviews_widget_domain' );
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'api_url' ); ?>"><?php _e( 'API URL:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'api_url' ); ?>" name="<?php echo $this->get_field_name( 'api_url' ); ?>" type="text" value="<?php echo esc_attr( $api_url ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'api_key' ); ?>"><?php _e( 'API Key:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'api_key' ); ?>" name="<?php echo $this->get_field_name( 'api_key' ); ?>" type="text" value="<?php echo esc_attr( $api_key ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'max_items' ); ?>"><?php _e( 'Max reviews quantity:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'max_items' ); ?>" name="<?php echo $this->get_field_name( 'max_items' ); ?>" type="number" value="<?php echo esc_attr( $max_items ); ?>" />
        </p>
        <?php
    }
        
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title']       = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['api_url']     = ( ! empty( $new_instance['api_url'] ) ) ? strip_tags( $new_instance['api_url'] ) : '';
        $instance['api_key']     = ( ! empty( $new_instance['api_key'] ) ) ? strip_tags( $new_instance['api_key'] ) : '';
        $instance['max_items']   = ( ! empty( $new_instance['max_items'] ) ) ? strip_tags( $new_instance['max_items'] ) : '';
        $instance['is_autoplay'] = ( ! empty( $new_instance['is_autoplay'] ) ) ? strip_tags( $new_instance['is_autoplay'] ) : '';
        
        
        return $instance;
    }
}

class vhd_sm_display_reviews {
    
    public function get_api_data( $params ) {
        $result = wp_cache_get('vhd_reviews_' . $params['api_key']);
        
        if (! $result) {
            $url = trailingslashit( $params['api_url'] ) . 'fetch-review-data/' . $params['api_key'] . '/' . $params['max_items'];
            $ch = curl_init( $url );
            curl_setopt_array( $ch, array(
                CURLOPT_RETURNTRANSFER => TRUE
            ) );
            $response = curl_exec( $ch );
            $result = json_decode( $response, TRUE );
            
            wp_cache_set('vhd_reviews_' . $params['api_key'], $result);
        }

        return $result;
    }
    
    public function get_slider( $data, $id ) {
        $slider = '';
        if ( ! empty ( $data['review_data']['reviews'] ) && $data['review_data']['numberOfReviews'] > 0 ) {
            
            $slider = '<div id="' . $id . '" class="owl-carousel owl-theme">';

            foreach ( $data['review_data']['reviews'] as $review ) {
                $slider .= '<div class="vhd_review">'
                        . $this->get_stars( $data['assets'], $review['stars'] )
                        . '<div class="vhd_review_text">'
                        . $review['text']
                        . '</div>'
                        . '<div class="vhd_review_name">' . $review['display_name'] . '</div>'
                        . '<div class="vhd_review_date">' . $review['date'] . '</div>'
                        . '</div>';
            }
            
            $slider .= '</div>';
            $slider .= '<script>jQuery(document).ready(function($){
              $(".owl-carousel#' . $id . '").owlCarousel({loop:true, items:1, autoplay:true, autoplayHoverPause:true, autoplaySpeed:400});
            })</script>';
        } else {
            $slider .= '<div class="vhd_review"><div class="vhd_review_text">There are no reviews yet.</div></div>';
        }

        return $slider;
    }
    
    public function get_reviews_content( $params ) {
        $id = substr( md5( rand() ), 2, 6 );
        
        $content = '<div class="vhd_review_content">';
        
        $data = $this->get_api_data( $params );
        
        if (empty($data) || empty($data['assets'])) {
            return "";
        }
        $logo = '<div class="vhd_review_logo"><a target="_blank" href="'
                . $data['assets']['vhdUrl'] . '"><img src="'
                . $data['assets']['logoSmall'] . '" alt="'
                . $data['assets']['vhdUrl'] . '"></a></div>';
        $slider = $this->get_slider( $data, $id );
        
        $links = '';
        if ( ! $this->is_group( $data ) ) {
            $links = '<div class="vhd_review_links">'
                    . '<a target="_blank" class="vhd_review_link vhd_review_read_more" href="'
                    . $data['assets']['vhdUrl'] . $data['links']['practice_page']
                    . '#startofreviews">Read More</a>'
                    . '<a target="_blank" class="vhd_review_link vhd_review_add" href="'
                    . $data['links']['write_review'] . '&formid=7&color=ffffff&color2=ffffff'
                    . '">Add Review</a></div>';
        } else {
            $links = '<div class="vhd_review_links">'
                    . '<a class="vhd_review_link vhd_review_link_popup" data-type="more" data-popup="' . $id . '" href="#">Read More</a>'
                    . '<a class="vhd_review_link vhd_review_link_popup" data-type="add" data-popup="' . $id . '" href="#">Add Review</a></div>';
            
            $links.= '<div class="vhd_review_popup ' . $id . '">'
                    . '<div class="vhd_review_popup-content">'
                    . '<p class="vhd_review_popup-title">Which practice?</p>'
                    . '<div class="vhd_review_popup_select">';
            
            $options_more = '';
            $options_add = '';
            foreach ($data as $item) {
                if ( !empty($item['links']) && !empty($item['practice_details'])) {
                    $options_more .= '<option value="' . $data['assets']['vhdUrl'] . $item['links']['practice_page'] . '">' . $item['practice_details']['name'] . '</option>';
                    $options_add .= '<option value="' . $item['links']['write_review'] . '&formid=7&color=ffffff&color2=ffffff">' . $item['practice_details']['name'] . '</option>';
                }
            }

            $links.= '<select class="vhd_review_popup_select_more">' . $options_more . '</select>'
                    . '<select class="vhd_review_popup_select_add">' . $options_add . '</select>'
                    . '</div>'
                    . '<a href="" target="_blank" class="vhd_review_link vhd_review_popup_continue">Continue</a>'
                    . '<a href="" class="vhd_review_link vhd_review_popup_close">Close</a>'
                    . '</div>'
                    . '</div>';
        }
        
        $content .= $logo . $slider . $links;
        
        $content .= '</div>';
        return $content;
    }
    
    public function is_group( $data ) {
        return empty ( $data['links'] ) || empty ( $data['practice_details'] );
    }
    
    public function get_stars( $assets, $quantity ) {
        $stars = '<div class="vhd_review_stars">';
        for ( $i = 0; $i < 5; $i++ ) {
            $src = $i < $quantity ? $assets['starGold'] : $assets['starGrey'];
            $stars .= '<img src=' . $src . '/>';
        }
        $stars .= '</div>';
        return $stars;
    }
    
}
