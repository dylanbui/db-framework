<?php  if ( ! defined('__SITE_PATH')) exit('No direct script access allowed');

define('_FB_APP_ID', '534956693276758');
define('_FB_APP_SECRET', '2e23283d32b30b1c53e978b68d32c40b');

define('_TB_PREFIX', "z__");

// --- -------------------------------------------------------------------------------------------------- ---//
// --- CMS TABLE ---//

define('TB_MAIN_CATS', _TB_PREFIX . 'main_category');
define('TB_USER', _TB_PREFIX . 'user');
define('TB_USER_GROUP', _TB_PREFIX . 'user_group');
define('TB_CONTENTS', _TB_PREFIX . 'content');
define('TB_CONTENT_CATS', _TB_PREFIX . 'content_cat');
define('TB_PRODUCTS', _TB_PREFIX . 'product');
define('TB_PRODUCT_CATS', _TB_PREFIX . 'product_cat');
define('TB_GALLERY', _TB_PREFIX . 'gallery');
// define('TB_CONFIGURES', _TB_PREFIX . 'configure');
// define('TB_CONFIGURE_CATS', _TB_PREFIX . 'configure_cat');
define('TB_CONFIGURE_SYSTEM', _TB_PREFIX . 'configure');
define('TB_CONFIGURE_SYSTEM_GROUP', _TB_PREFIX . 'configure_group');
define('TB_LANGUAGE', _TB_PREFIX . 'language');
define('TB_MEMBER', _TB_PREFIX . 'member');
define('TB_CONTACT', _TB_PREFIX . 'contact');

define('TB_URL_ALIAS', _TB_PREFIX . 'url_alias');

// --- -------------------------------------------------------------------------------------------------- ---//
// --- PAGE MODULE TABLE ---//

define('TB_PAGE_CONTENT', _TB_PREFIX . 'page_content');
define('TB_PAGE_CONTENT_LN', _TB_PREFIX . 'page_content_ln');
define('TB_PAGE_CONTENT_OPTIONS', _TB_PREFIX . 'page_content_options');
define('TB_PAGE_CATEGORY', _TB_PREFIX . 'page_category');
define('TB_PAGE_CATEGORY_LN', _TB_PREFIX . 'page_category_ln');
define('TB_PAGE_CATEGORY_PATH', _TB_PREFIX . 'page_category_path');
define('TB_PAGE_CONFIGURE', _TB_PREFIX . 'page_configure');
define('TB_PAGE_GALLERY', _TB_PREFIX . 'page_gallery');

// --- -------------------------------------------------------------------------------------------------- ---//
// --- EXAMPLE MODULE TABLE ---//

define('TB_EX_USER', _TB_PREFIX . '_ex_user');
define('TB_EX_ADVERTISING', _TB_PREFIX . '_ex_advertising');
define('TB_EX_CONTENT', _TB_PREFIX . '_ex_content');
define('TB_EX_CONTENT_CAT', _TB_PREFIX . '_ex_content_cat');
define('TB_EX_CONTENT_CAT_PATH', _TB_PREFIX . '_ex_content_cat_path');
define('TB_EX_PRODUCT', _TB_PREFIX . '_ex_product');
define('TB_EX_PRODUCT_CAT', _TB_PREFIX . '_ex_product_cat');

