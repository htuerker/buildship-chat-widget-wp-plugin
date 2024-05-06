<?php
class ChatWidgetAdmin
{

  public function add_hooks()
  {
    add_action('admin_menu', array($this, 'add_page'));
    add_action('admin_init', array($this, 'register_settings'));
  }

  public static function register_settings()
  {
    $args = array(
      'type' => 'array',
      'sanitize_callback' => array(ChatWidget::class, 'sanitize_settings'),
      'default' => ChatWidget::settings_default(),
    );
    register_setting(
      CHAT_WIDGET_SCREEN,
      CHAT_WIDGET_SETTINGS,
      $args
    );
  }
  public function add_page()
  {
    $this->register_fields();

    $hook_suffix = add_options_page(
      __('BuildShip Chat Widget', 'chat-widget'),
      __('Settings', 'chat-widget'),
      'manage_options',
      CHAT_WIDGET_SCREEN,
      array($this, 'render_page')
    );

    add_action(
      'plugin_action_links_' . plugin_basename(CHAT_WIDGET_MAIN_FILE),
      array(
        $this,
        'plugin_action_links_add_settings',
      )
    );

    return $hook_suffix;
  }

  protected function sections()
  {
    return array(
      'basic-options' => array(
        'name' => __('Settings', 'chat-widget'),
        'settings' => array(
          'endpoint_url' => array(
            'label' => __('Endpoint URL', 'chat-widget'),
            'type' => 'text',
          ),
          'widget_title' => array(
            'label' => __('Widget Title', 'chat-widget'),
            'type' => 'text',
          ),
          'greeting_message' => array(
            'label' => __('Greeting Message', 'chat-widget'),
            'type' => 'text',
          ),
          'button_name' => array(
            'label' => __('Button Name', 'chat-widget'),
            'type' => 'text',
          ),
        )
      ),
      'advanced-options' => array(
        'name' => __('Advanced Options', 'chat-widget'),
        'settings' => array(
          'response_is_a_stream' => array(
            'label' => __('Response is a Stream', 'chat-widget'),
            'type' => 'checkbox',
          ),
          'close_on_outside_click' => array(
            'label' => __('Close on Outside Click', 'chat-widget'),
            'type' => 'checkbox',
          ),
          'open_on_load' => array(
            'label' => __('Open on Load', 'chat-widget'),
            'type' => 'checkbox',
          ),
          'disable_error_alert' => array(
            'label' => __('Disable Error Alert', 'chat-widget'),
            'type' => 'checkbox',
          ),
        ),
      ),

    );
  }

  public function register_fields()
  {
    $sections = $this->sections();
    $saved = ChatWidget::get_settings();

    foreach ($sections as $section_slug => $section_data) {
      add_settings_section(
        $section_slug,
        $section_data['name'],
        null,
        CHAT_WIDGET_SCREEN
      );
      foreach ($section_data['settings'] as $id => $setting) {
        $value = isset($saved[$id]) ? $saved[$id] : null;
        add_settings_field(
          $id,
          $setting['label'],
          function () use ($setting, $id, $value) {
            $this->render_field(
              $setting['label'],
              $id,
              $value,
              isset ($setting['required']) ? $setting['required'] : false,
              $setting['type'],
            );
          },
          CHAT_WIDGET_SCREEN,
          $section_slug
        );
      }
    }

  }

  public function render_field($label, $name, $value = null, $required = false, $type = 'text')
  {
    $name = CHAT_WIDGET_SETTINGS . '[' . $name . ']';
    $field = sprintf(
      '<label for="%s" %s>%s<input type="%s" name="%s" %s />%s</label>',
      esc_attr($name),
      $type == 'checkbox' ? 'class="switch"' : '',
      $type == 'checkbox' ? '' : esc_html($label),
      esc_attr($type),
      esc_attr($name),
      sprintf(
        ' %s %s %s',
        $required ? 'required' : '',
        $type == 'checkbox' && $value ? 'checked' : '',
        $value ? 'value="' . esc_attr($type == 'checkbox' ? 1 : $value) . '"' : esc_attr($type == 'checkbox' ? 0 : '') . '"',
      ),
      $type == 'checkbox' ? '<div></div><span class="switch-bg"></span>' : '',
    );

    $wrapper = sprintf(
      '<div class="field-wrapper">%s</div>',
      $field
    );
    echo $wrapper;
  }

  public function render_page()
  {
    ?>
    <div class="buildship-chat-widget-wrap">
      <div class="buildship-chat-widget-header">
        <a href="https://buildship.com/" target="_blank">
          <img src="<?php echo plugins_url("/assets/images/buildship.png", __FILE__); ?>" />
        </a>
      </div>
      <h1><?php esc_html_e('BuildShip AI Chat Widget', 'buildship-chat-widget'); ?></h1>
      <div class="buildship-chat-widget-links">
        <a href="https://buildship.com/" target="_blank">BuildShip</a>
        <a href="https://github.com/rowyio/buildship-chat-widget/tree/main" target="_blank">Documentation</a>
        <a href="https://buildship.com/chat-widget/city-advisor" target="_blank">Live Demo</a>
      </div>
      <form action="options.php" method="post" novalidate="novalidate">
        <?php settings_fields(CHAT_WIDGET_SCREEN); ?>
        <?php do_settings_sections(CHAT_WIDGET_SCREEN); ?>
        <?php submit_button(); ?>
      </form>
    </div>
    <?php
  }

  function plugin_action_links_add_settings($links)
  {
    $settings_link = sprintf(
      '<a href="%s">%s</a>',
      esc_url(add_query_arg('page', CHAT_WIDGET_SCREEN, admin_url('options-general.php'))),
      esc_html__('Settings', 'buildship-chat-widget')
    );
    array_unshift($links, $settings_link);

    return $links;
  }


}