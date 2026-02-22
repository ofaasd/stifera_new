<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class NexadashController extends Controller
{
    public function dashboard(){
		$CurrentPage = 'index';
		return view('index', compact('CurrentPage'));
	}
    public function dashboard_2(){
        $CurrentPage = 'index-2';
        return view('index-2', compact('CurrentPage'));
    }
    public function dashboard_3(){
        $CurrentPage = 'index-3';
        return view('index-3', compact('CurrentPage'));
    }
    public function dashboard_4(){
        $CurrentPage = 'index-4';
        return view('index-4', compact('CurrentPage'));
    }
	public function add_blog() {
	$CurrentPage = 'add-blog';
	return view('add-blog', compact('CurrentPage'));
	}

	public function add_categary() {
	$CurrentPage = 'add-categary';
	return view('add-categary', compact('CurrentPage'));
	}

	public function add_email() {
	$CurrentPage = 'add-email';
	return view('add-email', compact('CurrentPage'));
	}

	public function add_product() {
	$CurrentPage = 'add-product';
	return view('add-product', compact('CurrentPage'));
	}

	public function analytics() {
	$CurrentPage = 'analytics';
	return view('analytics', compact('CurrentPage'));
	}

	public function app_calender() {
	$CurrentPage = 'app-calender';
	return view('app-calender', compact('CurrentPage'));
	}

	public function app_profile() {
	$CurrentPage = 'app-profile';
	return view('app-profile', compact('CurrentPage'));
	}

	public function app_profile_1() {
	$CurrentPage = 'app-profile-1';
	return view('app-profile-1', compact('CurrentPage'));
	}

	public function app_profile_2() {
	$CurrentPage = 'app-profile-2';
	return view('app-profile-2', compact('CurrentPage'));
	}

	public function blog_category() {
	$CurrentPage = 'blog-category';
	return view('blog-category', compact('CurrentPage'));
	}

	public function category_table() {
	$CurrentPage = 'category-table';
	return view('category-table', compact('CurrentPage'));
	}

	public function chart_chartist() {
	$CurrentPage = 'chart-chartist';
	return view('chart-chartist', compact('CurrentPage'));
	}

	public function chart_chartjs() {
	$CurrentPage = 'chart-chartjs';
	return view('chart-chartjs', compact('CurrentPage'));
	}

	public function chart_flot() {
	$CurrentPage = 'chart-flot';
	return view('chart-flot', compact('CurrentPage'));
	}

	public function chart_morris() {
	$CurrentPage = 'chart-morris';
	return view('chart-morris', compact('CurrentPage'));
	}

	public function chart_peity() {
	$CurrentPage = 'chart-peity';
	return view('chart-peity', compact('CurrentPage'));
	}

	public function chart_sparkline() {
	$CurrentPage = 'chart-sparkline';
	return view('chart-sparkline', compact('CurrentPage'));
	}

	public function chat() {
	$CurrentPage = 'chat';
	return view('chat', compact('CurrentPage'));
	}

	public function cms_blog() {
	$CurrentPage = 'cms-blog';
	return view('cms-blog', compact('CurrentPage'));
	}

	public function content() {
	$CurrentPage = 'content';
	return view('content', compact('CurrentPage'));
	}

	public function content_add() {
	$CurrentPage = 'content-add';
	return view('content-add', compact('CurrentPage'));
	}

	public function course() {
	$CurrentPage = 'course';
	return view('course', compact('CurrentPage'));
	}

	public function crm() {
	$CurrentPage = 'crm';
	return view('crm', compact('CurrentPage'));
	}

	public function crypto() {
	$CurrentPage = 'crypto';
	return view('crypto', compact('CurrentPage'));
	}

	public function ecom_checkout() {
	$CurrentPage = 'ecom-checkout';
	return view('ecom-checkout', compact('CurrentPage'));
	}

	public function ecom_customers() {
	$CurrentPage = 'ecom-customers';
	return view('ecom-customers', compact('CurrentPage'));
	}

	public function ecom_invoice() {
	$CurrentPage = 'ecom-invoice';
	return view('ecom-invoice', compact('CurrentPage'));
	}

	public function ecommerce() {
	$CurrentPage = 'ecommerce';
	return view('ecommerce', compact('CurrentPage'));
	}

	public function ecom_product_detail() {
	$CurrentPage = 'ecom-product-detail';
	return view('ecom-product-detail', compact('CurrentPage'));
	}

	public function ecom_product_grid() {
	$CurrentPage = 'ecom-product-grid';
	return view('ecom-product-grid', compact('CurrentPage'));
	}

	public function ecom_product_list() {
	$CurrentPage = 'ecom-product-list';
	return view('ecom-product-list', compact('CurrentPage'));
	}

	public function ecom_product_order() {
	$CurrentPage = 'ecom-product-order';
	return view('ecom-product-order', compact('CurrentPage'));
	}

	public function edit_categary() {
	$CurrentPage = 'edit-categary';
	return view('edit-categary', compact('CurrentPage'));
	}

	public function edit_product() {
	$CurrentPage = 'edit-product';
	return view('edit-product', compact('CurrentPage'));
	}

	public function edit_profile() {
	$CurrentPage = 'edit-profile';
	return view('edit-profile', compact('CurrentPage'));
	}

	public function email_compose() {
	$CurrentPage = 'email-compose';
	return view('email-compose', compact('CurrentPage'));
	}

	public function email_inbox() {
	$CurrentPage = 'email-inbox';
	return view('email-inbox', compact('CurrentPage'));
	}

	public function email_read() {
	$CurrentPage = 'email-read';
	return view('email-read', compact('CurrentPage'));
	}

	public function email_template() {
	$CurrentPage = 'email-template';
	return view('email-template', compact('CurrentPage'));
	}

	public function empty_page() {
	$CurrentPage = 'empty-page';
	return view('empty-page', compact('CurrentPage'));
	}

	public function error_404() {
	$CurrentPage = 'error-404';
	return view('error-404', compact('CurrentPage'));
	}

	public function finance() {
	$CurrentPage = 'finance';
	return view('finance', compact('CurrentPage'));
	}

	public function form_ckeditor() {
	$CurrentPage = 'form-ckeditor';
	return view('form-ckeditor', compact('CurrentPage'));
	}

	public function form_element() {
	$CurrentPage = 'form-element';
	return view('form-element', compact('CurrentPage'));
	}

	public function form_pickers() {
	$CurrentPage = 'form-pickers';
	return view('form-pickers', compact('CurrentPage'));
	}

	public function form_validation() {
	$CurrentPage = 'form-validation';
	return view('form-validation', compact('CurrentPage'));
	}

	public function form_wizard() {
	$CurrentPage = 'form-wizard';
	return view('form-wizard', compact('CurrentPage'));
	}

	public function map_jqvmap() {
	$CurrentPage = 'map-jqvmap';
	return view('map-jqvmap', compact('CurrentPage'));
	}

	public function medical() {
	$CurrentPage = 'medical';
	return view('medical', compact('CurrentPage'));
	}

	public function menu() {
	$CurrentPage = 'menu';
	return view('menu', compact('CurrentPage'));
	}

	public function my_project() {
	$CurrentPage = 'my-project';
	return view('my-project', compact('CurrentPage'));
	}

	public function page_error_400() {
	$CurrentPage = 'page-error-400';
	return view('page-error-400', compact('CurrentPage'));
	}

	public function page_error_403() {
	$CurrentPage = 'page-error-403';
	return view('page-error-403', compact('CurrentPage'));
	}

	public function page_error_404() {
	$CurrentPage = 'page-error-404';
	return view('page-error-404', compact('CurrentPage'));
	}

	public function page_error_500() {
	$CurrentPage = 'page-error-500';
	return view('page-error-500', compact('CurrentPage'));
	}

	public function page_error_503() {
	$CurrentPage = 'page-error-503';
	return view('page-error-503', compact('CurrentPage'));
	}

	public function page_forgot_password() {
	$CurrentPage = 'page-forgot-password';
	return view('page-forgot-password', compact('CurrentPage'));
	}

	public function page_lock_screen() {
	$CurrentPage = 'page-lock-screen';
	return view('page-lock-screen', compact('CurrentPage'));
	}

	public function page_login() {
	$CurrentPage = 'page-login';
	return view('page-login', compact('CurrentPage'));
	}

	public function page_register() {
	$CurrentPage = 'page-register';
	return view('page-register', compact('CurrentPage'));
	}

	public function post_details() {
	$CurrentPage = 'post-details';
	return view('post-details', compact('CurrentPage'));
	}

	public function product_table() {
	$CurrentPage = 'product-table';
	return view('product-table', compact('CurrentPage'));
	}

	public function table_bootstrap_basic() {
	$CurrentPage = 'table-bootstrap-basic';
	return view('table-bootstrap-basic', compact('CurrentPage'));
	}

	public function table_datatable_basic() {
	$CurrentPage = 'table-datatable-basic';
	return view('table-datatable-basic', compact('CurrentPage'));
	}

	public function uc_lightgallery() {
	$CurrentPage = 'uc-lightgallery';
	return view('uc-lightgallery', compact('CurrentPage'));
	}

	public function uc_nestable() {
	$CurrentPage = 'uc-nestable';
	return view('uc-nestable', compact('CurrentPage'));
	}

	public function uc_noui_slider() {
	$CurrentPage = 'uc-noui-slider';
	return view('uc-noui-slider', compact('CurrentPage'));
	}

	public function uc_select2() {
	$CurrentPage = 'uc-select2';
	return view('uc-select2', compact('CurrentPage'));
	}

	public function uc_sweetalert() {
	$CurrentPage = 'uc-sweetalert';
	return view('uc-sweetalert', compact('CurrentPage'));
	}

	public function uc_toastr() {
	$CurrentPage = 'uc-toastr';
	return view('uc-toastr', compact('CurrentPage'));
	}

	public function ui_accordion() {
	$CurrentPage = 'ui-accordion';
	return view('ui-accordion', compact('CurrentPage'));
	}

	public function ui_alert() {
	$CurrentPage = 'ui-alert';
	return view('ui-alert', compact('CurrentPage'));
	}

	public function ui_badge() {
	$CurrentPage = 'ui-badge';
	return view('ui-badge', compact('CurrentPage'));
	}

	public function ui_breadcrumb() {
	$CurrentPage = 'ui-breadcrumb';
	return view('ui-breadcrumb', compact('CurrentPage'));
	}

	public function ui_button() {
	$CurrentPage = 'ui-button';
	return view('ui-button', compact('CurrentPage'));
	}

	public function ui_button_group() {
	$CurrentPage = 'ui-button-group';
	return view('ui-button-group', compact('CurrentPage'));
	}

	public function ui_card() {
	$CurrentPage = 'ui-card';
	return view('ui-card', compact('CurrentPage'));
	}

	public function ui_carousel() {
	$CurrentPage = 'ui-carousel';
	return view('ui-carousel', compact('CurrentPage'));
	}

	public function ui_colors() {
	$CurrentPage = 'ui-colors';
	return view('ui-colors', compact('CurrentPage'));
	}

	public function ui_dropdown() {
	$CurrentPage = 'ui-dropdown';
	return view('ui-dropdown', compact('CurrentPage'));
	}

	public function ui_grid() {
	$CurrentPage = 'ui-grid';
	return view('ui-grid', compact('CurrentPage'));
	}

	public function ui_list_group() {
	$CurrentPage = 'ui-list-group';
	return view('ui-list-group', compact('CurrentPage'));
	}

	public function ui_media_object() {
	$CurrentPage = 'ui-media-object';
	return view('ui-media-object', compact('CurrentPage'));
	}

	public function ui_modal() {
	$CurrentPage = 'ui-modal';
	return view('ui-modal', compact('CurrentPage'));
	}

	public function ui_navbar() {
	$CurrentPage = 'ui-navbar';
	return view('ui-navbar', compact('CurrentPage'));
	}

	public function ui_object_fit() {
	$CurrentPage = 'ui-object-fit';
	return view('ui-object-fit', compact('CurrentPage'));
	}

	public function ui_offcanvas() {
	$CurrentPage = 'ui-offcanvas';
	return view('ui-offcanvas', compact('CurrentPage'));
	}

	public function ui_pagination() {
	$CurrentPage = 'ui-pagination';
	return view('ui-pagination', compact('CurrentPage'));
	}

	public function ui_placeholder() {
	$CurrentPage = 'ui-placeholder';
	return view('ui-placeholder', compact('CurrentPage'));
	}

	public function ui_popover() {
	$CurrentPage = 'ui-popover';
	return view('ui-popover', compact('CurrentPage'));
	}

	public function ui_progressbar() {
	$CurrentPage = 'ui-progressbar';
	return view('ui-progressbar', compact('CurrentPage'));
	}

	public function ui_range_slider() {
	$CurrentPage = 'ui-range-slider';
	return view('ui-range-slider', compact('CurrentPage'));
	}

	public function ui_scrollspy() {
	$CurrentPage = 'ui-scrollspy';
	return view('ui-scrollspy', compact('CurrentPage'));
	}

	public function ui_spinners() {
	$CurrentPage = 'ui-spinners';
	return view('ui-spinners', compact('CurrentPage'));
	}

	public function ui_tab() {
	$CurrentPage = 'ui-tab';
	return view('ui-tab', compact('CurrentPage'));
	}

	public function ui_toasts() {
	$CurrentPage = 'ui-toasts';
	return view('ui-toasts', compact('CurrentPage'));
	}

	public function ui_typography() {
	$CurrentPage = 'ui-typography';
	return view('ui-typography', compact('CurrentPage'));
	}

	public function widget_basic() {
	$CurrentPage = 'widget-basic';
	return view('widget-basic', compact('CurrentPage'));
	}
	public function activity() {
	$CurrentPage = 'activity';
	return view('account.activity', compact('CurrentPage'));
	}

	public function api_keys() {
	$CurrentPage = 'api-keys';
	return view('account.api-keys', compact('CurrentPage'));
	}

	public function billing() {
	$CurrentPage = 'billing';
	return view('account.billing', compact('CurrentPage'));
	}

	public function logs() {
	$CurrentPage = 'logs';
	return view('account.logs', compact('CurrentPage'));
	}

	public function overview() {
	$CurrentPage = 'overview';
	return view('account.overview', compact('CurrentPage'));
	}

	public function referrals() {
	$CurrentPage = 'referrals';
	return view('account.referrals', compact('CurrentPage'));
	}

	public function security() {
	$CurrentPage = 'security';
	return view('account.security', compact('CurrentPage'));
	}

	public function settings() {
	$CurrentPage = 'settings';
	return view('account.settings', compact('CurrentPage'));
	}

	public function statements() {
	$CurrentPage = 'statements';
	return view('account.statements', compact('CurrentPage'));
	}
	
	public function activity_profile() {
	$CurrentPage = 'activity-profile';
	return view('profile.activity-profile', compact('CurrentPage'));
	}

	public function campaigns() {
	$CurrentPage = 'campaigns';
	return view('profile.campaigns', compact('CurrentPage'));
	}

	public function documents() {
	$CurrentPage = 'documents';
	return view('profile.documents', compact('CurrentPage'));
	}

	public function followers() {
	$CurrentPage = 'followers';
	return view('profile.followers', compact('CurrentPage'));
	}

	public function overview_profile() {
	$CurrentPage = 'overview-profile';
	return view('profile.overview-profile', compact('CurrentPage'));
	}

	public function projects() {
	$CurrentPage = 'projects';
	return view('profile.projects', compact('CurrentPage'));
	}

	public function projects_details() {
	$CurrentPage = 'projects-details';
	return view('profile.projects-details', compact('CurrentPage'));
	}

	public function auto_write() {
	$CurrentPage = 'auto-write';
	return view('aikit.auto-write', compact('CurrentPage'));
	}

	public function chatbot() {
	$CurrentPage = 'chatbot';
	return view('aikit.chatbot', compact('CurrentPage'));
	}

	public function fine_tune_models() {
	$CurrentPage = 'fine-tune-models';
	return view('aikit.fine-tune-models', compact('CurrentPage'));
	}

	public function import() {
	$CurrentPage = 'import';
	return view('aikit.import', compact('CurrentPage'));
	}

	public function prompt() {
	$CurrentPage = 'prompt';
	return view('aikit.prompt', compact('CurrentPage'));
	}

	public function repurpose() {
	$CurrentPage = 'repurpose';
	return view('aikit.repurpose', compact('CurrentPage'));
	}

	public function rss() {
	$CurrentPage = 'rss';
	return view('aikit.rss', compact('CurrentPage'));
	}

	public function scheduled() {
	$CurrentPage = 'scheduled';
	return view('aikit.scheduled', compact('CurrentPage'));
	}

	public function setting() {
	$CurrentPage = 'setting';
	return view('aikit.setting', compact('CurrentPage'));
	}

 }