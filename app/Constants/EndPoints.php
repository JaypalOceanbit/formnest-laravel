<?php


namespace App\Constants;


class EndPoints
{
    /**
     * ========================================================================
     * Users Services
     * ========================================================================
     */
    const user_register = '/user/register';
    const user_login = '/user/login';
    const user_forgotPassword = '/user/forgotPassword';
    const user_forgotPasswordCustom = '/user/forgotPasswordCustom';
    const user_adminUsers = '/user/adminUsers';
    const user_detail = '/user/detail';
    const user_profile = '/user/profile';
    const user_changePassword = '/user/changePassword';
    const user_updateProfile = '/user/updateProfile';
    const user_logout = '/user/logout';
    const user_list = '/user/list';
    const user_activeUsers = '/user/activeUsers';
    const user_newUsers = '/user/newUsers';
    const user_blockUsers = '/user/blockUsers';


    /**
     * ========================================================================
     * Middeleware Route
     * ========================================================================
     */
    const unauthorised = 'unauthorised';
    const adminaccess = 'adminaccess';
    const activeaccess = 'activeaccess';
    const password_reset = 'password/reset';
    const password_update = 'password/update';

    /**
     * ========================================================================
     * Master Field Types Services
     * ========================================================================
     */
    const master_field_type_list = '/master-field-type/list';
    const master_field_type_add = '/master-field-type/add';
    const master_field_type_detail = '/master-field-type/detail';
    const master_field_type_update = '/master-field-type/update';
    const master_field_type_delete = '/master-field-type/delete';
    const master_field_type_change_status = '/master-field-type/change-status';

    /**
     * ========================================================================
     * Master Template Categories Services
     * ========================================================================
     */
    const master_template_category_list = '/master-template-category/list';
    const master_template_category_add = '/master-template-category/add';
    const master_template_category_detail = '/master-template-category/detail';
    const master_template_category_update = '/master-template-category/update';
    const master_template_category_delete = '/master-template-category/delete';
    const master_template_category_change_status = '/master-template-category/change-status';

    /**
     * ========================================================================
     * Master Templates Services
     * ========================================================================
     */
    const master_template_list = '/master-template/list';
    const master_template_add = '/master-template/add';
    const master_template_detail = '/master-template/detail';
    const master_template_update = '/master-template/update';
    const master_template_delete = '/master-template/delete';
    const master_template_duplicate = '/master-template/duplicate';
    const master_template_change_status = '/master-template/change-status';

}
