<?php
class ChatWidget
{
  public static function add_hooks()
  {
    add_action('plugins_loaded', array(__CLASS__, 'load_textdomain'));
    wp_enqueue_style("style", plugins_url("/assets/style.css", __FILE__));
  }

  public static function load_textdomain()
  {
    load_plugin_textdomain('buildship');
  }

  public static function sanitize_settings($values)
  {
    if (!is_array($values)) {
      $values = array();
    }
    $data = array();
    $defaults = self::settings_default();
    foreach ($defaults as $key => $default) {
      if (!array_key_exists($key, $values)) {
        $data[$key] = $default;
      } else {
        $data[$key] = $values[$key];
      }
    }

    return $data;
  }

  public static function settings_default()
  {
    $defaults = array(
      // basic settings
      'endpoint_url' => '',
      'widget_title' => 'Buildship Chat Widget',
      'greeting_message' => 'Hello, how can I help you?',
      'button_name' => 'Beep Boop',
      // advanced settings
      "response_is_a_stream" => 0,
      "close_on_outside_click" => 0,
      "open_on_load" => 0,
      "disable_error_alert" => 0,
    );

    return $defaults;
  }

  public static function get_settings()
  {
    $settings = get_option(CHAT_WIDGET_SETTINGS, self::settings_default());

    return apply_filters('chat_widget_settings', $settings);
  }

  public static function save_settings(array $update)
  {
    $update = apply_filters('chat_widget_pre_save_settings', $update);

    return update_option(CHAT_WIDGET_SETTINGS, $update);
  }

}