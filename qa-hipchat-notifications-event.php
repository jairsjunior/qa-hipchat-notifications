<?php

/*
  HipChat Notifications

  File: qa-plugin/hipchat-notifications/qa-hipchat-notifications-event.php
  Version: 0.1
  Date: 2014-02-25
  Description: Event module class for HipChat notifications plugin
*/

require_once QA_INCLUDE_DIR.'qa-app-posts.php';

class qa_hipchat_notifications_event {

  private $plugindir;

  function load_module($directory, $urltoroot)
  {
    $this->plugindir = $directory;
  }

  public function process_event($event, $userid, $handle, $cookieid, $params)
  {
    switch ($event) {
      case 'q_post':
        if (qa_opt('hipchat_notifications_notify_enabled') > 0 ? true : false) {
          $this->send_hipchat_notification(
            $this->build_new_question_message_hipchat(
              isset($handle) ? $handle : qa_lang('main/anonymous'),
              $params['title'],
              qa_q_path($params['postid'], $params['title'], true)
            )
          );
        }
        if (qa_opt('ms_notifications_notify_enabled') > 0 ? true : false) {
          $this->send_msteams_notification(
            $this->build_new_question_message_msteams(
              isset($handle) ? $handle : qa_lang('main/anonymous'),
              $params['title']
            ),
            qa_q_path($params['postid'], $params['title'], true)
          );
        }
        if (qa_opt('telegram_notifications_notify_enabled') > 0 ? true : false) {
          $this->send_telegram_notification(
            $this->build_new_question_message_telegram(
              isset($handle) ? $handle : qa_lang('main/anonymous'),
              $params['title'],
              qa_q_path($params['postid'], $params['title'], true)
            ),
            qa_q_path($params['postid'], $params['title'], true)
          );
        }
        break;
      case 'a_post':
        $parentpost=qa_post_get_full($params['parentid']);
        if (qa_opt('hipchat_notifications_notify_enabled') > 0 ? true : false) {
          $this->send_hipchat_notification(
            $this->build_new_answer_message_hipchat(
              isset($handle) ? $handle : qa_lang('main/anonymous'),
              $parentpost['title'],
              qa_path(qa_q_request($params['parentid'], $parentpost['title']), null, qa_opt('site_url'), null, qa_anchor('A', $params['postid']))
            )
          );
        }
        if (qa_opt('ms_notifications_notify_enabled') > 0 ? true : false) {
          $this->send_msteams_notification(
            $this->build_new_answer_message_msteams(
              isset($handle) ? $handle : qa_lang('main/anonymous'),
              $parentpost['title']
            ),
            qa_path(qa_q_request($params['parentid'], $parentpost['title']), null, qa_opt('site_url'), null, qa_anchor('A', $params['postid']))
          );
        }
        if (qa_opt('telegram_notifications_notify_enabled') > 0 ? true : false) {
          $this->send_telegram_notification(
            $this->build_new_answer_message_telegram(
              isset($handle) ? $handle : qa_lang('main/anonymous'),
              $parentpost['title']
            ),
            qa_path(qa_q_request($params['parentid'], $parentpost['title']), null, qa_opt('site_url'), null, qa_anchor('A', $params['postid']))
          );
        }
        break;
    }
  }

  private function build_new_question_message_hipchat($who, $title, $url) {
    return sprintf("%s asked a new question: <a href=\"%s\">\"%s\"</a>. Do you know the answer?", $who, $url, $title);
  }

  private function build_new_question_message_msteams($who, $title) {
    return sprintf("%s asked: %s. Do you know the answer? Like this message before respond to everyone know that you get this!", $who, $title);
  }

  private function build_new_question_message_telegram($who, $title, $url) {
    return sprintf("<b>%s</b> asked: \n<a href=\"%s\">\"%s\"</a> \n\nDo you know the answer? \nReply with <b>i got it</b>, to everyone knows that you pick this question.", $who, $this->replaceHttp($url), $title);
  }

  private function build_new_answer_message_msteams($who, $title) {
    return sprintf("%s answered the question: %s", $who, $title);
  }

  private function build_new_answer_message_telegram($who, $title) {
    return sprintf("%s answered the question: %s", $who, $title);
  }

  private function build_new_answer_message_hipchat($who, $title, $url) {
    return sprintf("%s answered the question: <a href=\"%s\">\"%s\"</a>.", $who, $url, $title);
  }

  private function replaceHttp($url) {
    return str_replace("http:","https:",$url);
  }

  private function send_hipchat_notification($message) {
    require_once $this->plugindir . 'HipChat' . DIRECTORY_SEPARATOR . 'HipChat.php';

    $token = qa_opt('hipchat_notifications_api_token');
    $room = qa_opt('hipchat_notifications_room_name');
    $sender = qa_opt('hipchat_notifications_sender');
    $color = qa_opt('hipchat_notifications_color');
    $notify = qa_opt('hipchat_notifications_notify') > 0 ? true : false;

    if ($sender == null || $sender == '')
      $sender = 'Question2Answer';

    if ($color == null || $color == '')
      $color = 'yellow';

    if ($token && $room) {
      $hc = new HipChat\HipChat($token);
      try{
        $result = $hc->message_room($room, $sender, $message, $notify, $color);
      }
      catch (HipChat\HipChat_Exception $e) {
        error_log($e->getMessage());
      }
    }
  }

  private function send_msteams_notification($message, $url) {
    require_once $this->plugindir . 'MSTeams' . DIRECTORY_SEPARATOR . 'MSTeams.php';

    $msUrl = qa_opt('ms_notifications_webhook_url');
    $title = qa_opt('ms_notifications_webhook_title');

    if ($msUrl) {
      $msTeams = new MSTeams\MSTeams($msUrl);
      try{
        $result = $msTeams->message_room($title, $message, $url);
      }
      catch (MSTeams\MSTeams_Exception $e) {
        error_log($e->getMessage());
      }
    }
  }

  private function send_telegram_notification($message, $url) {
    require_once $this->plugindir . 'Telegram' . DIRECTORY_SEPARATOR . 'Telegram.php';

    $telegramUrl = qa_opt('telegram_notifications_webhook_url');
    $chatId = qa_opt('telegram_notifications_webhook_chat_id');

    if ($telegramUrl) {
      $telegram = new Telegram\Telegram($telegramUrl, $chatId);
      try{
        $result = $telegram->message_room($message, $url);
      }
      catch (Telegram\Telegram_Exception $e) {
        error_log($e->getMessage());
      }
    }
  }
}
