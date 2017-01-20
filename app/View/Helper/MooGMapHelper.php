<?php
class MooGMapHelper extends AppHelper{
    public function loadGoogleMap($address = '', $width = 530, $height = 300,$isAjaxModal = false)
    {
//        $data =
//        'var map;'
//        .'var myLatlng;'
//        .'var geocoder = new google.maps.Geocoder();'
//        .'geocoder.geocode( { "address": "'.$address.'"}, function(results, status) {'
//            .'if (status == google.maps.GeocoderStatus.OK) {'
//            .'    myLatlng = new google.maps.LatLng(results[0].geometry.location.lat(),results[0].geometry.location.lng());'
//            .'}else{'
//            .'    myLatlng = new google.maps.LatLng(0,0);'
//            .'}'
//        .'});';
        //$this->_View->append('mooGmap','<script src="https://maps.google.com/maps/api/js?sensor=false"></script>');
        //$this->_View->Helpers->Html->scriptBlock($data,array('inline' => false,'block' => 'mooGmap'));
        $this->_View->viewVars['address'] = $address;
        $this->_View->viewVars['isAjaxModal'] = $isAjaxModal;
        $this->_View->loadLibarary('googleMap');
        return '<div id="map_canvas" style="width:'.$width.'px; height:'.$height.'px"></div>';

    }
}