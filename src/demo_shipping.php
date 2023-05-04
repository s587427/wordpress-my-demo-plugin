<?php

//new zone

$a = new WC_Shipping_Zone();

$a->set_zone_name('test');

$a->save();





//get zone

$data_store = WC_Data_Store::load('shipping-zone');

foreach ($data_store->get_zones() as $raw_zone) {

    $zones[$raw_zone->zone_id] = new WC_Shipping_Zone($raw_zone->zone_id);
}

$zones[] = new WC_Shipping_Zone(0);

//id 0 is default zone that contoury not in list



$t = new WC_Shipping_Zone(2);



$zone_id = $zone->get_id();

$zone_name = $zone->get_zone_name();

$zone_order = $zone->get_zone_order();

$zone_locations = $zone->get_zone_locations();

$zone_formatted_location = $zone->get_formatted_location();

$zone_shipping_methods = $zone->get_shipping_methods();



//get shipping method for zone

$zone_shipping_methods = $zone->get_shipping_methods();

foreach ($zone_shipping_methods as $index => $method) {

    $method_is_taxable = $method->is_taxable();

    $method_is_enabled = $method->is_enabled();

    $method_instance_id = $method->get_instance_id();

    $method_title = $method->get_method_title(); // e.g. "Flat Rate"

    $method_description = $method->get_method_description();

    $method_user_title = $method->get_title(); // e.g. whatever you renamed "Flat Rate" into

    $method_rate_id = $method->get_rate_id(); // e.g. "flat_rate:18"

    $instance = $method->instance_settings;

    $cost = $instance['cost'];
}




//get all shipping method

$wc_shipping = WC_Shipping::instance();

$wc_shipping->get_shipping_method_class_names();



//get instance for OLT csutom shipment method

$shipping_instance = new OLT_Custom_Shipment_Method($method_instance_id);
