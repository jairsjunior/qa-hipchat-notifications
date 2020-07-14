<?php

/*
  HipChat Notifications

  File: qa-plugin/hipchat-notifications/qa-hipchat-notifications-page.php
  Version: 0.1
  Date: 2014-02-25
  Description: Event module class for HipChat notifications plugin
*/

class qa_hipchat_notifications_page {

  function admin_form() {
    $saved=false;

    if (qa_clicked('general_save_button')) {
      // save the preferences
      $hipchat_enabled = qa_post_text('hipchat_notifications_notify_enabled');
      qa_opt('hipchat_notifications_notify_enabled', empty($hipchat_enabled) ? 0 : 1);
      qa_opt('hipchat_notifications_api_token', qa_post_text('hipchat_notifications_api_token'));
      qa_opt('hipchat_notifications_room_name', qa_post_text('hipchat_notifications_room_name'));
      qa_opt('hipchat_notifications_sender', qa_post_text('hipchat_notifications_sender'));
      qa_opt('hipchat_notifications_color', qa_post_text('hipchat_notifications_color'));

      $notify = qa_post_text('hipchat_notifications_notify');
      qa_opt('hipchat_notifications_notify', empty($notify) ? 0 : 1);

      $msteams_enabled = qa_post_text('ms_notifications_notify_enabled');
      qa_opt('ms_notifications_notify_enabled', empty($msteams_enabled) ? 0 : 1);
      qa_opt('ms_notifications_webhook_url', qa_post_text('ms_notifications_webhook_url'));
      qa_opt('ms_notifications_webhook_title', qa_post_text('ms_notifications_webhook_title'));
      
      $saved=true;
    }

    $form = array(
      'ok' => $saved ? 'HipChat Notification preferences saved' : null,

      'fields' => array(
        array(
          'type' => 'checkbox',
          'label' => 'Hipchat message enabled',
          'value' => qa_opt('hipchat_notifications_notify_enabled') ? true : false,
          'tags' => 'NAME="hipchat_notifications_notify_enabled"',
        ),

        array(
          'label' => 'HipChat API Token',
          'value' => qa_opt('hipchat_notifications_api_token'),
          'tags' => 'NAME="hipchat_notifications_api_token"',
        ),

        array(
          'label' => 'Room Name',
          'value' => qa_opt('hipchat_notifications_room_name'),
          'tags' => 'NAME="hipchat_notifications_room_name"',
        ),

        array(
          'label' => 'Sender Name (default: Question2Answer)',
          'value' => qa_opt('hipchat_notifications_sender'),
          'tags' => 'NAME="hipchat_notifications_sender"',
        ),

        array(
          'label' => 'Message Color (default: yellow)',
          'value' => qa_opt('hipchat_notifications_color'),
          'tags' => 'NAME="hipchat_notifications_color"',
        ),

        array(
          'type' => 'checkbox',
          'label' => 'Notify the people in the room (Sound or Alert)',
          'value' => qa_opt('hipchat_notifications_notify') ? true : false,
          'tags' => 'NAME="hipchat_notifications_notify"',
        ),

        array(
          'type' => 'checkbox',
          'label' => 'MS Teams message enabled',
          'value' => qa_opt('ms_notifications_notify_enabled') ? true : false,
          'tags' => 'NAME="ms_notifications_notify_enabled"',
        ),
        
        array(
          'label' => 'MS Teams Webhook Url',
          'value' => qa_opt('ms_notifications_webhook_url'),
          'tags' => 'NAME="ms_notifications_webhook_url"',
        ),

        array(
          'label' => 'MS Teams Webhook Title',
          'value' => qa_opt('ms_notifications_webhook_title'),
          'tags' => 'NAME="ms_notifications_webhook_title"',
        ),

      ),

      'buttons' => array(
        array(
          'label' => 'Save Changes',
          'tags' => 'NAME="general_save_button"',
        ),
      )
    );

    return $form;

  }

}
